<?php

namespace Mapbender\CoordinatesUtilityBundle\Element;

use Mapbender\CoreBundle\Component\Element;
use Mapbender\CoreBundle\Entity\SRS;
use Symfony\Bridge\Doctrine\RegistryInterface;

class CoordinatesUtility extends Element
{
    /**
     * @inheritdoc
     */
    public static function getClassTitle()
    {
        return "mb.coordinatesutility.class.title";
    }

    /**
     * @inheritdoc
     */
    public static function getClassDescription()
    {
        return "mb.coordinatesutility.class.description";
    }

    /**
     * @inheritdoc
     */
    public function getAssets()
    {
        return [
            'js' => [
                '@MapbenderCoordinatesUtilityBundle/Resources/public/mapbender.element.coordinatesutility.js',
            ],
            'css' => [
                '@MapbenderCoordinatesUtilityBundle/Resources/public/sass/element/coordinatesutility.scss'
            ],
            'trans' => [
                'mb.coordinatesutility.widget.*',
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getDefaultConfiguration()
    {
        return [
            'target'    => null,
            'srsList'   => '',
            'addMapSrsList' => true,
            'zoomlevel' => 6,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getWidgetName()
    {
        return 'mapbender.mbCoordinatesUtility';
    }

    /**
     * @inheritdoc
     */
    public static function getType()
    {
        return 'Mapbender\CoordinatesUtilityBundle\Element\Type\CoordinatesUtilityAdminType';
    }

    /**
     * @inheritdoc
     */
    public static function getFormTemplate()
    {
        return 'MapbenderCoordinatesUtilityBundle:ElementAdmin:coordinatesutility.html.twig';
    }

    /**
     * @inheritdoc
     */
    public function getFrontendTemplatePath($suffix = '.html.twig')
    {
        return 'MapbenderCoordinatesUtilityBundle:Element:coordinatesutility.html.twig';
    }

    public function getPublicConfiguration()
    {
        $conf = $this->entity->getConfiguration() ?: array();

        if (!empty($conf['srsList'])) {
            $conf['srsList'] = $this->addSrsDefinitions($conf['srsList']);
        }

        if (!isset($conf['zoomlevel'])) {
            $conf['zoomlevel'] = CoordinatesUtility::getDefaultConfiguration()['zoomlevel'];
        }
        // Coords utility doesn't have an autoOpen backend option, and doesn't support it in the frontend
        // However, some legacy / cloned / YAML-based etc Applications may have a value there that will
        // royally confuse controlling buttons. Just make sure it's never there.
        unset($conf['autoOpen']);

        return $conf;
    }

    /**
     * @param $srsList
     * @return mixed
     */
    public function addSrsDefinitions($srsList)
    {
        $srsList = $this->normalizeSrsList($srsList);
        $srsWithDefinitions = $this->getSrsDefinitionsFromDatabase($srsList);

        foreach ($srsList as $key => $srsSpec) {
            $srsName = $srsSpec['name'];

            if (isset($srsWithDefinitions[$srsName])) {
                $srs = $srsWithDefinitions[$srsName];
                $srsList[$key]['definition'] = $srs->getDefinition();
                if (empty($srsList[$key]['title'])) {
                    $srsList[$key]['title'] = $srs->getTitle() ?: $srs->getName();
                }
            } elseif (empty($srsList[$key]['title'])) {
                $srsList[$key]['title'] = $srsList[$key]['name'];
            }
        }

        return $srsList;
    }

    /**
     * @param mixed[] $srsList strings or arrays
     * @return mixed[][]
     */
    protected function normalizeSrsList($srsList)
    {
        // Tolerate both arrays + scalars
        /** @see Type\CoordinatesUtilityAdminType::reverseTransform */
        foreach ($srsList as $k => $srsSpec) {
            if (\is_string($srsSpec)) {
                $parts = explode('|', $srsSpec, 2);
                $name = trim($parts[0]);
                $title = (count($parts) > 1) ? $parts[1] : null;
            } else {
                $name = $srsSpec['name'];
                $title = !empty($srsSpec['title']) ? $srsSpec['title'] : null;
            }
            $srsList[$k] = array(
                'name' => $name,
                'title' => trim($title) ?: null,
            );
        }
        return $srsList;
    }

    /**
     * @param $srsList
     * @return SRS[] keyed on name
     */
    public function getSrsDefinitionsFromDatabase($srsList)
    {
        $srsNames = array_map(function($srs) {
            return $srs['name'];
        }, $srsList);
        /** @var RegistryInterface $doctrine */
        $doctrine = $this->container->get('doctrine');
        /** @var SRS[] $entities */
        $entities = $doctrine->getRepository(SRS::class)->findBy(array(
            'name' => $srsNames,
        ));
        $entityMap = array();
        foreach ($entities as $srs) {
            $entityMap[$srs->getName()] = $srs;
        }
        return $entityMap;
    }
}
