<?php

namespace App;

use App\Application\Attribute\RoutingStepScheme;
use App\Application\Attribute\SupportedRoutingStepScheme;
use App\Infrastructure\DependencyInjection\Compiler\RoutingStepClassCompilerPass;
use App\Infrastructure\DependencyInjection\Compiler\RoutingStepSchemeCompilerPass;
use App\Infrastructure\DependencyInjection\Compiler\RoutingStepStrategiesCompilerPass;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\Preloader;
use Symfony\Component\ErrorHandler\DebugClassLoader;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\CacheWarmer\WarmableInterface;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use Throwable;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    private ?string $warmupDir = null;

    /**
     * @var array<string, bool>
     */
    private static array $freshCache = [];

    public function reboot(?string $warmupDir): void
    {
        $this->warmupDir = $warmupDir;

        parent::reboot($warmupDir);
    }

    protected function build(ContainerBuilder $container): void
    {
        /* @phpstan-ignore argument.type */
        $container->registerAttributeForAutoconfiguration(RoutingStepScheme::class, static function (ChildDefinition $definition, RoutingStepScheme $attribute, ReflectionClass $reflector): void {
            $definition->addTag(RoutingStepSchemeCompilerPass::ROUTING_STEP_SCHEME_TAG, [
                'type' => $attribute->type,
                'alias' => $attribute->alias,
            ]);
        });

        /* @phpstan-ignore argument.type */
        $container->registerAttributeForAutoconfiguration(SupportedRoutingStepScheme::class, static function (ChildDefinition $definition, SupportedRoutingStepScheme $attribute, ReflectionClass $reflector): void {
            $definition->addTag(RoutingStepStrategiesCompilerPass::STRATEGIES_SUPPORTED_SCHEMES_TAG, [
                'class' => $attribute->class,
            ]);
        });

        $container->addCompilerPass(new RoutingStepClassCompilerPass());
        $container->addCompilerPass(new RoutingStepSchemeCompilerPass());
        $container->addCompilerPass(new RoutingStepStrategiesCompilerPass());
    }

    protected function initializeContainer(): void
    {
        $class = $this->getContainerClass();
        $buildDir = $this->warmupDir ?: $this->getBuildDir();
        $skip = $_SERVER['SYMFONY_DISABLE_RESOURCE_TRACKING'] ?? '';
        $skip = filter_var($skip, \FILTER_VALIDATE_BOOLEAN, \FILTER_NULL_ON_FAILURE) ?? explode(',', $skip);
        $cache = new ConfigCache($buildDir . '/' . $class . '.php', $this->debug, null, \is_array($skip) && ['*'] !== $skip ? $skip : ($skip ? [] : null));

        $cachePath = $cache->getPath();

        $pluginsDirLastModified = $this->getPluginsDirLastModifiedTime();

        $pluginsDirLastModifiedFilePath = $buildDir . '/plugins_dir_last_modified';

        $prevPluginsDirLastModified = file_exists($pluginsDirLastModifiedFilePath) ? (int)file_get_contents($pluginsDirLastModifiedFilePath) : 0;

        $errorLevel = error_reporting(\E_ALL ^ \E_WARNING);

        try {
            if (is_file($cachePath) && \is_object($this->container = include $cachePath)
                && (!$this->debug || (self::$freshCache[$cachePath] ?? $cache->isFresh())) && $pluginsDirLastModified <= $prevPluginsDirLastModified
            ) {
                self::$freshCache[$cachePath] = true;
                $this->container->set('kernel', $this);
                error_reporting($errorLevel);

                return;
            }
        } catch (Throwable $e) {
        }

        file_put_contents($pluginsDirLastModifiedFilePath, $pluginsDirLastModified);

        $oldContainer = \is_object($this->container) ? new ReflectionClass($this->container) : $this->container = null;

        try {
            is_dir($buildDir) ?: mkdir($buildDir, 0777, true);

            if ($lock = fopen($cachePath . '.lock', 'w+')) {
                if (!flock($lock, \LOCK_EX | \LOCK_NB, $wouldBlock) && !flock($lock, $wouldBlock ? \LOCK_SH : \LOCK_EX)) {
                    fclose($lock);
                    $lock = null;
                } elseif (!is_file($cachePath) || !\is_object($this->container = include $cachePath)) {
                    $this->container = null;
                } elseif (!$oldContainer || $this->container::class !== $oldContainer->name) {
                    flock($lock, \LOCK_UN);
                    fclose($lock);
                    $this->container->set('kernel', $this);

                    return;
                }
            }
        } catch (Throwable $e) {
        } finally {
            error_reporting($errorLevel);
        }

        if ($collectDeprecations = $this->debug && !\defined('PHPUNIT_COMPOSER_INSTALL')) {
            $collectedLogs = [];
            $previousHandler = set_error_handler(function ($type, $message, $file, $line) use (&$collectedLogs, &$previousHandler) {
                if (\E_USER_DEPRECATED !== $type && \E_DEPRECATED !== $type) {
                    return $previousHandler ? $previousHandler($type, $message, $file, $line) : false;
                }

                if (isset($collectedLogs[$message])) {
                    ++$collectedLogs[$message]['count'];

                    return null;
                }

                $backtrace = debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS, 5);

                for ($i = 0; isset($backtrace[$i]); ++$i) {
                    if (isset($backtrace[$i]['file'], $backtrace[$i]['line']) && $backtrace[$i]['line'] === $line && $backtrace[$i]['file'] === $file) {
                        $backtrace = \array_slice($backtrace, 1 + $i);
                        break;
                    }
                }

                for ($i = 0; isset($backtrace[$i]); ++$i) {
                    if (!isset($backtrace[$i]['file'], $backtrace[$i]['line'], $backtrace[$i]['function'])) {
                        continue;
                    }

                    if (!isset($backtrace[$i]['class']) && 'trigger_deprecation' === $backtrace[$i]['function']) {
                        $file = $backtrace[$i]['file'];
                        $line = $backtrace[$i]['line'];
                        $backtrace = \array_slice($backtrace, 1 + $i);
                        break;
                    }
                }

                for ($i = \count($backtrace) - 2; 0 < $i; --$i) {
                    if (DebugClassLoader::class === ($backtrace[$i]['class'] ?? null)) {
                        $backtrace = [$backtrace[$i + 1]];
                        break;
                    }
                }

                $collectedLogs[$message] = [
                    'type' => $type,
                    'message' => $message,
                    'file' => $file,
                    'line' => $line,
                    'trace' => [$backtrace[0]],
                    'count' => 1,
                ];

                return null;
            });
        }

        try {
            $container = null;
            $container = $this->buildContainer();
            $container->compile();
        } finally {
            if ($collectDeprecations) {
                restore_error_handler();

                @file_put_contents($buildDir . '/' . $class . 'Deprecations.log', serialize(array_values($collectedLogs)));
                @file_put_contents($buildDir . '/' . $class . 'Compiler.log', null !== $container ? implode("\n", $container->getCompiler()->getLog()) : '');
            }
        }

        $this->dumpContainer($cache, $container, $class, $this->getContainerBaseClass());

        if (isset($lock) && $lock) {
            flock($lock, \LOCK_UN);
            fclose($lock);
        }

        $this->container = require $cachePath;
        $this->container->set('kernel', $this);

        if ($oldContainer && $this->container::class !== $oldContainer->name) {
            static $legacyContainers = [];
            $oldContainerDir = \dirname($oldContainer->getFileName());
            $legacyContainers[$oldContainerDir . '.legacy'] = true;

            foreach (glob(\dirname($oldContainerDir) . \DIRECTORY_SEPARATOR . '*.legacy', \GLOB_NOSORT) as $legacyContainer) {
                if (!isset($legacyContainers[$legacyContainer]) && @unlink($legacyContainer)) {
                    (new Filesystem())->remove(substr($legacyContainer, 0, -7));
                }
            }

            touch($oldContainerDir . '.legacy');
        }

        $buildDir = $this->container->getParameter('kernel.build_dir');
        $cacheDir = $this->container->getParameter('kernel.cache_dir');
        $preload = $this instanceof WarmableInterface ? $this->warmUp($cacheDir, $buildDir) : [];

        if ($this->container->has('cache_warmer')) {
            $cacheWarmer = $this->container->get('cache_warmer');

            if ($cacheDir !== $buildDir) {
                $cacheWarmer->enableOptionalWarmers();
            }

            $preload = array_merge($preload, $cacheWarmer->warmUp($cacheDir, $buildDir));
        }

        if ($preload && file_exists($preloadFile = $buildDir . '/' . $class . '.preload.php')) {
            Preloader::append($preloadFile, $preload);
        }
    }

    private function getPluginsDirLastModifiedTime(): int
    {
        $lastModified = filemtime($this->getPluginsDir());

        $directory = $this->getPluginsDir();

        if (is_dir($directory)) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($iterator as $file) {
                $fileModified = $file->getMTime();

                if ($fileModified > $lastModified) {
                    $lastModified = $fileModified;
                }
            }
        }

        return $lastModified;
    }

    private function getPluginsDir(): string
    {
        return $this->getProjectDir() . '/plugin';
    }
}
