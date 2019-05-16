<?php

namespace Tests\Resiliency;

use PHPUnit\Framework\TestCase;
use Resiliency\Contracts\CircuitBreaker;
use Resiliency\SimpleCircuitBreakerFactory;

class SimpleCircuitBreakerFactoryTest extends TestCase
{
    /**
     * @return void
     */
    public function testCreation(): void
    {
        $factory = new SimpleCircuitBreakerFactory();

        $this->assertInstanceOf(SimpleCircuitBreakerFactory::class, $factory);
    }

    /**
     * @depends testCreation
     * @dataProvider getSettings
     *
     * @param array $settings the Circuit Breaker settings
     *
     * @return void
     */
    public function testCircuitBreakerCreation(array $settings): void
    {
        $factory = new SimpleCircuitBreakerFactory();
        $circuitBreaker = $factory->create($settings);

        $this->assertInstanceOf(CircuitBreaker::class, $circuitBreaker);
    }

    /**
     * @return array
     */
    public function getSettings(): array
    {
        return [
            [
                [
                    'closed' => [2, 0.1, 0],
                    'open' => [0, 0, 10],
                    'half_open' => [1, 0.2, 0],
                ],
            ],
            [
                [
                    'closed' => [2, 0.1, 0],
                    'open' => [0, 0, 10],
                    'half_open' => [1, 0.2, 0],
                    'client' => ['proxy' => '192.168.16.1:10'],
                ],
            ],
        ];
    }
}
