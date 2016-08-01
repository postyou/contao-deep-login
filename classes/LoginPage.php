<?php
/**
 * Created by PhpStorm.
 * User: Gerald
 * Date: 29.01.2015
 * Time: 13:47
 */

namespace postyou;


use Contao\Input;
use Contao\Message;
use Contao\PageError403;
use Contao\PageModel;
use Contao\System;

class LoginPage extends \PageRegular{

    public function checkLoginStatus($objPage, $objLayout, \PageRegular $objPageRegular)
    {
        global $objPage;

        $loggedIn=FE_USER_LOGGED_IN;
        if(\Input::get('logout'))
            if(@$this->User->logout())
                $loggedIn=false;

        if (!$loggedIn && $objPage->type!="login" && ($this->isProtected($objPage) || $objPage->type=="error_403")) {
            $loginPage=PageModel::findOneBy("type","login");
            if(isset($loginPage) && $loginPage->published==1){
                $loginPage->loadDetails();
                $objPage=$loginPage;
                $handler=new LoginPage(\Environment::get("request"));
                $handler->generate($loginPage);
                exit();
            } else {
                System::log("Please create a Login-Page urgently!!","LoginPage\checkLoginStatus",TL_ERROR);
                if($objPage->type=="error_403")
                    return;
                $page403_model=\PageModel::find403ByPid($objPage->rootId);
                if(isset($page403_model) && $page403_model->published==1){
                    $page403_model->loadDetails();
                    $objPage=$page403_model;
                    $handler=new PageError403();
                    $handler->generate($page403_model->id,$page403_model->rootId);
                    exit();
                } else {
                    System::log("Please create a 403-Error-Page urgently!!","LoginPage\checkLoginStatus",TL_ERROR);
                    $objPage->template      = 'fe_login';
                    $objPage->templateGroup = $objLayout->templates;
                    $objPageRegular->createTemplate($objPage, $objLayout);
                }
            }
        }


    }


    public function isProtected($pageModel){
        if($pageModel->protected===true)
            return true;

        if ($pageModel->pid >0)
        {
            $objParentPage = \PageModel::findByPk($pageModel->pid);
            return $this->isProtected($objParentPage);
        }
        return false;
    }

    public function getLoginPageIcon($objPage, $image)
    {
        if ($objPage->type == 'login' && $objPage->published==1)
        {
            return 'system/modules/deepLogin/assets/images/login.png';
        }elseif($objPage->type == 'login' && $objPage->published!=1){
            return 'system/modules/deepLogin/assets/images/login_grey.png';
        }

        return $image;

    }

}