<?php

namespace Mapbender\CoordinatesUtilityBundle;

use Mapbender\CoreBundle\Component\MapbenderBundle;

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
}
