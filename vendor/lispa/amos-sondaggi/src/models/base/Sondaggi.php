<?php
/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\sondaggi\models\base
 * @category   CategoryName
 */

namespace lispa\amos\sondaggi\models\base;

use lispa\amos\core\record\ContentModel;
use lispa\amos\notificationmanager\record\NotifyRecord;
use lispa\amos\sondaggi\AmosSondaggi;

/**
 * Class Sondaggi
 *
 * This is the base-model class for table "sondaggi".
 *
 * @property integer $id
 * @property string $status
 * @property string $titolo
 * @property string $descrizione
 * @property integer $send_pdf_via_email
 * @property integer $additional_emails
 * @property integer $compilazioni_disponibili
 * @property integer $sondaggi_stato_id
 * @property integer $filemanager_mediafile_id
 * @property integer $sondaggi_temi_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $deleted_by
 * @property integer $version
 *
 * @property \lispa\amos\upload\models\FilemanagerMediafile $filemanagerMediafile
 * @property \lispa\amos\sondaggi\models\SondaggiStato $sondaggiStato
 * @property \lispa\amos\sondaggi\models\SondaggiTemi $sondaggiTemi
 * @property \lispa\amos\sondaggi\models\SondaggiDomande[] $sondaggiDomandes
 * @property \lispa\amos\sondaggi\models\SondaggiDomandePagine[] $sondaggiDomandePagines
 * @property \lispa\amos\sondaggi\models\SondaggiRisposteSessioni[] $sondaggiRisposteSessionis
 * @property \lispa\amos\sondaggi\models\SondaggiPubblicazione[] $sondaggiPubblicaziones
 *
 * @package lispa\amos\sondaggi\models\base
 */
abstract class Sondaggi extends ContentModel
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sondaggi';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
    return [
    [['titolo'], 'required'],
    [['titolo', 'descrizione', 'additional_emails', 'thank_you_page', 'url_sondaggio_non_compilabile', 'url_chiudi_sondaggio', 'link_landing_page', 'testo_sondaggio_non_compilabile_front', 'titolo_fine_sondaggio_front', 'testo_fine_sondaggio_front', 'mail_mittente_nuovo_utente', 'mail_soggetto_nuovo_utente', 'mail_contenuto_nuovo_utente', 'mail_mittente_utente_presente', 'mail_soggetto_utente_presente', 'mail_contenuto_utente_presente', 'mail_registrazione_mittente', 'mail_registrazione_soggetto', 'mail_registrazione_corpo', 'mail_conf_community_mittente', 'mail_conf_community_soggetto', 'mail_conf_community_corpo', 'forza_lingua'], 'string'],
    [['status'], 'string', 'max' => 255],
    [['compilazioni_disponibili', 'send_pdf_via_email', 'sondaggi_stato_id', 'filemanager_mediafile_id', 'sondaggi_temi_id', 'created_by',
    'updated_by', 'deleted_by', 'version', 'send_pdf_via_email', 'frontend', 'abilita_registrazione', 'mail_registrazione_custom', 'mail_conf_community', 'mail_conf_community_id', 'abilita_criteri_valutazione', 'n_max_valutatori',], 'integer'],
    [['created_at', 'updated_at', 'deleted_at'], 'safe']
    ];
}

/**
 * @inheritdoc 
 */
