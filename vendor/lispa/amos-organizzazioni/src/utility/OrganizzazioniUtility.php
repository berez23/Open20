<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\organizzazioni\utility
 * @category   CategoryName
 */

namespace lispa\amos\organizzazioni\utility;

use lispa\amos\admin\models\UserProfile;
use lispa\amos\admin\models\UserProfileArea;
use lispa\amos\admin\models\UserProfileRole;
use lispa\amos\community\models\CommunityType;
use lispa\amos\community\models\CommunityUserMm;
use lispa\amos\core\exceptions\AmosException;
use lispa\amos\core\interfaces\OrganizationsModelInterface;
use lispa\amos\core\record\Record;
use lispa\amos\organizzazioni\models\Profilo;
use lispa\amos\organizzazioni\models\ProfiloEntiType;
use lispa\amos\organizzazioni\models\ProfiloSedi;
use lispa\amos\organizzazioni\models\ProfiloSediTypes;
use lispa\amos\organizzazioni\models\ProfiloSediUserMm;
use lispa\amos\organizzazioni\models\ProfiloUserMm;
use lispa\amos\organizzazioni\Module;
use Yii;
use yii\base\BaseObject;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\log\Logger;

/**
 * Class OrganizzazioniUtility
 * @package lispa\amos\organizzazioni\utility
 */
