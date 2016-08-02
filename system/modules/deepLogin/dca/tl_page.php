<?php
$GLOBALS['TL_DCA']['tl_page']['palettes']['login'] = '
{title_legend},title,alias,type;
{meta_legend},pageTitle,robots,description;
{layout_legend:hide},includeLayout;
{cache_legend:hide},includeCache;
{chmod_legend:hide},includeChmod;
{search_legend},noSearch;
{expert_legend:hide},cssClass,sitemap,hide,guests;
{tabnav_legend:hide},tabindex,accesskey;
{publish_legend},published,start,stop';

$GLOBALS['TL_DCA']['tl_page']['fields']['guests']['load_callback'][]=array('tl_page_deep_login', 'check4LoginPageType');

$GLOBALS['TL_DCA']['tl_page']['list']['operations']['articles']['button_callback']=array('tl_page_deep_login', 'editArticles');

foreach($GLOBALS['TL_DCA']['tl_page']['config']['onsubmit_callback'] as $key=>&$callbacks){
    if(in_array("generateArticle",$callbacks)){
        $GLOBALS['TL_DCA']['tl_page']['config']['onsubmit_callback'][$key]=array('tl_page_deep_login', 'generateArticle');
    }
}

Class tl_page_deep_login extends \Backend {

    /**
     * Import the back end user object
     */
    public function __construct()
    {
        parent::__construct();
        $this->import('BackendUser', 'User');
    }

    public function loadCallBack($varValue,$dc){
        $res=$dc->Database->prepare("Select autoforward from tl_page where id=?")->execute($dc->id)->fetchAssoc();
        return $res['autoforward'];
    }
    public function saveCallBack($varValue,$dc){
        $res=$dc->Database->prepare("Update tl_page set autoforward=? where id=?")->execute($varValue,$dc->id);
        return null;
    }


    function check4LoginPageType($varValue,$dc){
        if($dc->activeRecord->type=="login") {
            $GLOBALS['TL_DCA']['tl_page']['fields']['guests']['eval']['readonly']=true;
            return 1;
        }
        else{
            $GLOBALS['TL_DCA']['tl_page']['fields']['guests']['eval']['readonly']=false;
            return $varValue;
        }
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
        $arrSet['published'] = $dc->activeRecord->published;

        $this->Database->prepare("INSERT INTO tl_article %s")->set($arrSet)->execute();
    }
}