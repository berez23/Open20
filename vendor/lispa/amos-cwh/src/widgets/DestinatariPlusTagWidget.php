<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\cwh
 * @category   CategoryName
 */

namespace lispa\amos\cwh\widgets;

use yii\base\Widget;

/**
 * Class DestinatariPlusTagWidget
 * @package lispa\amos\cwh\widgets
 */
class DestinatariPlusTagWidget extends Widget
{
    public
        $model,
        $moduleCwh,
        $scope;

    /**
     * @var int|array $singleFixedTreeId
     */
    public $singleFixedTreeId;

    /**
     * @return string
     */
    public function run()
    {
        return $this->render(
            'destinatari-plus-tag',
            [
                'singleFixedTreeId' => $this->singleFixedTreeId,
                'model' => $this->model,
                'moduleCwh' => $this->moduleCwh
            ]
        );
    }

    /**
     * @return \yii\db\ActiveRecord
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param \yii\db\ActiveRecord $model
     */
    public function setModel($model)
    {
        $this->model = $model;
    }
}
