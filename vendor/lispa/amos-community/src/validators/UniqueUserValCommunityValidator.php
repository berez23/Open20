<?php
/**
 * Created by PhpStorm.
 * User: michele.lafrancesca
 * Date: 14/06/2019
 * Time: 16:40
 */

namespace lispa\amos\community\validators;


use lispa\amos\community\AmosCommunity;
use lispa\amos\community\models\CommunityUserFieldVal;
use yii\validators\Validator;

class UniqueUserValCommunityValidator extends Validator
{

    public $community_id;

    /**
     * @param \yii\base\Model $model
     * @param string $attribute
     * @throws \yii\base\InvalidConfigException
     */
    function validateAttribute($model, $attribute)
    {

       $value = $model->$attribute;
       $fieldValCount = CommunityUserFieldVal::find()
           ->innerJoin('community_user_field', 'community_user_field_val.user_field_id = community_user_field.id')
           ->andWhere(['value' => $value])
           ->andWhere(['community_id' => $this->community_id])
           ->count();
       if($fieldValCount > 0){
           $this->addError($model, $attribute, AmosCommunity::t('amoscommunity', 'Questo campo deve essere univoco'));
           return false;
       }
       return true;
    }




}