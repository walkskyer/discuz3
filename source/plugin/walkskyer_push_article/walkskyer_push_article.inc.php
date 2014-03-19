<?php
/**
 * Created by PhpStorm.
 * User: walkskyer
 * Date: 14-3-19
 * Time: 下午2:07
 */
define('MYS','walkskyer_push_article');
require DISCUZ_ROOT.'./source/function/function_home.php';
require DISCUZ_ROOT.'./source/function/function_portal.php';
require_once libfile('function/upload');
$swfconfig = getuploadconfig($_G['uid'], 0, false);
require_once libfile('function/spacecp');
$albums = getalbums($_G['uid']);
@include template(MYS.':portalcp_article');