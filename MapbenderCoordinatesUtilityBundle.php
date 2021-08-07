<?php

namespace Mapbender\CoordinatesUtilityBundle;

use Mapbender\CoreBundle\Component\MapbenderBundle;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class MapbenderCoordinatesUtilityBundle extends MapbenderBundle
{
    /**
     * @inheritdoc
     */
    public function getElements()
    {
        return [
            "Mapbender\CoordinatesUtilityBundle\Element\CoordinatesUtility",
        ];
    }

    public function build(ContainerBuilder $container)
    {
        $configLocator = new FileLocator(__DIR__ . '/Resources/config');
        $loader = new XmlFileLoader($container, $configLocator);
        $loader->load('services.xml');
        $container->addResource(new FileResource($loader->getLocator()->locate('services.xml')));
        $loader->load('controllers.xml');
        $container->addResource(new FileResource($loader->getLocator()->locate('controllers.xml')));
    }
}
