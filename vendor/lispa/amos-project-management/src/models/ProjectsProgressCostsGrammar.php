<?php
namespace lispa\amos\projectmanagement\models;

use lispa\amos\projectmanagement\Module;
use lispa\amos\core\interfaces\ModelGrammarInterface;

class ProjectsProgressCostsGrammar implements ModelGrammarInterface
{

    /**
     * @return string
     */
    public function getModelSingularLabel()
    {
        return Module::t('amosproject_management', 'Projects Progress Costs');
    }

    /**
     * @inheritdoc
     */
    public function getModelLabel()
    {
        return Module::t('amosproject_management', 'Projects Progress Costs');
    }

    /**
     * @return mixed
     */
    public function getArticleSingular()
    {
        return Module::t('amosproject_management', 'la');
    }

    /**
     * @return mixed
     */
    public function getArticlePlural()
    {
        return Module::t('amosproject_management', 'le');
    }

    /**
     * @return string
     */
    public function getIndefiniteArticle()
    {
        return Module::t('amosproject_management', 'una');
    }
}