# Бэкенд проекта "умные ссылки"

## Расширение функционала путем добавления плагинов

Для добавления нового условия (не обязательно условия, в принципе также вы можете реализовать механизм каких-либо действий, например отправки нужных вам метрик), достаточно реализовать свою схему шага роутинга и стратегию для его обработки.

Схема описывает то, какие поля должна содержать настройка шага. Это должен быть класс имплементирующий **App\Application\Scheme\RoutingStepSchemeInterface**, а также помеченный атрибутом **App\Application\Attribute\RoutingStepScheme** содержащим тип шага (из коробки поддерживаются типы "условие" и "редирект"), а также алиас используемый для именования данной схемы. 

Вот пример схемы, которую вы бы могли захотеть добавить в проект для определения, содержит ли запрос пользователя определенный заголовок.

```php

<?php

declare(strict_types=1);

namespace Plugin\HasHeader;

use App\Application\Attribute\RoutingStepScheme;
use App\Application\Scheme\RoutingStepSchemeInterface;
use App\Domain\Enum\RoutingStepType;

#[RoutingStepScheme(type: RoutingStepType::CONDITION->value, alias: 'has_header')]
final class HasHeaderScheme implements RoutingStepSchemeInterface
{
    public string $headerName;
}


```

Далее вам следует добавить стратегию, которая будет обрабатывать шаг роутинга, содержащий вашу схему. Она должна имплементировать **App\Application\Service\Strategy\RoutingStepStrategyInterface** и быть помеченной атрибутом **App\Application\Attribute\SupportedRoutingStepScheme** с указанием в последнем того класса схемы, которую ваша стратегия обрабатывает.

Рекомендуется не писать с нуля класс, а расширить один из существующих, например **App\Infrastructure\Service\Strategy\ConditionCheckerStrategy**. Если вы хотите создавать блоки, которые не являются ни условиями, ни редиректами (например хотите писать метрики), расширьте базовый класс **App\Infrastructure\Service\Strategy\RoutingStepStrategy**.

Вот пример стратегии для работы с приведенной выше кастомной схемой.

```php

<?php

declare(strict_types=1);

namespace Plugin\HasHeader;

use App\Application\Attribute\SupportedRoutingStepScheme;
use App\Application\Dto\HttpRequestDto;
use App\Application\Scheme\RoutingStepSchemeInterface;
use App\Application\Service\Routing\RedirectionContextInterface;
use App\Infrastructure\Service\Strategy\ConditionCheckerStrategy;

#[SupportedRoutingStepScheme(class: HasHeaderScheme::class)]
final class HasHeaderCheckerStrategy extends ConditionCheckerStrategy
{
    /**
     * @param HasHeaderScheme $routingStepScheme
     */
    protected function meetsCondtion(
        RoutingStepSchemeInterface $routingStepScheme,
        HttpRequestDto $httpRequestDto,
        RedirectionContextInterface $context,
    ): bool {
        return isset($httpRequestDto->headers[$routingStepScheme->headerName]);
    }

    protected function isRouteStepSchemeSupported(RoutingStepSchemeInterface $routingStepScheme): bool
    {
        return $routingStepScheme instanceof HasHeaderScheme;
    }    
}


```

Подобных двух классов достаточно, чтобы реализовать свой плагин. Поместите свои файлы в директорию **plugin** проекта и они автоматически будут подключены.