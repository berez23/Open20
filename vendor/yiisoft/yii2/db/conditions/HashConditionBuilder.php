<?php
/**
 */

namespace yii\db\conditions;

use yii\db\ExpressionBuilderInterface;
use yii\db\ExpressionBuilderTrait;
use yii\db\ExpressionInterface;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class HashConditionBuilder builds objects of [[HashCondition]]
 *
 * @since 2.0.14
 */
class HashConditionBuilder implements ExpressionBuilderInterface
{
    use ExpressionBuilderTrait;


    /**
     * Method builds the raw SQL from the $expression that will not be additionally
     * escaped or quoted.
     *
     * @param ExpressionInterface|HashCondition $expression the expression to be built.
     * @param array $params the binding parameters.
     * @return string the raw SQL that will not be additionally escaped or quoted.
     */
    public function build(ExpressionInterface $expression, array &$params = [])
    {
        $hash = $expression->getHash();
        $parts = [];
        foreach ($hash as $column => $value) {
            if (ArrayHelper::isTraversable($value) || $value instanceof Query) {
                // IN condition
                $parts[] = $this->queryBuilder->buildCondition(new InCondition($column, 'IN', $value), $params);
            } else {
                if (strpos($column, '(') === false) {
                    $column = $this->queryBuilder->db->quoteColumnName($column);
                }
                if ($value === null) {
                    $parts[] = "$column IS NULL";
                } elseif ($value instanceof ExpressionInterface) {
                    $parts[] = "$column=" . $this->queryBuilder->buildExpression($value, $params);
                } else {
                    $phName = $this->queryBuilder->bindParam($value, $params);
                    $parts[] = "$column=$phName";
                }
            }
        }

        return count($parts) === 1 ? $parts[0] : '(' . implode(') AND (', $parts) . ')';
    }
}