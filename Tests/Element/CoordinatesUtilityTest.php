<?php

namespace Mapbender\CoordinatesUtilityBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\Container;

use Mapbender\CoreBundle\Component\Application;
use Mapbender\CoreBundle\Entity\Element as Entity;
use Mapbender\CoreBundle\Entity\Application as ApplicationEntity;

use Mapbender\CoordinatesUtilityBundle\Element\CoordinatesUtility;

class CoordinatesUtilityTest extends WebTestCase
{
    /**
     * @var CoordinatesUtility
     */
    private $coordinatesUtility;

    public function setUp()
    {
        $container = new Container();
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
}