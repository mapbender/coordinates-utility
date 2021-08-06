<?php

namespace Mapbender\CoordinatesUtilityBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Mapbender\CoreBundle\Entity\Element;
use Mapbender\CoreBundle\Entity\Application;

use Mapbender\CoordinatesUtilityBundle\Element\CoordinatesUtility;

class CoordinatesUtilityTest extends WebTestCase
{
    const SRS_NAME = "EPSG:4647";
    const SRS_TITLE = "ETRS89 / UTM zone N32";
    const SRS_DEFINITION = "+proj=tmerc +lat_0=0 +lon_0=9 +k=0.9996 +x_0=32500000 +y_0=0 +ellps=GRS80 +towgs84=0,0,0,0,0,0,0 +units=m +no_defs";

    /**
     * @var CoordinatesUtility
     */
    private $coordinatesUtility;
    /** @var Element */
    protected $element;

    public function setUp()
    {
        static::createClient();
        static::$kernel;

        $container = static::$kernel->getContainer();
        $this->element = new Element();
        $this->element->setApplication(new Application());

        $this->coordinatesUtility = new CoordinatesUtility($container->get('doctrine'));
    }

    /**
     * Test widget name
     */
    public function testGetWidgetName()
    {
        $actual = $this
            ->coordinatesUtility
            ->getWidgetName($this->element);

        $expected = "mapbender.mbCoordinatesUtility";

        $this->assertEquals($expected, $actual);
    }

    /**
     * Test srs definition
     */
    public function testAddSrsDefinitions()
    {
        $srsList = [
            [
                "name"  => self::SRS_NAME,
                "title" => self::SRS_TITLE,
            ]
        ];

        $actual = $this
            ->coordinatesUtility
            ->addSrsDefinitions($srsList);

        $expected = [
            [
                "name"  => self::SRS_NAME,
                "title" => self::SRS_TITLE,
                "definition" => self::SRS_DEFINITION,
            ]
        ];

        $this->assertEquals($expected, $actual);
    }
}
