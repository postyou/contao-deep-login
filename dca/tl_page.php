<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @package   deepLogin
 * @author    Gerald Meier
 * @license   LGPL
 * @copyright Postyou 2016
 */


$GLOBALS['TL_DCA']['tl_page']['palettes']['login'] = '
{title_legend},title,alias,type;
{meta_legend},pageTitle,robots,description;
{layout_legend:hide},includeLayout;
{cache_legend:hide},includeCache;
{chmod_legend:hide},includeChmod;
{search_legend},noSearch;
{expert_legend:hide},cssClass,sitemap,hide,guests;
{tabnav_legend:hide},tabindex,accesskey;
{publish_legend},published';


$GLOBALS['TL_DCA']['tl_page']['list']['operations']['articles']['button_callback']=array('tl_page_deep_login', 'editArticles');

foreach($GLOBALS['TL_DCA']['tl_page']['config']['onsubmit_callback'] as $key=>&$callbacks){
    if(in_array("generateArticle",$callbacks)){
        $GLOBALS['TL_DCA']['tl_page']['config']['onsubmit_callback'][$key]=array('tl_page_deep_login', 'generateArticle');
    }
}
$GLOBALS['TL_DCA']['tl_page']['config']['onsubmit_callback'][]=array('tl_page_deep_login', 'generateLoginModule');
$GLOBALS['TL_DCA']['tl_page']['config']['onsubmit_callback'][]=array('tl_page_deep_login', 'generate403Page');

Class tl_page_deep_login extends \Backend {

    /**
     * Import the back end user object
     */
    public function __construct()
    {
        parent::__construct();
        $this->import('BackendUser', 'User');
    }

    public function editArticles($row, $href, $label, $title, $icon)
    {
        if (!$this->User->hasAccess('article', 'modules'))
        {
            return '';
        }

        return ($row['type'] == 'regular' ||
                $row['type'] == 'login' ||
                $row['type'] == 'error_403' ||
                $row['type'] == 'error_404'
        ) ? '<a href="' .
            $this->addToUrl($href.'&amp;pn='.$row['id']) .
            '" title="'.specialchars($title).'">'.
            Image::getHtml($icon, $label).'</a> ' : Image::getHtml(preg_replace('/\.gif$/i', '_.gif', $icon)). ' ';
    }

    public function generateArticle(DataContainer $dc)
    {
        // Return if there is no active record (override all)
        if (!$dc->activeRecord)
        {
            return;
        }

        // Existing or not a regular page
        if ($dc->activeRecord->tstamp > 0 || !in_array($dc->activeRecord->type, array(
            'regular', 'error_403', 'error_404','login')))
        {
            return;
        }

        $new_records = $this->Session->get('new_records');

        // Not a new page
        if (!$new_records || (is_array($new_records[$dc->table]) && !in_array($dc->id, $new_records[$dc->table])))
        {
            return;
        }

        // Check whether there are articles (e.g. on copied pages)
        $objTotal = $this->Database->prepare("SELECT COUNT(*) AS count FROM tl_article WHERE pid=?")
            ->execute($dc->id);

        if ($objTotal->count > 0)
        {
            return;
        }

        // Create article
        $arrSet['pid'] = $dc->id;
        $arrSet['sorting'] = 128;
        $arrSet['tstamp'] = time();
        $arrSet['author'] = $this->User->id;
        $arrSet['inColumn'] = 'main';
        $arrSet['title'] = $dc->activeRecord->title;
        $arrSet['alias'] = str_replace('/', '-', $dc->activeRecord->alias); // see #5168
        if($dc->activeRecord->type!='login')
            $arrSet['published'] = $dc->activeRecord->published;
        else
            $arrSet['published'] = "1";

        $this->Database->prepare("INSERT INTO tl_article %s")->set($arrSet)->execute();
    }

    public function generateLoginModule(DataContainer $dc){
        // Return if there is no active record (override all)
        if (!$dc->activeRecord  || $dc->activeRecord->tstamp > 0 || $dc->activeRecord->type!='login')
        {
            return;
        }
        $new_records = $this->Session->get('new_records');

        // Not a new page
        if (!$new_records || (is_array($new_records[$dc->table]) && !in_array($dc->id, $new_records[$dc->table])))
        {
            return;
        }

        $artRes = $this->Database->prepare("SELECT id FROM tl_article WHERE pid=?")
            ->execute($dc->id);

        if (count($artRes)==0)
        {
            $this->generateArticle($dc);
            $artRes = $this->Database->prepare("SELECT id FROM tl_article WHERE pid=?")
                ->execute($dc->id);
        }

        $objTotal = $this->Database->prepare("SELECT COUNT(*) AS count FROM tl_article,tl_content,tl_module WHERE tl_article.pid=? AND tl_content.pid=tl_article.id AND tl_content.type='module' AND tl_module.id=tl_content.module AND tl_module.type='deep_login' ")
            ->execute($dc->id);

        if ($objTotal->count>0)
        {
            return;
        }else{
            $this->addLoginModuleCE($artRes->id);
        }
    }
    private function addLoginModuleCE($articleID){

        $arrSet['pid'] = $articleID;
        $arrSet['sorting'] = 128;
        $arrSet['tstamp'] = time();
        $arrSet['type'] = 'module';
        $arrSet['ptable'] = 'tl_article';
        $arrSet['module'] = $this->addModule();
        $this->Database->prepare("INSERT INTO tl_content %s")->set($arrSet)->execute();
        \Contao\Message::addInfo("ContentElement for Login Module created.");

    }



    private function addModule(){
        $moduleId=0;
        $moduleIdRes = $this->Database->prepare("SELECT id FROM tl_module WHERE tl_module.type='deep_login' ")->execute();
        if (count($moduleIdRes)>0)
        {
            return $moduleIdRes->id;
        }else{
            $themeRes = $this->Database->prepare("SELECT id FROM tl_theme LIMIT 1 ORDER BY id")->execute();
            $arrSet['pid'] = $themeRes->id;
            $arrSet['tstamp'] = time();
            $arrSet['type'] = 'deep_login';
            $objInsertStmt=$this->Database->prepare("INSERT INTO tl_module %s")->set($arrSet)->execute();
            if ($objInsertStmt->affectedRows) {
                $moduleId = $objInsertStmt->insertId;
                \Contao\Message::addInfo("Login-Module created.");
            }
        }
        return $moduleId;
    }

    public function generate403Page($dc){
        // Return if there is no active record (override all)
        if (!$dc->activeRecord  || $dc->activeRecord->tstamp > 0 || $dc->activeRecord->type!='login')
        {
            return;
        }
        $new_records = $this->Session->get('new_records');

        // Not a new page
        if (!$new_records || (is_array($new_records[$dc->table]) && !in_array($dc->id, $new_records[$dc->table])))
        {
            return;
        }

        // Check whether there are articles (e.g. on copied pages)
        $objTotal = $this->Database->prepare("SELECT COUNT(*) AS count FROM tl_page WHERE type='error_403'")
            ->execute($dc->id);

        if ($objTotal->count > 0)
        {
            return;
        }else{
            $arrSet['pid'] = $dc->activeRecord->pid;
            $arrSet['sorting'] = 0;
            $arrSet['tstamp'] = time();
            $arrSet['type'] = "error_403";
            $arrSet['title'] = "403 Zugriff verweigert";
            $arrSet['alias'] = "403-zugriff-verweigert"; // see #5168
            $arrSet['published'] = "1";
            $this->Database->prepare("INSERT INTO tl_page %s")->set($arrSet)->execute();
            \Contao\Message::addInfo("Created 403-error page.");
        }

    }

}