<?php

namespace Mapbender\CoordinatesUtilityBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

use Mapbender\CoordinatesUtilityBundle\MapbenderCoordinatesUtilityBundle;


class CoordinatesUtilityBundleTest extends TestCase
{
    /**
     * Test Bundle Elements
     */
    public function testBundleElements()
    {
        $bundle = new MapbenderCoordinatesUtilityBundle();

        $elementsActual = $bundle->getElements();

        $elementsExpected = [
            "Mapbender\CoordinatesUtilityBundle\Element\CoordinatesUtility",
        ];

        $this->assertEquals($elementsExpected, $elementsActual);
    }
}