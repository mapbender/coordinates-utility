<?php

namespace Mapbender\CoordinatesUtilityBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Form\FormEvents;

use Mapbender\CoordinatesUtilityBundle\Element\EventListener\CoordinatesUtilitySubscriber;

class CoordinatesUtilitySubscriberTest extends WebTestCase
{
    /**
     * @var CoordinatesUtilitySubscriber
     */
    private $coordinatesUtilitySubscriber;

    public function setUp()
    {
        $this->coordinatesUtilitySubscriber = new CoordinatesUtilitySubscriber();
    }

    /**
     * Test get subscribed events
     */
    public function testGetSubscribedEvents()
    {
        $actual = $this
            ->coordinatesUtilitySubscriber
            ->getSubscribedEvents();

        $expected = [
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT   => 'preSubmit',
        ];

        $this->assertEquals($expected, $actual);
    }

    /**
     * Test default configuration
     */
    public function testFormatSrsOutput()
    {
        $formatSrsOutput = self::getMethod('formatSrsOutput');

        $srsListData = [
            [
                'name'  => 'SRS 1',
                'title' => 'Great title',
            ],
            [
                'name'  => 'SRS 2',
                'title' => '',
            ]
        ];
        $expected = "SRS 1 | Great title, SRS 2";
        $actual = $formatSrsOutput
            ->invokeArgs($this->coordinatesUtilitySubscriber, [
                $srsListData
            ]);

        $this->assertEquals($expected, $actual);
    }

    /**
     * Test widget name
     */
    public function testRetrieveUniqueSRSNamesAndTitles()
    {
        $retrieveUniqueSRSNamesAndTitles = self::getMethod('retrieveUniqueSRSNamesAndTitles');

        $srsListData = "SRS 1 | Great title, SRS 2";
        $expected = [
            [
                'name' => 'SRS 1',
                'title' => 'Great title',
            ],
            [
                'name' => 'SRS 2',
                'title' => '',
            ]
        ];
        $actual = $retrieveUniqueSRSNamesAndTitles
            ->invokeArgs($this->coordinatesUtilitySubscriber, [
                $srsListData
            ]);

        $this->assertEquals($expected, $actual);
    }

    /**
     * Get reflection of private method
     *
     * @param $name
     * @return \ReflectionMethod
     */
    protected static function getMethod($name)
    {
        $class = new \ReflectionClass(CoordinatesUtilitySubscriber::class);

        $method = $class->getMethod($name);
        $method->setAccessible(true);

        return $method;
    }
}

