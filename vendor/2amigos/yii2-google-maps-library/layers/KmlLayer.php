<?php
/**
 */
namespace dosamigos\google\maps\layers;

use yii\base\InvalidConfigException;

/**
 * KmlLayer
 *
 * A KmlLayer adds geographic markup to the map from a KML, KMZ or GeoRSS file that is hosted on a publicly accessible
 * web server. A KmlFeatureData object is provided for each feature when clicked.
 *
 * For further information please visit its
 * [documentation](https://developers.google.com/maps/documentation/javascript/reference#KmlLayer) at Google.
 *
 *
 * @package dosamigos\google\maps\layers
 */
class KmlLayer extends KmlLayerOptions
{
    /**
     * @inheritdoc
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        if ($this->map == null) {
            throw new InvalidConfigException('"map" cannot be null');
        }
    }

    /**
     * Returns the required initialization javascript code
     *
     * @return string
     */
    public function getJs()
    {
        $name = $this->getName();
        $options = $this->getEncodedOptions();

        return "var {$name} = new google.maps.KmlLayer({$options});";
    }
} 