<?php
/**
 */

namespace yii\db;

/**
 * Class PdoValueBuilder builds object of the [[PdoValue]] expression class.
 *
 * @since 2.0.14
 */
class PdoValueBuilder implements ExpressionBuilderInterface
{
    const PARAM_PREFIX = ':pv';


    /**
     * {@inheritdoc}
     */
    public function build(ExpressionInterface $expression, array &$params = [])
    {
        $placeholder = static::PARAM_PREFIX . count($params);
        $params[$placeholder] = $expression;

        return $placeholder;
    }
}
