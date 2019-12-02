<?php

namespace lispa\amos\projectmanagement\models;

use lispa\amos\attachments\behaviors\FileBehavior;
use lispa\amos\community\AmosCommunity;
use lispa\amos\community\models\Community;
use lispa\amos\community\models\CommunityContextInterface;
use lispa\amos\community\models\CommunityUserMm;
use lispa\amos\core\icons\AmosIcons;
use lispa\amos\core\interfaces\ModelLabelsInterface;
use lispa\amos\core\interfaces\OrganizationsModelInterface;
use lispa\amos\core\interfaces\ViewModelInterface;
use lispa\amos\core\user\User;
use lispa\amos\cwh\AmosCwh;
use lispa\amos\cwh\behaviors\TaggableInterestingBehavior;
use lispa\amos\projectmanagement\controllers\ProjectsTasksController;
use lispa\amos\projectmanagement\events\ProjectsWorkflowEvent;
use lispa\amos\projectmanagement\i18n\grammar\ProjectsGrammar;
use lispa\amos\projectmanagement\interfaces\ItemWorkPlanInterface;
use lispa\amos\projectmanagement\Module;
use lispa\amos\projectmanagement\utility\ProjectManagementUtility;
use lispa\amos\projectmanagement\widgets\icons\WidgetIconProjectsActivities;
use raoul2000\workflow\base\SimpleWorkflowBehavior;
use Yii;
use yii\base\Exception;
use yii\db\ActiveQuery;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use yii\helpers\Url;

/**
 * This is the model class for table "projects".
 * @method \cornernote\workflow\manager\components\WorkflowDbSource getWorkflowSource()
 * @property \lispa\amos\community\models\CommunityUserMm $communityUserMm
 * @property int $membership_type_id [int(1)]
 * @property string $status [varchar(255)]
 * @property int $version [int(11)]
 */
class Projects extends \lispa\amos\projectmanagement\models\base\Projects implements CommunityContextInterface, ModelLabelsInterface, ViewModelInterface
{
    /**
     * @var    string    PROJECTS_WORKFLOW    Projects Workflow ID
     */
    const PROJECTS_WORKFLOW = 'ProjectsWorkflow';
    const PROJECTS_WORKFLOW_STATUS_STARTUP = 'ProjectsWorkflow/STARTUP';
    const PROJECTS_WORKFLOW_STATUS_VALIDATED = 'ProjectsWorkflow/VALIDATED';
    const PROJECTS_WORKFLOW_STATUS_ONEDIT = 'ProjectsWorkflow/ONEDIT';
    const PROJECTS_WORKFLOW_STATUS_CLOSED = 'ProjectsWorkflow/CLOSED';
    const SESSION_UPDATE_REFERER = 'projects_update_referer';

    const SCENARIO_CREATE_SAVE = 'create_save_without_wizard';

    const ROLE_PROJECT_MANAGER = 'PROJECT_MANAGER';
    const COMMUNITY_MANAGER_ROLE_NAME = 'Project Manager';
    const ROLE_PARTNER_REFERENT = 'PARTNER_REFERENT';
    const ROLE_PARTICIPANT = 'PARTICIPANT';
    public $logo;

