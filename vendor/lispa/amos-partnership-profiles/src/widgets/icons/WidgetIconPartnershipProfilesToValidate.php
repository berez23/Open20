<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\partnershipprofiles\widgets\icons
 * @category   CategoryName
 */

namespace lispa\amos\partnershipprofiles\widgets\icons;

use lispa\amos\core\widget\WidgetIcon;
use lispa\amos\partnershipprofiles\Module;
use lispa\amos\core\widget\WidgetAbstract;
use lispa\amos\core\icons\AmosIcons;
use yii\helpers\ArrayHelper;

/**
 * Class WidgetIconPartnershipProfilesToValidate
 * @package lispa\amos\partnershipprofiles\widgets\icons
 */
class WidgetIconPartnershipProfilesToValidate extends WidgetIcon
{

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $paramsClassSpan = [
            'bk-backgroundIcon',
            'color-primary'
        ];

        $this->setLabel(Module::tHtml('amospartnershipprofiles', 'To Validate'));
        $this->setDescription(Module::t('amospartnershipprofiles', 'Show the partnership profiles to validate'));

        if (!empty(\Yii::$app->params['dashboardEngine']) && \Yii::$app->params['dashboardEngine'] == WidgetAbstract::ENGINE_ROWS) {
            $this->setIconFramework(AmosIcons::IC);
            $this->setIcon('propostecollaborazione');
            $paramsClassSpan = [];
        } else {
            $this->setIcon('partnership-profiles');
        }

        $this->setUrl(['/partnershipprofiles/partnership-profiles/to-validate']);
        $this->setCode('PARTNERSHIP_PROFILES_TOVALIDATE');
        $this->setModuleName('partnershipprofiles');
        $this->setNamespace(__CLASS__);

        $this->setClassSpan(
            ArrayHelper::merge(
                $this->getClassSpan(),
                $paramsClassSpan
            )
        );
    }

}
