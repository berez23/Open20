<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\admin\widgets\icons
 * @category   CategoryName
 */

namespace lispa\amos\admin\widgets\icons;

use lispa\amos\core\widget\WidgetIcon;
use lispa\amos\core\widget\WidgetAbstract;
use lispa\amos\core\icons\AmosIcons;

use lispa\amos\admin\AmosAdmin;
use lispa\amos\admin\models\UserProfile;

use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class WidgetIconUserProfile
 * @package lispa\amos\admin\widgets\icons
 */
class WidgetIconUserProfile extends WidgetIcon
{

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $paramsClassSpan = [
            'bk-backgroundIcon',
            'color-darkGrey'
        ];

        $this->setLabel(AmosAdmin::tHtml('amosadmin', 'All users'));
        $this->setDescription(AmosAdmin::t('amosadmin', 'List of all platform users'));

        if (!empty(Yii::$app->params['dashboardEngine']) && Yii::$app->params['dashboardEngine'] == WidgetAbstract::ENGINE_ROWS) {
            $this->setIconFramework(AmosIcons::IC);
            $this->setIcon('user');
            $paramsClassSpan = [];
        } else {
            $this->setIcon('users');
        }

        $this->setUrl(['/admin/user-profile/index']);
        $this->setCode('ALL_USERS');
        $this->setModuleName(AmosAdmin::getModuleName());
        $this->setNamespace(__CLASS__);

        $this->setClassSpan(
            ArrayHelper::merge(
                $this->getClassSpan(),
                $paramsClassSpan
            )
        );

        $query = new Query();
        $query
            ->select([UserProfile::tableName().'.id', UserProfile::tableName().'.attivo', UserProfile::tableName().'.deleted_at'])
            ->from(UserProfile::tableName())
            ->andWhere([
                '>=', 
                UserProfile::tableName().'.created_at',
                Yii::$app->getUser()->getIdentity()->getProfile()->ultimo_logout
            ]);
        
        $this->setBulletCount(
            $this->makeBulletCounter(
                Yii::$app->getUser()->getId(),
                UserProfile::className(),
                $query
            )
        );
    }

}
