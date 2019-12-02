<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\sondaggi\models\search
 * @category   CategoryName
 */

namespace lispa\amos\sondaggi\models\search;

use lispa\amos\notificationmanager\base\NotifyWidget;
use lispa\amos\notificationmanager\base\NotifyWidgetDoNothing;
use lispa\amos\notificationmanager\models\NotificationChannels;
use lispa\amos\sondaggi\models\Sondaggi;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\di\Container;

/**
 * SondaggiSearch represents the model behind the search form about `lispa\amos\sondaggi\models\Sondaggi`.
 */
class SondaggiSearch extends Sondaggi
{
    /**
     * @var Container $container
     */
    private $container;

    /**
     * @inheritdoc
     */
    public function __construct(array $config = [])
    {
        $this->container = new Container();
        $this->container->set('notify', new NotifyWidgetDoNothing());
        $this->isSearch = true;
        parent::__construct($config);
    }

    /**
     * @return object
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    public function getNotifier()
    {
        return $this->container->get('notify');
    }

    /**
     * @param $notifier
     */
    public function setNotifier(NotifyWidget $notifier)
    {
        $this->container->set('notify', $notifier);
    }

    /**
     * @param ActiveQuery $query
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    private function notificationOff($query)
    {
        $notify = $this->getNotifier();
        if ($notify) {
            /** @var \lispa\amos\notificationmanager\AmosNotify $notify */
            $notify->notificationOff(\Yii::$app->getUser()->id, Sondaggi::className(), $query, NotificationChannels::CHANNEL_READ);
        }
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'filemanager_mediafile_id', 'created_by', 'updated_by', 'deleted_by', 'version'], 'integer'],
            [['titolo', 'descrizione', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

//    /**
//     * @param array $params Search parameters
//     * @return \yii\db\ActiveQuery
//     */
//    public function baseSearch($params)
//    {
//        // Init the default search values
//        $this->initOrderVars();
//
//        // Check params to get orders value
//        $this->setOrderVars($params);
//
//        /** @var \yii\db\ActiveQuery $baseQuery */
//        $baseQuery = Sondaggi::find();
//
//        return $baseQuery;
//    }

    /**
     * Base filter.
     * @param ActiveQuery $query
     * @return mixed
     */
    public function baseFilter($query)
    {
        $query->andFilterWhere([
            'id' => $this->id,
            'filemanager_mediafile_id' => $this->filemanager_mediafile_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'deleted_by' => $this->deleted_by,
            'version' => $this->version,
        ]);

        $query->andFilterWhere(['like', 'titolo', $this->titolo]);
        $query->andFilterWhere(['like', 'descrizione', $this->descrizione]);

        return $query;
    }

    /**
     * @inheritdoc
     */
    public function searchFieldsMatch()
    {
        return [
            'id',
            'filemanager_mediafile_id',
            'created_at',
            'updated_at',
            'deleted_at',
            'created_by',
            'updated_by',
            'deleted_by',
            'version',
        ];
    }

    /**
     * @inheritdoc
     */
    public function searchFieldsLike()
    {
        return [
            'titolo',
            'descrizione',
        ];
    }

    /**
     * @inheritdoc
     */
    public function searchOwnInterest($params, $limit = null)
    {
        $dataProvider = parent::searchOwnInterest($params, $limit);
        $dataProvider->query->orderBy(['created_at' => SORT_DESC]);
        return $dataProvider;
    }

    /**
     * @param array $params Search parameters
     * @return ActiveDataProvider
     */
    public function searchDominio($params)
    {
        $query = $this->baseSearch($params);

//        $configurazioneAccessi = \backend\modules\peipoint\models\PeiAccessiServiziFacilitazioneConfigurazioneSondaggi::find();
//        $sondaggiAccessi = [];
//        if($configurazioneAccessi->count()){
//            foreach ($configurazioneAccessi->all() as $ConfAccesso){
//                $sondaggiAccessi[] = $ConfAccesso['sondaggi_id'];
//            }
//        }        
//        $query->andWhere(['NOT IN', 'id', $sondaggiAccessi]);
        $utente = \Yii::$app->getUser();
        $ruoli_utente = \Yii::$app->authManager->getRolesByUser($utente->getId());

        //ruolo pubblico sempre visibile
        $Ruoli = ['PUBBLICO'];
        foreach ($ruoli_utente as $Ruolo) {
            $Ruoli[] = $Ruolo->name;
        }

        $query->orderBy('sondaggi.created_at DESC');
        $query->andWhere(['status' => self::WORKFLOW_STATUS_VALIDATO]);
        $query->innerJoinWith('sondaggiPubblicaziones');
        if (!$utente->can('AMMINISTRAZIONE_SONDAGGI')) {
            $query->andWhere(['IN', 'ruolo', $Ruoli]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->notificationOff($query);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $this->baseFilter($query);

        return $dataProvider;
    }

    /**
     * @param array $params Search parameters
     * @return ActiveDataProvider
     */
    public function searchPartecipato($params)
    {
        $query = $this->baseSearch($params);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $this->notificationOff($query);
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        $this->baseFilter($query);
        return $dataProvider;
    }

    /**
     * @param array $params Search parameters
     * @return ActiveQuery
     */
    public function searchSondaggiNonPartecipato($params)
    {
        //$query = $this->baseSearch($params);
        $query = $this->buildQuery($params, 'admin-scope');
        //$query->joinWith('sondaggiRisposteSessionis');
        //$query->andWhere(['begin_date' => null]);
        $this->notificationOff($query);
        return $query;
    }
    
    /**
     * 
     * @param type $params
     * @param type $limit
     * @return type
     */
    public function ultimiSondaggi($params, $limit = null) {
        $dataProvider = $this->searchAll($params, $limit);
        return $dataProvider;
    }

    /**
     * 
     * @param type $params
     * @param type $limit
     * @return type
     */
    public function searchAll($params, $limit = null) {
        return $this->search($params, "all", $limit);
    }
}
