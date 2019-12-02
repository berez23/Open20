<?php

namespace lispa\amos\projectmanagement\models\base;

use lispa\amos\core\user\User;
use lispa\amos\projectmanagement\Module;
use yii\helpers\ArrayHelper;

/**
 * Class ProjectsActivities
 *
 * This is the base-model class for table "projects_activities".
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $deleted_by
 *
 * @property User[] $attrUserMm
 * @property \lispa\amos\projectmanagement\models\ProjectsTasks[] $projectsTasks
 * @property \lispa\amos\projectmanagement\models\ProjectsActivitiesOrganizationMm[] $projectsActivitiesOrganizationMms
 * @property \lispa\amos\projectmanagement\models\ProjectsTasksOrganizationMm[] $tasksOrganizationMms
 * @property \lispa\amos\projectmanagement\models\ProjectsActivitiesProjectsMm[] $projectsActivitiesProjectsMms
 * @property \lispa\amos\projectmanagement\models\ProjectsTasksProjectsActivitiesMm[] $projectsTasksProjectsActivitiesMms
 * @property \lispa\amos\projectmanagement\models\Projects $projects
 * @property int $publish [int(1)]
 * @property string $status [varchar(255)]
 * @property int $version [int(11)]
 *
 * @package lispa\amos\projectmanagement\models\base
 */
class ProjectsActivities extends \lispa\amos\core\record\Record
{
    /**
     * @var array Attributo di relazione
     */
    public $attrProjectsMm;

    /**
     * @var array Attributo di relazione
     */
    public $attrOrganizationMm;
    public $attrUserMm;
    public $organizationModelClass;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'projects_activities';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['description'], 'string'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['publish', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['attrProjectsMm'], 'safe'],
            [['attrProjectsMm'], 'required'],
            [['attrOrganizationMm'], 'safe'],
            [['attrOrganizationMm'], 'required'],
            [['attrUserMm'], 'safe'],
            [['attrUserMm'], 'required'],
        ];
    }

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
        return ArrayHelper::merge(parent::attributeLabels(), [
            'id' => 'ID',
            'name' => Module::t('amosproject_management', 'Name'),
            'description' => Module::t('amosproject_management', 'Description'),
            'publish' => Module::t('amosproject_management', 'Publish To The Calendar'),
            'created_at' => Module::t('amosproject_management', 'Creato il'),
            'updated_at' => Module::t('amosproject_management', 'Aggiornato il'),
            'deleted_at' => Module::t('amosproject_management', 'Cancellato il'),
            'created_by' => Module::t('amosproject_management', 'Creato da'),
            'updated_by' => Module::t('amosproject_management', 'Aggiornato da'),
            'deleted_by' => Module::t('amosproject_management', 'Cancellato da'),
            'projects' => Module::t('amosproject_management', 'Project'),
            'attrProjectsMm' => Module::t('amosproject_management', 'Project'),
            'organization' => Module::t('amosproject_management', 'Reference Organization'),
            'attrOrganizationMm' => Module::t('amosproject_management', 'Reference Organization'),
            'attrUserMm' => Module::t('amosproject_management', 'Reference User'),
            'startDate' => Module::t('amosproject_management', 'Start Date'),
            'endDate' => Module::t('amosproject_management', 'End Date'),
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjectsActivitiesOrganizationMms()
    {
        return $this->hasMany(\lispa\amos\projectmanagement\models\ProjectsActivitiesOrganizationMm::className(),
            ['projects_activities_id' => 'id'])->inverseOf('projectsActivities');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjectsActivitiesProjectsMms()
    {
        return $this->hasMany(\lispa\amos\projectmanagement\models\ProjectsActivitiesProjectsMm::className(),
            ['projects_activities_id' => 'id'])->inverseOf('projectsActivities');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjectsTasksProjectsActivitiesMms()
    {
        return $this->hasMany(\lispa\amos\projectmanagement\models\ProjectsTasksProjectsActivitiesMm::className(),
            ['projects_activities_id' => 'id'])->inverseOf('projectsActivities');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjects()
    {
        return $this->hasOne(\lispa\amos\projectmanagement\models\Projects::className(),
            ['id' => 'projects_id'])->via('projectsActivitiesProjectsMms');
    }

    public function getAttrProjectsMm()
    {
        $ritorno = "";
        $ritorno .= '' . $this->projects->name;

        return $ritorno;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrganization()
    {
        return $this->hasOne($this->organizationModelClass,
            ['id' => 'organization_id'])->via('projectsActivitiesOrganizationMms');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(),
            ['id' => 'user_id'])->via('projectsActivitiesOrganizationMms');
    }

    public function getattrOrganizationMm()
    {
        $ritorno = "";
        if ($this->organization) {
            $ritorno .= '' . $this->organization->nameField;
        }

        return $ritorno;
    }

    public function getattrUserMm()
    {
        $ritorno = "";
        if ($this->user) {
            $ritorno .= '' . $this->user->userProfile->nomeCognome;
        }

        return $ritorno;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjectsTasks()
    {
        return $this->hasMany(\lispa\amos\projectmanagement\models\ProjectsTasks::className(), ['id' => 'projects_tasks_id'])->via('projectsTasksProjectsActivitiesMms');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasksOrganizationMms()
    {
        return $this->hasMany(\lispa\amos\projectmanagement\models\ProjectsTasksOrganizationMm::className(), ['projects_tasks_organization_mm.projects_tasks_id' => 'id'])->via('projectsTasks')->distinct();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasksJoinedOrganizationMms()
    {
        return $this->hasMany(\lispa\amos\projectmanagement\models\ProjectsTasksJoinedOrganizationsMm::className(), ['projects_tasks_joined_organization_mm.projects_tasks_id' => 'id'])->via('projectsTasks')->distinct();
    }

}
