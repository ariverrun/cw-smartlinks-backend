<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Service\Detector;

use App\Application\Dto\GeoDto;
use App\Application\Exception\GeoDetectionFailedException;
use App\Infrastructure\Service\Detector\GeoIpDetectorApiAdapter;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class GeoIpDetectorApiAdapterTest extends TestCase
{
    public function testSuccessRequest(): void
    {
        $geoIpDetectorApiMock = $this->createMock(ClientInterface::class);

        $denormalizerMock = $this->createMock(DenormalizerInterface::class);

        $ipAddress = '126.42.128.62';

        $responseMock = $this->createMock(ResponseInterface::class);
        
        $responseMock->expects($this->once())
                        ->method('getStatusCode')
                        ->willReturn(200);

        $responseData = [
            'data' => [
                'continent' => 'Europe',
                'country' => 'France',
                'city' => 'Paris',
                'latitude' => 48.864716,
                'longitude' => 2.349014,
                'timeZone' => 'Europe/Paris',
            ],
        ];

        $responseBodyMock = $this->createMock(StreamInterface::class);
        $responseBodyMock->expects($this->once())
                        ->method('__toString')
                        ->willReturn(json_encode($responseData));

        $responseMock->expects($this->once())
                        ->method('getBody')
                        ->willReturn($responseBodyMock);

        $geoIpDetectorApiMock->expects($this->once())
                            ->method('sendRequest')
                            ->willReturnCallback(function($request) use($responseMock, $ipAddress): ResponseInterface {
                                $this->assertInstanceOf(Request::class, $request);
                                $this->assertEquals('GET', $request->getMethod());
                                $this->assertEquals('/api/v1/geo/?' . http_build_query(['ip' => $ipAddress]), $request->getUri());

                                return $responseMock;
                            });


        $denormalizerMock->expects($this->once())
                            ->method('denormalize')
                            ->willReturnCallback(function(array $geoData, string $geoDtoClass) use($responseData): GeoDto {
                                $this->assertEquals(GeoDto::class, $geoDtoClass);

                                $this->assertEqualsCanonicalizing($responseData['data'], $geoData);

                                return new GeoDto(
                                    $geoData['continent'],
                                    $geoData['country'],
                                    $geoData['city'],
                                    $geoData['latitude'],
                                    $geoData['longitude'],
                                    $geoData['timeZone'],
                                );
                            });

        $adapter = new GeoIpDetectorApiAdapter($geoIpDetectorApiMock, $denormalizerMock);

        $geoDto = $adapter->detectGeoByIp($ipAddress);

        $this->assertInstanceOf(GeoDto::class, $geoDto);
    }

    public function testFailWithUnexpectedStatusCode(): void
    {
        $geoIpDetectorApiMock = $this->createMock(ClientInterface::class);

        $denormalizerMock = $this->createMock(DenormalizerInterface::class);

        $ipAddress = '126.42.128.62';

        $responseMock = $this->createMock(ResponseInterface::class);
        
        $responseMock->expects($this->once())
                        ->method('getStatusCode')
                        ->willReturn(400);

        $responseData = [];

        $responseBodyMock = $this->createMock(StreamInterface::class);
        $responseBodyMock->expects($this->once())
                        ->method('__toString')
                        ->willReturn(json_encode($responseData));

        $responseMock->expects($this->once())
                        ->method('getBody')
                        ->willReturn($responseBodyMock);

        $geoIpDetectorApiMock->expects($this->once())
                            ->method('sendRequest')
                            ->willReturnCallback(function($request) use($responseMock, $ipAddress): ResponseInterface {
                                $this->assertInstanceOf(Request::class, $request);
                                $this->assertEquals('GET', $request->getMethod());
                                $this->assertEquals('/api/v1/geo/?' . http_build_query(['ip' => $ipAddress]), $request->getUri());

                                return $responseMock;
                            });


        $denormalizerMock->expects($this->never())
                            ->method('denormalize');

        $adapter = new GeoIpDetectorApiAdapter($geoIpDetectorApiMock, $denormalizerMock);

        $this->expectException(GeoDetectionFailedException::class);

        $adapter->detectGeoByIp($ipAddress);
    }

    public function testFailWithInvalidResponseData(): void
    {
        $geoIpDetectorApiMock = $this->createMock(ClientInterface::class);

        $denormalizerMock = $this->createMock(DenormalizerInterface::class);

        $ipAddress = '126.42.128.62';

        $responseMock = $this->createMock(ResponseInterface::class);
        
        $responseMock->expects($this->once())
                        ->method('getStatusCode')
                        ->willReturn(200);

        $responseData = [];

        $responseBodyMock = $this->createMock(StreamInterface::class);
        $responseBodyMock->expects($this->once())
                        ->method('__toString')
                        ->willReturn(json_encode($responseData));

        $responseMock->expects($this->once())
                        ->method('getBody')
                        ->willReturn($responseBodyMock);

        $geoIpDetectorApiMock->expects($this->once())
                            ->method('sendRequest')
                            ->willReturnCallback(function($request) use($responseMock, $ipAddress): ResponseInterface {
                                $this->assertInstanceOf(Request::class, $request);
                                $this->assertEquals('GET', $request->getMethod());
                                $this->assertEquals('/api/v1/geo/?' . http_build_query(['ip' => $ipAddress]), $request->getUri());

                                return $responseMock;
                            });


        $denormalizerMock->expects($this->never())
                            ->method('denormalize');

        $adapter = new GeoIpDetectorApiAdapter($geoIpDetectorApiMock, $denormalizerMock);

        $this->expectException(GeoDetectionFailedException::class);

        $adapter->detectGeoByIp($ipAddress);
    }
}