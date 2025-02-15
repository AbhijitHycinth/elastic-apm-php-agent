<?php


namespace PhilKra\Tests\Traces;


use PhilKra\Traces\Span;
use PhilKra\Traces\Transaction;
use PHPUnit\Framework\TestCase;

class TransactionTest extends TestCase
{
    /** @var  Transaction */
    private $transaction;
    public function setUp()
    {
        parent::setUp();
        mt_srand(123456);
        $this->transaction = new Transaction('testName', 'testType');
    }

    public function testNameNadType() {
        $this->assertSame('testName', $this->transaction->getName());
        $this->transaction->setName('testName2');
        $this->assertSame('testName2', $this->transaction->getName());

        $this->assertSame('testType', $this->transaction->getType());
        $this->transaction->setType('testType2');
        $this->assertSame('testType2', $this->transaction->getType());
    }

    public function testChangeTranactionId() {
        $span = $this->createMock(Span::class);
        $span->expects(self::once())->method('setParentId')->with('transactionId');
        $span->expects(self::once())->method('setTransaction')->with($this->transaction);

        $this->transaction->addSpan($span);

        $this->transaction->setId('transactionId');
    }

    public function testSetResults() {
        $result = 'testResult';
        $this->transaction->setResult($result);
        $reflection = new \ReflectionClass($this->transaction);
        $reflectionProperty = $reflection->getProperty('result');
        $reflectionProperty->setAccessible(true);
        $this->assertSame($result, $reflectionProperty->getValue($this->transaction));
    }

    public function testSerialize() {
        $this->transaction->setTraceId($this->transaction->generateTraceId());
        $this->assertSame([
            'transaction' => [
                'id' => '10a9',
                'trace_id' => '1e7d69ea',
                'name' => 'testName',
                'type' => 'testType',
                'timestamp' => null,
                'duration' => null,
                'sampled' => true,
                'span_count' => [
                    'started' => 0,
                    'dopped' => 0
                ]
            ]
        ], $this->transaction->jsonSerialize());
    }
}