<?php

namespace Dhii\Stats\FuncTest;

/**
 * Tests {@see \Dhii\Stats\AbstractAggregator}.
 *
 * @since 0.1.0
 */
class AbstractAggregatorTest extends \Xpmock\TestCase
{
    /**
     * @since 0.1.0
     *
     * @return \Dhii\Stats\AbstractAggregator
     */
    public function createInstance()
    {
        $mock = $this->mock('Dhii\\Stats\\AbstractAggregator')
                ->_getCalculators(array(
                    'count'         => function($totals, $code, $source) {
                        return $totals[$code] + 1;
                    }
                ))
                ->new();

        return $mock;
    }

    /**
     * Tests that the aggregation of stats happens according to defined rules.
     *
     * @since 0.1.0
     */
    public function testAggregation()
    {
        $subject = $this->createInstance();
        $data = array(
            'apple',
            'banana',
            'orange'
        );

        $result = $subject->aggregate(array('count'), $data);

        $this->assertEquals(array('count' => 3), $result, 'Aggregated totals are incorrect');
    }
}