    /**
     * @inheritdoc
     */
    public function init()
    {
        if ($this->isNewRecord) {
            $this->status = $this->getWorkflowSource()->getWorkflow(self::PROJECTS_WORKFLOW)->getInitialStatusId();
        }
        // workflow events
        $this->on('afterChangeStatusFrom{' . self::PROJECTS_WORKFLOW_STATUS_STARTUP . '}to{' . self::PROJECTS_WORKFLOW_STATUS_VALIDATED . '}', [new ProjectsWorkflowEvent(), 'afterChangeStatusFromStartupToValidated'], $this);
        $this->on('afterChangeStatusFrom{' . self::PROJECTS_WORKFLOW_STATUS_ONEDIT . '}to{' . self::PROJECTS_WORKFLOW_STATUS_VALIDATED . '}', [new ProjectsWorkflowEvent(), 'afterChangeStatusFromOnModifyToValidated'], $this);

        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function representingColumn()
    {
        return [
            'name',
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $parentScenarios = parent::scenarios();
        $scenarios = ArrayHelper::merge(
            $parentScenarios,
            [
                self::SCENARIO_CREATE_SAVE => $parentScenarios[self::SCENARIO_DEFAULT]
            ]
        );
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        parent::afterFind(); // TODO: Change the autogenerated stub

        $this->logo = $this->getLogo()->one();
    }

    /**
     * @inheritdoc
     */
    public function getLogo()
    {
        return $this->hasOneFile('logo');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['finish_date'], 'date', 'format' => 'php:Y-m-d'],
            ['finish_date', 'validateDates'],
            [['logo'], 'file'],
            [['logo'], 'image'],
            [['status'], 'safe'],
            [['start_date', 'finish_date'],'required', 'on' => self::SCENARIO_CREATE_SAVE]
        ]);
    }

    public function validateDates()
    {
        if (strtotime($this->finish_date) <= strtotime($this->start_date)) {
            $this->addError('start_date', Module::t('amosproject_management', 'Start date must be set before finish date'));
            $this->addError('finish_date', Module::t('amosproject_management', 'Finish date must be set after start date'));
        }
    }

    /**
     * Adding the file behavior
     */
    public function behaviors()
    {
        $behaviors = ArrayHelper::merge(parent::behaviors(), [
            'fileBehavior' => [
                'class' => FileBehavior::className()
            ],
            'workflow' => [
                'class' => SimpleWorkflowBehavior::className(),
                'defaultWorkflowId' => self::PROJECTS_WORKFLOW,
                'propagateErrorsToModel' => true
            ],
            'ganttBehavior' => [
                'class' => \lispa\amos\gantt\behaviors\GanttBehavior::className(),
                'projects' => function ($Model) {
                    $ActivityArr = [];
                    /**@var Projects $Model * */
                    /**@var ProjectsActivities $Activity * */
                    foreach ($Model->projectsActivities as $Activity) {
                        $organization = !empty($Activity->organization->nameField) ? $Activity->organization->nameField : 'N/D';
                        $ActivityArr[] = [
                            'id' => $Activity->id,
                            'text' => $Activity->name,
                            'open' => true,
                            'custom' => [
                                'organization' => $organization
                            ],
                            'links' => [
                                'edit' => \Yii::$app->urlManager->createUrl([
                                    '/project_management/projects-activities/update',
                                    'id' => $Activity->id
                                ])
                            ]
                        ];
                    }
                    return $ActivityArr;
                },
                'tasks' => function ($Model) {
                    $TaskArr = [];
                    /**@var Projects $Model * */
                    /**@var ProjectsActivities $Activity * */
                    /**@var ProjectsTasks $Task * */
                    foreach ($Model->projectsActivities as $Activity) {

                        foreach ($Activity->projectsTasks as $Task) {
                            $startDate = new \DateTime($Task->start_date);

                            $organization = !empty($Task->organization->nameField) ? $Task->organization->nameField : 'N/D';

                            /**
                             * @var $model ProjectsTasks
                             */
                            $error = $Task->getHasDependencyError(true);
                            if ($error) {
                                $taskDependency = $Task->getFirstDependencyTaskError(true);
                                $icon = 'alert-triangle';
                                $class = 'text-danger';
                                $message = ProjectsTasksController::getRelationError($error, $taskDependency);
                            } else {
                                $icon = 'check-circle';
                                $class = 'text-success';
                                $message = Module::t('amosproject_management', 'Ok');
                            }

                            $TaskArr[] = [
                                'id' => $Task->id,
                                'text' => $Task->name,
                                'start_date' => $startDate->format("d-m-Y"),
                                'duration' => $Task->durationMinutes,
                                'parent' => "p-" . $Activity->id,
                                'custom' => [
                                    'organization' => $organization,
                                    'relationError' => $error ? AmosIcons::show($icon, [
                                        'data' => [
                                            'toggle' => "tooltip",
                                            'placement' => 'right',
                                        ],
                                        'title' => $message,
                                        'class' => $class,
                                        'style' => 'font-size: 20px;'
                                    ]) : ''
                                ],
                                'links' => [
                                    'edit' => \Yii::$app->urlManager->createUrl([
                                        '/project_management/projects-tasks/update',
                                        'id' => $Task->id
                                    ])
                                ]
                            ];
                        }
                    }
                    return $TaskArr;
                },
                'links' => function ($Model) {
                    $LinkArr = [];
                    /**@var Projects $Model * */
                    /**@var ProjectsActivities $Activity * */
                    /**@var ProjectsTasks $Task * */
                    foreach ($Model->projectsActivities as $Activity) {

                        foreach ($Activity->projectsTasks as $Task) {
                            /*
                            $LinkArr[] = [
                                'project_source_id' => $Activity->id,
                                'task_target_id' => $Task->id,
                                'type' => "0"
                            ];
*/
                            foreach ($Task->projectsRequiredTasksMms as $Ttask) {
                                $LinkArr[] = [
                                    'task_source_id' => $Ttask->projects_tasks_id,
                                    'task_target_id' => $Task->id,
                                    'type' => $Ttask->typeNumber
                                ];
                            }

                        }


                    }
                    return $LinkArr;
                },
                'ajaxCallback' => [
                    'onBeforeLinkDelete' => [
                        'url' => Yii::$app->getUrlManager()->createUrl(['/project_management/rest-link/remove']),
                        'method' => 'POST'
                    ],
                    'onBeforeLinkUpdate' => [
                        'url' => Yii::$app->getUrlManager()->createUrl(['/project_management/rest-link/update']),
                        'method' => 'POST'
                    ],
                    'onBeforeLinkAdd' => [
                        'url' => Yii::$app->getUrlManager()->createUrl(['/project_management/rest-link/create']),
                        'method' => 'POST'
                    ],
                ]
            ]
        ]);

        $cwhInstallata = \Yii::$app->getModule('cwh');
        if ($cwhInstallata !== null) {
            $cwhTaggable = [
                'interestingTaggable' => [
                    'class' => TaggableInterestingBehavior::className(),
                    // 'tagValuesAsArray' => false,
                    // 'tagRelation' => 'tags',
                    'tagValueAttribute' => 'id',
                    'tagValuesSeparatorAttribute' => ',',
                    'tagValueNameAttribute' => 'nome',
                    //'tagFrequencyAttribute' => 'frequency',
                ]
            ];

            $behaviors = ArrayHelper::merge($behaviors, $cwhTaggable);
        }

        return $behaviors;
    }


