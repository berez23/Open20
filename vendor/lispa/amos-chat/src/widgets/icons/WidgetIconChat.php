<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\chat
 * @category   CategoryName
 */

namespace lispa\amos\chat\widgets\icons;

use lispa\amos\core\widget\WidgetIcon;

use lispa\amos\chat\models\Message;
use lispa\amos\chat\AmosChat;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class WidgetIconChat
 * @package lispa\amos\chat\widgets\icons
 */
class WidgetIconChat extends WidgetIcon
{
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->setLabel(AmosChat::tHtml('amoschat', 'Messaggi privati'));
        $this->setDescription(AmosChat::t('amoschat', 'Visualizza i messaggi privati'));

        $this->setIcon('comments-o');
        $this->setUrl(['/messages']);
        $this->setCode('CHAT');
        $this->setModuleName('chat');
        $this->setNamespace(__CLASS__);

        $this->setClassSpan(
            ArrayHelper::merge(
                $this->getClassSpan(),
                [
                    'bk-backgroundIcon',
                    'color-primary'
                ]
            )
        );
        
        if ($this->disableBulletCounters == false) {
            $this->setBulletCount(
                $this->makeBulletCounter(
                    Yii::$app->getUser()->getId(),
                    Message::className(),
                    Message::find()
                    ->select('id')
                    ->andWhere([
                        'is_new' => true,
                        'receiver_id' => Yii::$app->getUser()->getId(),
                        'is_deleted_by_receiver' => false
                    ])
                )
            );
        }
    }

    /**
     * Aggiunge all'oggetto container tutti i widgets recuperati dal controller del modulo
     * 
     * @return array
     */
    public function getOptions()
    {
        return ArrayHelper::merge(
                parent::getOptions(),
                ['children' => []]
        );
    }

}
