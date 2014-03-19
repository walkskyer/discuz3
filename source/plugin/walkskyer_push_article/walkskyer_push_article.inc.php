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
@include template(MYS.':portalcp_article');