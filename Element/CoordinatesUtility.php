<?php

namespace Mapbender\CoordinatesUtilityBundle\Element;

use Mapbender\CoreBundle\Component\Element;

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
    static public function listAssets()
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
    public function render()
    {
        return $this
            ->container
            ->get('templating')
            ->render(
                'MapbenderCoordinatesUtilityBundle:Element:coordinatesutility.html.twig',
                [
                    'id'            => $this->getId(),
                    'configuration' => $this->entity->getConfiguration(),
                    'title'         => $this->getTitle()
                ]
            );
    }
}
