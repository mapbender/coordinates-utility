<?php

namespace Mapbender\CoordinatesUtilityBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Mapbender\CoreBundle\Component\Application;
use Mapbender\CoreBundle\Entity\Element as Entity;
use Mapbender\CoreBundle\Entity\Application as ApplicationEntity;

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

    public function setUp()
    {
        $client = static::createClient();
        $kernel = static::$kernel;

        $container = static::$kernel->getContainer();

        $applicationEntity = new ApplicationEntity();
        $application = new Application($container, $applicationEntity, []);
        $entity = new Entity();

        $this->coordinatesUtility = new CoordinatesUtility($application, $container, $entity);
    }

    /**
     * Test list of assets
     */
    public function testListAssets()
    {
        $actual = $this
            ->coordinatesUtility
            ->listAssets();

        $expected = [
            'js' => [
                'mapbender.element.coordinatesutility.js',
            ],
            'css' => [
                'sass/element/coordinatesutility.scss'
            ],
            'trans' => [
                'MapbenderCoordinatesUtilityBundle:Element:coordinatesutility.json.twig'
            ]
        ];

        $this->assertEquals($expected, $actual);
    }

    /**
     * Test default configuration
     */
    public function testDefaultConfiguration()
    {
        $actual = $this
            ->coordinatesUtility
            ->getDefaultConfiguration();

        $expected = [
            'type'      => null,
            'target'    => null,
            'srsList'   => '',
            'addMapSrsList' => true,
        ];

        $this->assertEquals($expected, $actual);
    }

    /**
     * Test widget name
     */
    public function testGetWidgetName()
    {
        $actual = $this
            ->coordinatesUtility
            ->getWidgetName();

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