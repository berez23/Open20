<?php
/**
 */
namespace dosamigos\google\maps;


use dosamigos\google\maps\controls\ControlPosition;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;

/**
 * RotateControlOptions
 *
 * Options for the rendering of the rotate control.
 *
 * For further information please visit its
 * [documentation](https://developers.google.com/maps/documentation/javascript/reference#RotateControlOptions) at
 * Google.
 *
 * @property string position Position id by [ControlPosition]. Used to specify the position of the control on the map.
 * The default position is [ControlPosition::TOP_LEFT].
 *
 * @package dosamigos\google\maps
 */
class RotateControlOptions extends ObjectAbstract
{
    use OptionsTrait;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->options = ArrayHelper::merge(
            [
                'position' => null,
            ],
            $this->options
        );
    }

    /**
     * Sets the position and makes sure is a valid [ControlPosition] value.
     *
     * @param string $value
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function setPosition($value)
    {
        if (!ControlPosition::getIsValid($value)) {
            throw new InvalidConfigException('Unknown "position" value');
        }
        $this->options['position'] = $value;
    }
} 