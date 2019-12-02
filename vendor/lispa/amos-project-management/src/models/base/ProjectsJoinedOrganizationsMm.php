<?php

namespace lispa\amos\projectmanagement\models\base;

use lispa\amos\projectmanagement\Module;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the base-model class for table "projects_joined_organization_mm".
 *
 * @property integer $id
 * @property integer $projects_id
 * @property integer $organization_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $deleted_by
 *
 * @property \lispa\amos\projectmanagement\models\Projects $projects
 */
class ProjectsJoinedOrganizationsMm extends ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'projects_joined_organization_mm';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['projects_id', 'organization_id'], 'required'],
            [['projects_id', 'organization_id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [
                ['organization_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => $this->organizationModelClass,
                'targetAttribute' => ['organization_id' => 'id']
            ],
            [
                ['projects_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Projects::className(),
                'targetAttribute' => ['projects_id' => 'id']
            ],
        ];
    }

    public $organizationModelClass;

    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub

        $this->organizationModelClass = Module::getModuleOrganization()->getOrganizationModelClass();
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'projects_id' => 'Projects ID',
            'organization_id' => 'Organization ID',
            'created_at' => 'Creato il',
            'updated_at' => 'Aggiornato il',
            'deleted_at' => 'Cancellato il',
            'created_by' => 'Creato da',
            'updated_by' => 'Aggiornato da',
            'deleted_by' => 'Cancellato da',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrganization()
    {
        return $this->hasOne($this->organizationModelClass,
            ['id' => 'organization_id'])->inverseOf('projectsJoinedOrganizationsMms');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjects()
    {
        return $this->hasOne(\lispa\amos\projectmanagement\models\Projects::className(),
            ['id' => 'projects_id'])->inverseOf('p1rojectsJoinedOrganizationsMms');
    }
}
