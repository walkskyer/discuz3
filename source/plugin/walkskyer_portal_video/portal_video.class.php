<?php
/**
 * Created by PhpStorm.
 * User: walkskyer
 * Date: 14-1-17
 * Time: 下午4:11
 */
if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
define('WS_FOLDER','plugin/walkskyer_portal_video');
class plugin_walkskyer_portal_video{
    public function __construct(){
    }
}
class plugin_walkskyer_portal_video_portal extends plugin_walkskyer_portal_video{
    function view_article_content(){
        global $_G;
        if($_GET['mod'] != 'view') return ;
        $navtitle = str_replace('{bbname}', $_G['setting']['bbname'], $_G['setting']['seotitle']['portal']);
        $_G['disabledwidthauto'] = 1;
        require_once libfile('portal/'.$_GET['mod'], WS_FOLDER.'/module');
    }
    function portalcp_extend(){
        return 'hello';
    }
}