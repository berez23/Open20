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
 * Class WidgetIconPartnerProfExprOfIntDashboard
 * @package lispa\amos\partnershipprofiles\widgets\icons
 */
class WidgetIconPartnerProfExprOfIntDashboard extends WidgetIcon {

    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();

        $paramsClassSpan = [
            'bk-backgroundIcon',
            'color-primary'
        ];

        $this->setLabel(Module::tHtml('amospartnershipprofiles', '#part_prof_expr_of_int_widget_label'));
        $this->setDescription(Module::t('amospartnershipprofiles', 'Partnership profiles and expressions of interest of the user you facilitate'));

        if (!empty(\Yii::$app->params['dashboardEngine']) && \Yii::$app->params['dashboardEngine'] == WidgetAbstract::ENGINE_ROWS) {
            $this->setIconFramework(AmosIcons::IC);
            $this->setIcon('propostecollaborazione');
            $paramsClassSpan = [];
        } else {
            $this->setIcon('partnership-profiles');
        }

        $this->setUrl(['/partnershipprofiles/partnership-profiles/facilitator-partnership-profiles']);
        $this->setCode('PARTNER_PROF_EXPR_OF_INT');
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
