<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    amos\sitemanagement\widgets\icons
 * @category   CategoryName
 */

namespace lispa\amos\attachments\widgets\icons;

use lispa\amos\attachments\FileModule;
use lispa\amos\core\widget\WidgetIcon;
use yii\helpers\ArrayHelper;

/**
 * Class WidgetIconSiteManagementSlider
 * @package amos\sitemanagement\widgets\icons
 */
class WidgetIconGallery extends WidgetIcon
{

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->setLabel(FileModule::tHtml('amosattachments', '#label_gallery'));
        $this->setDescription(FileModule::t('amosattachments', '#desc_gallery'));
        $this->setIcon('linentita');
        $this->setUrl(['/attachments/attach-gallery/index']);
        $this->setCode('ATTACHMENTS_GALLERY');
        $this->setModuleName('attachments');
        $this->setNamespace(__CLASS__);

        $this->setClassSpan(
            ArrayHelper::merge(
                $this->getClassSpan(),
                [
                    'bk-backgroundIcon',
                    'color-lightPrimary'
                ]
            )
        );
    }

}
