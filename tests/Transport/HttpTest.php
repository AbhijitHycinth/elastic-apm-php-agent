<?php


namespace PhilKra\Tests\Transport;


use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use PhilKra\Helper\Config;
use PhilKra\Stores\TracesStore;
use PhilKra\Transport\Curl;
use PhilKra\Transport\Http;
use PHPUnit\Framework\TestCase;

class HttpTest extends TestCase
{
    /** @var  Http */
    private $http;
    public function setUp() {
        $configMap = [
            ['secretToken', null, 'testToken'],
            ['transport.config', null, []]
        ];
        $config = $this->createMock(Config::class);
        $config->expects(self::atLeastOnce())->method('get')->willReturnMap($configMap);
        $this->http = new Http($config);
    }

    public function testSend() {
        $client = $this->createMock(Curl::class);
        $reflection = new \ReflectionClass($this->http);
        $reflectionProperty = $reflection->getProperty('curl');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($this->http, $client);

        $client->expects(self::exactly(8))->method('setOption');
        $client->expects(self::once())->method('execute');
        $client->expects(self::once())->method('close');

        $tracesStore = $this->createMock(TracesStore::class);
        $tracesStore->expects(self::once())->method('toNdJson')->willReturn('testData');

        $this->http->send($tracesStore);
    }
}