//    NEW logic applied to the creation of a project
//    /**
//     * @param bool $insert
//     * @return bool
//     */
//    public function beforeSave($insert)
//    {
//        if (!$this->id || !$this->community_id) {
//            $community = ProjectManagementUtility::createCommunity($this);
//            if (is_null($community)) {
//                return false;
//            }
//        } elseif ($this->community_id && $this->id) {
//            $ok = ProjectManagementUtility::updateCommunity($this->community, $this->name, $this->insights);
//            if (!$ok) {
//                return false;
//            }
//        }
//
//        return parent::beforeSave($insert);
//    }

    /**
     * @param $insert
     * @return bool
     * @throws Exception
     */
    public function beforeSave($insert)
    {
        if (empty($this->community_id)) {
            /** @var AmosCwh $moduleCwh */
            $moduleCwh = Yii::$app->getModule('cwh');
            if (isset($moduleCwh)) {
                $scope = $moduleCwh->getCwhScope();
                if (isset($scope['community'])) {
                    $this->community_id = $scope['community'];
                } else {
                    throw new Exception(Module::t('amosproject_management', 'CWH SCOPE not set, community not defined'));
                }
            } else {
                throw new Exception(Module::t('amosproject_management', 'CWH Module Not Available'));
            }
        }
        return parent::beforeSave($insert);
    }


    /**
     * @param bool $insert
     * @param array $changedAttributes
     * @throws \Exception
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
//        if ($this->community_id && $this->id) {
//            $ok = ProjectManagementUtility::duplicateLogoForCommunity($this, 'logo', Projects::className());
//            if (!$ok) {
//                throw new \Exception(Module::t('amosproject_management',
//                    "Impossibile duplicare il logo del progetto nel logo della community"));
//            }
//        }
        // assign ROLE PROJECT_MANAGER to the community manager of this community   A CHE SERVE ?????
        $pmRole = Yii::$app->authManager->getRole(self::ROLE_PROJECT_MANAGER);
        if (!Yii::$app->authManager->checkAccess(Yii::$app->user->id, self::ROLE_PROJECT_MANAGER)) {
            Yii::$app->authManager->assign($pmRole, Yii::$app->user->id);
        }
    }

    /**
     * @inheritdoc
     */
    public function getManagerRole()
    {
        return self::ROLE_PROJECT_MANAGER;
    }

    /**
     * @inheritdoc
     */
    public function getContextRoles()
    {
        $roles = [
            self::ROLE_PROJECT_MANAGER,
            self::ROLE_PARTICIPANT
        ];
        return $roles;
    }

    /**
     * @inheritdoc
     */
    public function getBaseRole()
    {
        return self::ROLE_PARTICIPANT;
    }

    /**
     * @inheritdoc
     */
    public function getRolePermissions($role)
    {
        switch ($role) {
            case self::ROLE_PARTICIPANT:
                return ['CWH_PERMISSION_CREATE'];
                break;
            case self::ROLE_PROJECT_MANAGER:
                return ['CWH_PERMISSION_CREATE', 'CWH_PERMISSION_VALIDATE'];
                break;
            default:
                return ['CWH_PERMISSION_CREATE'];
                break;
        }
    }

    /**
     * @inheritdoc
     */
    public function getCommunityModel()
    {
        return $this->community;
    }

    /**
     * @inheritdoc
     */
    public function getNextRole($role)
    {
        switch ($role) {
            case self::ROLE_PARTICIPANT:
                return self::ROLE_PARTNER_REFERENT;
                break;
            case self::ROLE_PARTNER_REFERENT:
                return self::ROLE_PROJECT_MANAGER;
                break;
            default:
                return self::ROLE_PARTICIPANT;
                break;
        }
    }

    /**
     * @inheritdoc
     */
    public function getPluginModule()
    {
        return 'project_management';
    }

    /**
     * @inheritdoc
     */
    public function getPluginController()
    {
        return 'projects';
    }

    /**
     * @inheritdoc
     */
    public function getRedirectAction()
    {
        return 'update';
    }

    public function getAdditionalAssociationTargetQuery($communityId)
    {
        /** @var Projects $project */
        $project = Projects::findOne(['community_id' => $communityId]);
        $userIds = [];
        foreach ($project->joinedOrganizations as $joinedOrganization) {
            foreach ($joinedOrganization->employees as $joinedOrganizationEmployee) {
                /** @var User $joinedOrganizationEmployee */
                $userIds[] = $joinedOrganizationEmployee->id;
            }
        }
        /** @var ActiveQuery $userQuery */
        $userQuery = User::find()->andWhere([User::tableName() . '.id' => $userIds]);
        $userCommunityIds = CommunityUserMm::find()->andWhere(['community_id' => $communityId])->select('user_id');
        $userQuery->andWhere(['not in', User::tableName() . '.id', $userCommunityIds]);
        $userQuery->joinWith('userProfile');
        $userQuery->orderBy(['cognome' => SORT_ASC, 'nome' => SORT_ASC]);
        return $userQuery;
    }

    public function getAssociationTargetQuery($communityId)
    {
        /** @var Projects $project */
        $project = Projects::findOne(['community_id' => $communityId]);
        $userIds = [];
        foreach ($project->joinedOrganizations as $joinedOrganization) {
            foreach ($joinedOrganization->employees as $joinedOrganizationEmployee) {
                /** @var User $joinedOrganizationEmployee */
                $userIds[] = $joinedOrganizationEmployee->id;
            }
        }

        $userCommunityIds = CommunityUserMm::find()->andWhere(['community_id' => $communityId])->select('user_id');
        /** @var ActiveQuery $userQuery */
        $userQuery = User::find()->andWhere(['not in', User::tableName() . '.id', $userIds]);
        $userQuery->andWhere(['not in', User::tableName() . '.id', $userCommunityIds]);
        $userQuery->joinWith('userProfile');
        $userQuery->orderBy(['cognome' => SORT_ASC, 'nome' => SORT_ASC]);
        return $userQuery;
    }

    public function getPluginModel()
    {

    }

    public function getActiveCommunityMembers()
    {
        return $this->community->getCommunityUserMms()->andWhere([CommunityUserMm::tableName() . '.status' => 'ACTIVE']);
    }

    public function getProjectTasks()
    {
        /**
         * @var $query ActiveQuery
         */
        $query = ProjectsTasks::find();

        $query->leftJoin('projects_tasks_projects_activities_mm',
            'projects_tasks_projects_activities_mm.projects_tasks_id = projects_tasks.id');
        $query->leftJoin('projects_activities',
            'projects_activities.id = projects_tasks_projects_activities_mm.projects_activities_id');
        $query->leftJoin('projects_activities_projects_mm',
            ' projects_activities_projects_mm.projects_activities_id = projects_activities.id');
        $query->leftJoin('projects', ' projects.id = projects_activities_projects_mm.projects_id');
        $query->andFilterWhere(['projects.id' => $this->id]);

        return $query;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCommunityUserMm()
    {
        return $this->hasMany(CommunityUserMm::className(), ['community_id' => 'community_id']);
    }

    /**
     * Retrieve community role of the user identified by user id param.
     * @param int $userId
     * @return string
     * @throws \Exception
     */
    public function getRawCommunityRoleByUserId($userId)
    {
        if (!is_numeric($userId)) {
            throw new \Exception(Module::t('amosproject_management', 'Missing user id in' . __FUNCTION__));
        }
        $communityUser = $this->getCommunityUserMm()->andWhere(['user_id' => $userId])->one();
        return (!is_null($communityUser) ? $communityUser->role : '');
    }

    /**
     * @return string
     */
    public function getPluginWidgetClassname()
    {
        return WidgetIconProjectsActivities::className();
    }

    /**
     * @return ActiveQuery
     */
    public function getCosts()
    {
        /**
         * @var $query ActiveQuery
         */
        $query = ProjectsTasksCosts::find();
        $query->leftJoin('projects_tasks', 'projects_tasks.id = projects_tasks_costs.projects_tasks_id');
        $query->leftJoin('projects_tasks_projects_activities_mm',
            'projects_tasks_projects_activities_mm.projects_tasks_id = projects_tasks.id');
        $query->leftJoin('projects_activities',
            'projects_activities.id = projects_tasks_projects_activities_mm.projects_activities_id');
        $query->leftJoin('projects_activities_projects_mm',
            'projects_activities_projects_mm.projects_activities_id = projects_activities.id');
        $query->leftJoin('projects', 'projects.id = projects_activities_projects_mm.projects_id');
        $query->andWhere(['projects.id' => $this->id]);

        return $query;
    }

    /**
     * @param $costItemId
     * @param $organizationId
     * @return array|null|ProjectsTasksCosts
     */
    public function getCostRow($costItemId, $organizationId)
    {
        $query = $this->getCosts();
        $query->select(['SUM(value) valueSumm']);
        $query->andFilterWhere([
            'projects_custom_spending_voices_id' => $costItemId,
            'organization_id' => $organizationId
        ]);
        //pr($query->createCommand()->getRawSql());
        $result = $query->asArray()->one();

        return $result;
    }

    /**
     * @return mixed
     */
    public function getGrammar()
    {
        return new ProjectsGrammar();
    }

    /**
     * @return mixed
     */
    public function getFullViewUrl()
    {
        return Url::toRoute(["/" . $this->getViewUrl(), "id" => $this->id]);
    }

    /**
     * @inheritdoc
     */
    public function getViewUrl()
    {
        return "project_management/projects/view";
    }

    /**
     * @return Community|null
     * @throws Exception
     */
    public function getCommunityFromScope()
    {
        $community = null;
        /** @var AmosCwh $moduleCwh */
        $moduleCwh = Yii::$app->getModule('cwh');
        if (isset($moduleCwh)) {
            $scope = $moduleCwh->getCwhScope();
            if (isset($scope['community'])) {
                /** @var Community $community */
                $community = Community::findOne($scope['community']);
                if (empty($community)) {
                    throw new Exception(Module::t('amosproject_management', 'Community parent not exist...'));
                }
            } else {
                throw new Exception(Module::t('amosproject_management', 'CWH SCOPE not set, community not defined'));
            }
        } else {
            throw new Exception(Module::t('amosproject_management', 'CWH Module Not Available'));
        }
        return $community;
    }

    /**
     * @param $projectId
     * @return array
     */
    public static function getItemsWorkPlanArray($projectId, $checkOwner = false, $taskInSelection = [])
    {
        $ret = [];
        $project = Projects::findOne($projectId);
        if (!empty($project)) {
            /** @var ProjectsActivities[] $listOfActivities */
            $listOfActivities = $project->projectsActivities;

            ArrayHelper::multisort($listOfActivities, 'created_at', SORT_ASC);

            /** @var ProjectsActivities $activity */
            foreach ($listOfActivities as $activity) {
                if ($activity instanceof ItemWorkPlanInterface) {
                    $resultActivityKey = StringHelper::basename($activity->className()) . $activity->id;
                    $listOfTasks = $activity->projectsTasks;
                    /** @var ProjectsTasks $task */
                    foreach ($listOfTasks as $task) {
                        if ($task instanceof ItemWorkPlanInterface) {
                            if(!$checkOwner || (!is_null($task->projectsActivities->user) && (Yii::$app->user->id == $task->projectsActivities->user->id)) || (!is_null($task->user) && (Yii::$app->user->id == $task->user->id))) {
                                if(!array_key_exists($resultActivityKey, $ret)){
                                    $ret[$resultActivityKey]['class'] = $activity->className();
                                    $ret[$resultActivityKey]['id'] = $activity->id;
                                }
                                if(empty($taskInSelection) || in_array($task->id, $taskInSelection)) {
                                    $ret[StringHelper::basename($task->className()) . $task->id]['class'] = $task->className();
                                    $ret[StringHelper::basename($task->className()) . $task->id]['id'] = $task->id;
                                }
                            }
                        }
                    }
                }
            }
        }
        return $ret;
    }

    public function getProjectItemsWorkPlanArray()
    {
        return self::getItemsWorkPlanArray($this->id);
    }

    public static function getAllResponsiblesUsersWorkPaln($projectId)
    {
        $listOfItems = Projects::getItemsWorkPlanArray($projectId);
        $ids = [];
        foreach ($listOfItems as $item) {
            $class = $item['class'];
            $id = $item['id'];
            $obj = $class::findOne($id);
            $ids[] = $obj->getUserResponsibleId();
        }
        return $ids;
    }

    public function getProjectAllResponsiblesUsersWorkPaln()
    {
        return self::getAllResponsiblesUsersWorkPaln($this->id);
    }

    public function getNumberAllOrganizations()
    {
        $listOfItems = Projects::getItemsWorkPlanArray($this->id);
        $ids = [];
        foreach ($listOfItems as $item) {
            $class = $item['class'];
            $id = $item['id'];
            $obj = $class::findOne($id);
            if (!is_null($obj->getOrganizationResponsibleId())) {
                $ids[] = $obj->getOrganizationResponsibleId();
            }
        }
        /** @var \lispa\amos\projectmanagement\models\ProjectsTasks $task */
        foreach ($this->getProjectTasks()->all() as $task) {
            foreach ($task->joinedOrganizations as $mm) {
                $ids[] = $mm->id;
            }
        }
        return count(array_unique($ids));
    }

    /**
     * @param integer $customSpendingVoicesId
     * @return bool
     */
    public function hasLinkedCosts($customSpendingVoicesId)
    {
        $query = new Query();
        $query->from(ProjectsTasksCosts::tableName());
        $query->where(['deleted_at' => null]);
        $query->andWhere(['projects_custom_spending_voices_id' => $customSpendingVoicesId]);
        $total = $query->sum('value');
        return ($total != 0);
    }

    public function hasCustomSpendingVoices() {
        $spendingVoices = ProjectsCustomSpendingVoices::find()
                            ->andWhere(['projects_id' => $this->id])
                            ->asArray()
                            ->all();
        return count($spendingVoices) != 0;
    }

    public function beforeValidate()
    {
        if($this->status == Projects::PROJECTS_WORKFLOW_STATUS_VALIDATED) {
            if (!$this->hasCustomSpendingVoices()) {
                \Yii::$app->session->addFlash('danger', Module::t('amosproject_management', '#project_without_spending_voices_error'));
                $this->addError('spending_voices', Module::t('amosproject_management', '#project_without_spending_voices_error'));
                return false;
            }
        }
        return parent::beforeValidate(); // TODO: Change the autogenerated stub
    }

    /**
     * @return bool
     */
    public function hasTasks(){
        $activities = $this->projectsActivities;
        if(count($this->projectsActivities) == 0) {
            return false;
        }

        /** @var  $activity ProjectsActivities*/
        $count = 0;
        foreach ($activities as $activity){
            $count += count($activity->projectsTasks);
        }
        return $count > 0;
    }

    /**
     * @return OrganizationsModelInterface[]
     */
    public function getProjectOrganizations()
    {
        $wps = $this->projectsActivities;
        $projectOrganizations = [];
        foreach ($wps as $wp) {
            $tasks = $wp->projectsTasks;
            foreach ($tasks as $task) {
                foreach ($task->joinedOrganizations as $organization) {
                    $projectOrganizations[$organization->id] = $organization;
                }
            }
        }
        return $projectOrganizations;
    }

    public function getActivitiesReferentUserIds()
    {
        $userIds = [];
        $wps = $this->projectsActivities;
        foreach ($wps as $wp) {
            $wpReferentId = $wp->getProjectsActivitiesOrganizationMms()->select('user_id')->column();
            $userIds = ArrayHelper::merge($userIds, $wpReferentId);
            $wpTaskRefentIds = $wp->getTasksOrganizationMms()->select('user_id')->column();
            $userIds = ArrayHelper::merge($userIds, $wpTaskRefentIds);
        }
        return $userIds;
    }

    public function getProjectManagerIds()
    {
        return $this->getCommunityUserMms()->andWhere(['role' => [self::ROLE_PROJECT_MANAGER, CommunityUserMm::ROLE_COMMUNITY_MANAGER]])->select(CommunityUserMm::tableName().'.user_id')->column();
    }

    /**
     * @param $role
     * @return string
     */
    public static function getPmRole($role){
        if($role == 'COMMUNITY_MANAGER' || $role == 'PROJECT_MANAGER'){
            $role = AmosCommunity::t('amoscommunity', Projects::COMMUNITY_MANAGER_ROLE_NAME);
        }
        else {
            $role = $role;
        }
        return $role;
    }

    /**
     * @return bool
     */
    public function canStartProject(){
        if(in_array($this->status, [Projects::PROJECTS_WORKFLOW_STATUS_ONEDIT, Projects::PROJECTS_WORKFLOW_STATUS_STARTUP]) && ProjectManagementUtility::loggedUsedIsCommunityManager($this->community_id)){
            return true;
        }
        else return false;
    }

    /**
     * @inheritdoc
     */
    public function getWorkflowStatusLabel()
    {
        return Module::t('amosproject_management', parent::getWorkflowStatusLabel());
    }
}
