<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\admin\migrations
 * @category   CategoryName
 */

use lispa\amos\admin\models\UserProfileArea;
use yii\db\Migration;

/**
 * Class m181012_162615_add_user_profile_area_field_1
 */
class m181107_171015_remove_widgets extends Migration
{



    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->update('amos_widgets', ['status' => 0], ['classname' => 'lispa\amos\admin\widgets\icons\WidgetIconMyProfile']);
        $this->update('amos_widgets', ['status' => 0], ['classname' => 'lispa\amos\admin\widgets\graphics\WidgetGraphicMyProfile']);


    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->update('amos_widgets', ['status' => 1], ['classname' => 'lispa\amos\admin\widgets\icons\WidgetIconMyProfile']);
        $this->update('amos_widgets', ['status' => 1], ['classname' => 'lispa\amos\admin\widgets\graphics\WidgetGraphicMyProfile']);
    }
}