class OrganizzazioniUtility extends BaseObject
{
    /**
     * This method returns all platform organizations ready for select.
     * @param Profilo $model
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public static function getMembershipOrganizationsReadyForSelect($model)
    {
        /** @var Profilo $modelProfilo */
        $modelProfilo = Module::instance()->createModel('Profilo');
        /** @var ActiveQuery $query */
        $query = $modelProfilo::find();
        if ($model->id) {
            $query->andWhere(['<>', 'id', $model->id]);
        }
        $organizations = $query->all();
        $readyForSelect = ArrayHelper::map($organizations, 'id', 'name');
        return $readyForSelect;
    }

    /**
     * This method returns all profilo sedi types ready for select.
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public static function getProfiloSediTypesReadyForSelect()
    {
        /** @var Module $organizzazioniModule */
        $organizzazioniModule = \Yii::$app->getModule(Module::getModuleName());
        /** @var ProfiloSediTypes $modelProfiloSediTypes */
        $modelProfiloSediTypes = Module::instance()->createModel('ProfiloSediTypes');
        /** @var ActiveQuery $query */
        $query = $modelProfiloSediTypes::find();
        if (!$organizzazioniModule->enableAddOtherLegalHeadquarters) {
            $query->andWhere(['<>', 'id', ProfiloSediTypes::TYPE_LEGAL_HEADQUARTER]);
        }
        $query->andWhere(['active' => 1]);
        $query->orderBy(['order' => SORT_ASC]);
        $readyForSelect = ArrayHelper::map($query->all(), 'id', 'name');
        return $readyForSelect;
    }

    /**
     * This method copy the operative headquarter object field values to the legal
     * headquarter object fields. It returns the legal headquarter object.
     * @param ProfiloSedi $operativeHeadquarter
     * @param ProfiloSedi $legalHeadquarter
     * @param array $skipColumns
     * @return ProfiloSedi
     */
    public static function copyOperativeToLegalHeadquarterValues($operativeHeadquarter, $legalHeadquarter, $skipColumns = ['profilo_sedi_type_id', 'id'])
    {
        $sedeColumns = $operativeHeadquarter->attributes();
        foreach ($sedeColumns as $sedeColumn) {
            if (!in_array($sedeColumn, $skipColumns)) {
                $legalHeadquarter->{$sedeColumn} = $operativeHeadquarter->{$sedeColumn};
            }
        }
        return $legalHeadquarter;
    }


    /**
     * @param ProfiloUserMm $profiloUserMm
     * @return UserProfileArea[]
     * @throws \yii\base\InvalidConfigException
     */
    public static function getUserProfileAreas($profiloUserMm)
    {
        $moduleAdmin = \Yii::$app->getModule('admin');
        /** @var ActiveQuery $query */
        if ($moduleAdmin) {
            $query = UserProfileArea::find();
            if ($moduleAdmin->adminModule->roleAndAreaOnOrganizations && $moduleAdmin->adminModule->roleAndAreaFromOrganizationsWithTypeCat) {
                $query->andWhere(['type_cat' => [UserProfileArea::TYPE_CAT_GENERIC, $profiloUserMm->profilo->profilo_enti_type_id]]);
            }
            $query->orderBy(['order' => SORT_ASC]);
            $areas = $query->all();
            return $areas;
        }
        return null;
    }

    /**
     * @param ProfiloUserMm $profiloUserMm
     * @return UserProfileArea[]
     * @throws \yii\base\InvalidConfigException
     */
    public static function getUserProfileRoles($profiloUserMm)
    {
        $moduleAdmin = \Yii::$app->getModule('admin');
        /** @var ActiveQuery $query */
        if ($moduleAdmin) {
            $query = UserProfileRole::find();
            if ($moduleAdmin->adminModule->roleAndAreaOnOrganizations && $moduleAdmin->adminModule->roleAndAreaFromOrganizationsWithTypeCat) {
                $query->andWhere(['type_cat' => [UserProfileRole::TYPE_CAT_GENERIC, $profiloUserMm->profilo->profilo_enti_type_id]]);
            }
            $query->orderBy(['order' => SORT_ASC]);
            $areas = $query->all();
            return $areas;
        }
        return null;
    }

    /**
     * @param $userId
     * @param bool $onlyIds
     * @param bool $returnQuery
     * @return array|ActiveQuery|\yii\db\ActiveRecord[]
     * @throws AmosException
     */
    public static function getOrganizationsRepresentedOrReferredByUserId($userId, $onlyIds = false, $returnQuery = false)
    {
        if (!is_numeric($userId) || ($userId <= 0)) {
            throw new AmosException(Module::t('amosorganizzazioni', 'getOrganizationsRepresentedOrReferredByUserId: userId is not a number or is not positive'));
        }
        /** @var Profilo $profiloModel */
        $profiloModel = Module::instance()->createModel('Profilo');
        /** @var ActiveQuery $query */
        $query = $profiloModel::find();
        $query->andWhere(['or',
            ['rappresentante_legale' => $userId],
            ['referente_operativo' => $userId]
        ]);
        if ($returnQuery) {
            return $query;
        }
        if ($onlyIds) {
            $query->select([$profiloModel::tableName() . '.id']);
            $organizations = $query->column();
        } else {
            $organizations = $query->all();
        }
        return $organizations;
    }

    /**
     * This method returns an array of UserProfile objects that contains the
     * legal representative and the operative referee of the organization passed by param.
     * @param int $organizationId
     * @return UserProfile[]|bool
     */
    public static function getOrganizationReferees($organizationId, $onlyIds = false)
    {
        /** @var Profilo $profiloModel */
        $profiloModel = Module::instance()->createModel('Profilo');
        $organization = $profiloModel::findOne($organizationId);
        if (is_null($organization)) {
            return false;
        }
        /** @var Module $organizationsModule */
        $organizationsModule = Module::instance();
        $organizationReferees = [];
        if (!$organizationsModule->enableRappresentanteLegaleText && !is_null($organization->rappresentanteLegale)) {
            if ($onlyIds) {
                $organizationReferees[] = $organization->rappresentanteLegale->user_id;
            } else {
                $organizationReferees[] = $organization->rappresentanteLegale;
            }
        }
        if (!is_null($organization->referenteOperativo)) {
            if ($onlyIds) {
                $organizationReferees[] = $organization->referenteOperativo->user_id;
            } else {
                $organizationReferees[] = $organization->referenteOperativo;
            }
        }
        return $organizationReferees;
    }

    /**
     * @param int $userId
     * @param string $modelName
     * @param string $mmModelName
     * @param string $relationName
     * @return OrganizationsModelInterface[]
     * @throws \yii\base\InvalidConfigException
     */
    private static function getUserMainModels($userId, $modelName, $mmModelName, $relationName, $mmModelStatus)
    {
        /** @var Record $model */
        $model = Module::instance()->createModel($modelName);
        /** @var Record $mmModel */
        $mmModel = Module::instance()->createModel($mmModelName);
        /** @var ActiveQuery $query */
        $query = $model::find();
        $query->innerJoinWith($relationName);
        $query->andWhere([$mmModel::tableName() . '.user_id' => $userId]);
        $query->andWhere([$mmModel::tableName() . '.status' => $mmModelStatus]);
        $models = $query->all();
        return $models;
    }

    /**
     * This method returns all the organizations of an user.
     * @param int $userId
     * @return OrganizationsModelInterface[]
     * @throws \yii\base\InvalidConfigException
     */
    public static function getUserOrganizations($userId)
    {
        return static::getUserMainModels($userId, 'Profilo', 'ProfiloUserMm', 'profiloUserMms', ProfiloUserMm::STATUS_ACTIVE);
    }

    /**
     * This method returns all the headquarters of an user, if the module has headquarters.
     * @param int $userId
     * @return OrganizationsModelInterface[]
     * @throws \yii\base\InvalidConfigException
     */
    public static function getUserHeadquarters($userId)
    {
        return static::getUserMainModels($userId, 'ProfiloSedi', 'ProfiloSediUserMm', 'profiloSediUserMms', ProfiloSediUserMm::STATUS_ACTIVE);
    }

    /**
     * @return mixed|null
     */
    public static function getProfiloEntiTypeListReadyForSelect()
    {
        return ArrayHelper::map(ProfiloEntiType::find()->orderBy(['priority' => SORT_ASC])->all(), 'id', 'name');
    }

    /**
     * Create a community for the event.
     * @param Profilo $model
     * @param string $managerStatus
     * @return bool
     */
    public static function createCommunity($model, $managerStatus = '')
    {
        $communityModule = Yii::$app->getModule('community');
        $title = $model->title
            ? $model->title
            : '';

        $description = $model->description
            ? $model->description
            : '';

        $type = CommunityType::COMMUNITY_TYPE_CLOSED; // DEFAULT TYPE
        $context = Profilo::className();
        $managerRole = $model->getManagerRole();

        try {
            $model->community_id = $communityModule->createCommunity(
                $title,
                $type,
                $context,
                $managerRole,
                $description,
                $model,
                $managerStatus
            );

            $ok = $model->save(false);

            if ($ok) {
                // Add Rappresentante Legale e Referente Operativo come utenti di default
                if ($model->rappresentante_legale) {
                    $communityModule->createCommunityUser(
                        $model->community_id,
                        $managerStatus,
                        $managerRole,
                        $model->rappresentante_legale
                    );
                }

                if ($model->referente_operativo) {
                    $communityModule->createCommunityUser(
                        $model->community_id,
                        $managerStatus,
                        $managerRole,
                        $model->referente_operativo
                    );
                }
            }

        } catch (CommunityException $exception) {
            Yii::getLogger()->log($exception->getMessage(), Logger::LEVEL_ERROR);
            $ok = false;
        }

        return $ok;
    }

    /**
     *
     * @param type $model
     * @param type $communityModel
     */
    public static function updateCommunity($model, $communityModule)
    {
        $title = $model->title
            ? $model->title
            : '';
        $description = $model->description
            ? $model->description
            : '';

        $model->community->name = $model->title;
        $model->community->description = $model->description;

        try {
            // Used by OrganizzazioniUtility::updateCommunity()
            $oldAttrs = $model->community->getOldAttributes();

            $ok = $model->community->save(false);

            // Update Rappresentante legale as community member and manager
            if ($oldAttr['rappresentante_legale'] != $model->rappresentante_legale) {
                $communityModule->deleteCommunityUser($communityId, $oldAttr['rappresentante_legale']);
                $communityModule->createCommunityUser(
                    $communityId,
                    $managerStatus,
                    $managerRole,
                    $model->rappresentante_legale
                );
            }

            // Update Referente Operativo as community memember and manager
            if ($oldAttr['referente_operativo'] != $model->referente_operativo) {
                $communityModule->deleteCommunityUser($communityId, $model->referente_operativo);
                $communityModule->createCommunityUser(
                    $communityId,
                    $managerStatus,
                    $managerRole,
                    $model->referente_operativo
                );
            }

        } catch (\Exception $exception) {
            \Yii::getLogger()->log($exception->getMessage(), Logger::LEVEL_ERROR);
        }

    }

    /**
     * Check if there is at least one confirmed event manager only if there is a community. If not it return true.
     * @param Event $event
     * @param string $status
     * @return array[CommunityUserMm]
     */
    public static function findOrganizzazioneManagers($model, $status = '')
    {
        if (!$model->community_id) {
            return [];
        }

        $where = [
            'community_id' => $model->community_id,
            'role' => $model->getManagerRole()
        ];

        if ($status) {
            $where['status'] = $status;
        }

        $managers = CommunityUserMm::find()->andWhere($where)->all();

        return $managers;
    }

}