public function attributeLabels()
{ 
    return [
        'id' => AmosSondaggi::t('amossondaggi', 'ID'),
        'status' => AmosSondaggi::t('amossondaggi', 'Status'), 
        'titolo' => AmosSondaggi::t('amossondaggi', 'Titolo'),
        'descrizione' => AmosSondaggi::t('amossondaggi', 'Descrizione'),
        'forza_lingua' => AmosSondaggi::t('amossondaggi', 'Forza la lingua'),
        'thank_you_page' => AmosSondaggi::t('amossondaggi', 'Url della "Thank you page"'),
        'url_chiudi_sondaggio' => AmosSondaggi::t('amossondaggi', 'Url a cui redirigere al click su "Chiudi"'),
        'url_sondaggio_non_compilabile' => AmosSondaggi::t('amossondaggi',
            'Url della pagina di avviso di "Sondaggio non compilabile"'),
        'abilita_registrazione' => AmosSondaggi::t('amossondaggi',
            'Abilita registrazione automatica al termine del sondaggio'),
        'compilazioni_disponibili' => AmosSondaggi::t('amossondaggi', 'Compilazioni disponibili'),
        'additional_emails' => AmosSondaggi::t('amossondaggi', 'Altre email'),
        'send_pdf_via_email' => AmosSondaggi::t('amossondaggi',
            ' Al termine del sondaggio, invia il pdf compilato via email'),
        'sondaggi_stato_id' => AmosSondaggi::t('amossondaggi', 'Stato'),
        'filemanager_mediafile_id' => AmosSondaggi::t('amossondaggi', 'Immagine'), 
        'sondaggi_temi_id' => AmosSondaggi::t('amossondaggi', 'Tema'),
        'frontend' => AmosSondaggi::t('amossondaggi', 'Sondaggio compilabile in frontend'),
        'link_landing_page' => AmosSondaggi::t('amossondaggi', 'Url della Landing Page'),
        'testo_sondaggio_non_compilabile_front' => AmosSondaggi::t('amossondaggi', 'Testo in caso di sondaggio non compilabile'),
        'titolo_fine_sondaggio_front' => AmosSondaggi::t('amossondaggi', 'H1 della "thank you page"'),
        'testo_fine_sondaggio_front' => AmosSondaggi::t('amossondaggi', 'Messaggio della "thank you page"'),
        'mail_mittente_nuovo_utente' => AmosSondaggi::t('amossondaggi', 'Mittente (il nome visualizzato sarà il testo dopo il primo spazio)'),
        'mail_soggetto_nuovo_utente' => AmosSondaggi::t('amossondaggi', 'Soggetto'),
        'mail_contenuto_nuovo_utente' => AmosSondaggi::t('amossondaggi', 'Testo'),
        'mail_mittente_utente_presente' => AmosSondaggi::t('amossondaggi', 'Mittente'),
        'mail_soggetto_utente_presente' => AmosSondaggi::t('amossondaggi', 'Soggetto'),
        'mail_contenuto_utente_presente' => AmosSondaggi::t('amossondaggi', 'Testo'),
        'mail_registrazione_custom' => AmosSondaggi::t('amossondaggi', 'Abilita invio e-mail personalizzata per le nuove registrazioni'),
        'mail_registrazione_mittente' => AmosSondaggi::t('amossondaggi', 'Mittente'),
        'mail_registrazione_soggetto' => AmosSondaggi::t('amossondaggi', 'Soggetto'),
        'mail_registrazione_corpo' => AmosSondaggi::t('amossondaggi', 'Testo'),
        'mail_conf_community' => AmosSondaggi::t('amossondaggi', 'Abilita invio mail conferma registrazione alla community (al primo accesso in piattaforma)'),
        'mail_conf_community_id' => AmosSondaggi::t('amossondaggi', 'Id della Community'),
        'mail_conf_community_mittente' => AmosSondaggi::t('amossondaggi', 'Mittente'),
        'mail_conf_community_soggetto' => AmosSondaggi::t('amossondaggi', 'Soggetto'),
        'mail_conf_community_corpo' => AmosSondaggi::t('amossondaggi', 'Testo'),
        'created_at' => AmosSondaggi::t('amossondaggi', 'Creato il'),
        'updated_at' => AmosSondaggi::t('amossondaggi', 'Aggiornato il'),
        'deleted_at' => AmosSondaggi::t('amossondaggi', 'Cancellato il'),
        'created_by' => AmosSondaggi::t('amossondaggi', 'Creato da'),
        'updated_by' => AmosSondaggi::t('amossondaggi', 'Aggiornato da'),
        'deleted_by' => AmosSondaggi::t('amossondaggi', 'Cancellato da'),
        'version' => AmosSondaggi::t('amossondaggi', 'Versione'),
        'abilita_criteri_valutazione' => AmosSondaggi::t('amossondaggi', 'Abilita criteri di valutazione'),
        'n_max_valutatori' => AmosSondaggi::t('amossondaggi', 'N. max di valutatori (0 = senza limiti)'),
    ];
}

/**
 * @return \yii\db\ActiveQuery
 */
public function getFilemanagerMediafile()
{
    return $this->hasOne(\lispa\amos\upload\models\FilemanagerMediafile::className(),
            ['id' => 'filemanager_mediafile_id']);
}

/**
 * @return \yii\db\ActiveQuery
 */
public function getSondaggiStato()
{
    return $this->hasOne(\lispa\amos\sondaggi\models\SondaggiStato::className(), ['id' => 'sondaggi_stato_id']);
}

/**
 * @return \yii\db\ActiveQuery
 */
public function getSondaggiTemi()
{
    return $this->hasOne(\lispa\amos\sondaggi\models\SondaggiTemi::className(), ['id' => 'sondaggi_temi_id']);
}

/**
 * @return \yii\db\ActiveQuery
 */
public function getSondaggiDomandes()
{
    return $this->hasMany(\lispa\amos\sondaggi\models\SondaggiDomande::className(), ['sondaggi_id' => 'id'])->andWhere([
            \lispa\amos\sondaggi\models\SondaggiDomande::tableName().'.deleted_at' => null]);
}

/**
 * @return \yii\db\ActiveQuery
 */
public function getSondaggiDomandesWithFiles()
{
    return $this->hasMany(\lispa\amos\sondaggi\models\SondaggiDomande::className(), ['sondaggi_id' => 'id'])->andWhere([
            'OR', ['sondaggi_domande_tipologie_id' => 10], ['sondaggi_domande_tipologie_id' => 11]]);
}

/**
 * @return \yii\db\ActiveQuery
 */
public function getSondaggiDomandePagines()
{
    return $this->hasMany(\lispa\amos\sondaggi\models\SondaggiDomandePagine::className(),
            ['sondaggi_id' => 'id'])
        ->andWhere([\lispa\amos\sondaggi\models\SondaggiDomandePagine::tableName().'.deleted_at' => null]);
}

/**
 * @return \yii\db\ActiveQuery
 */
public function getSondaggiRisposteSessionis()
{
    return $this->hasMany(\lispa\amos\sondaggi\models\SondaggiRisposteSessioni::className(),
            ['sondaggi_id' => 'id']);
}

/**
 * @return \yii\db\ActiveQuery
 */
public function getSondaggiPubblicaziones()
{
    return $this->hasMany(\lispa\amos\sondaggi\models\SondaggiPubblicazione::className(),
            ['sondaggi_id' => 'id']);
}

/**
 * @return \yii\db\ActiveQuery
 */
public function getPeiAttivitaFormatives()
{
    return $this->hasMany(\backend\modules\attivitaformative\models\PeiAttivitaFormative::className(),
            ['id' => 'entita_id'])->via('sondaggiPubblicaziones');
}
}