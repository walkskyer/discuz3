<?php
/**
 * Created by PhpStorm.
 * User: walkskyer
 * Date: 14-1-17
 * Time: 下午4:11
 */
class plugin_walkskyer_portal_video{
    public function __construct(){
    }
}
class plugin_walkskyer_portal_video_portal extends plugin_walkskyer_portal_video{
    function view_article_content(){
        return 'string hello';
    }
    function portalcp_extend(){
        return 'hello';
    }
}