<?php

namespace Mapbender\CoordinatesUtilityBundle\Element;

use Mapbender\CoreBundle\Component\Element;
use Mapbender\CoreBundle\Entity\SRS;

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
    public static function getClassTags()
    {
        return [
            'mb.coordinatesutility.tag.coordinate',
            'mb.coordinatesutility.tag.map'
        ];
    }

    /**
     * @inheritdoc
     */
    public function getAssets()
    {
        return [
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
    }

    /**
     * @inheritdoc
     */
    public static function getDefaultConfiguration()
    {
        return [
            'type'      => null,
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

    /**
     * @inheritdoc
     */
    public function getConfiguration()
    {
        $configuration = parent::getConfiguration();

        if (isset($configuration['srsList']) && !empty($configuration['srsList'])) {
            $configuration['srsList'] = $this->addSrsDefinitions($configuration['srsList']);
        }

        return $configuration;
    }

    public function getPublicConfiguration()
    {
        $conf = parent::getPublicConfiguration();

        if (!isset($conf['zoomlevel'])) {
            $conf['zoomlevel'] = CoordinatesUtility::getDefaultConfiguration()['zoomlevel'];
        }

        return $conf;
    }

    /**
     * @param $srsList
     * @return mixed
     */
    public function addSrsDefinitions($srsList)
    {
        $srsWithDefinitions = $this->getSrsDefinitionsFromDatabase($srsList);

        foreach ($srsList as $key => $srs) {
            $srsName = $srs['name'];

            if (isset($srsWithDefinitions[$srsName])) {
                $srsList[$key]['definition'] = $srsWithDefinitions[$srsName]['definition'];
            }
        }

        return $srsList;
    }

    /**
     * @param $srsList
     * @return mixed
     */
    public function getSrsDefinitionsFromDatabase($srsList)
    {
        $queryBuilder = $this
            ->container
            ->get('doctrine')
            ->getManager()
            ->createQueryBuilder();

        $srsNames = array_map(function($srs) {
            return $srs['name'];
        }, $srsList);

        $queryBuilder
            ->select("srs")
            ->from(SRS::class, 'srs', 'srs.name')
            ->where('srs.name IN (:srsNames)')
            ->setParameter('srsNames', $srsNames)
            ->getQuery();

        return $queryBuilder->getQuery()->getArrayResult();
    }
}
