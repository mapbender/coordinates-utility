<?php

namespace Mapbender\CoordinatesUtilityBundle\Tests;

use Mapbender\CoordinatesUtilityBundle\Element\Type\SrsListType;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Form\DataTransformerInterface;

class CoordinatesUtilitySubscriberTest extends WebTestCase
{
    /**
     * @var DataTransformerInterface
     */
    private $transformer;

    public function setUp()
    {
        $this->transformer = new SrsListType();
    }

    /**
     * Test default configuration
     */
    public function testFormatSrsOutput()
    {
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
        $actual = $this->transformer->transform($srsListData);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Test widget name
     */
    public function testRetrieveUniqueSRSNamesAndTitles()
    {
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
        $actual = $this->transformer->reverseTransform($srsListData);
        $this->assertEquals($expected, $actual);
    }
}

