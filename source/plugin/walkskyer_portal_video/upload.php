<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: misc.php 32082 2012-11-07 08:00:31Z zhengqingpeng $
 */

define('APPTYPEID', 10000);
define('CURSCRIPT', 'ws_upload');


require '../../../source/class/class_core.php';

$discuz = C::app();
$discuz->reject_robot();
define('ALLOWGUEST', 1);
$cachelist = array();
$discuz->cachelist = $cachelist;

$discuz->init();

define('CURMODULE', $mod);
runhooks();

require DISCUZ_ROOT.'./source/plugin/walkskyer_portal_video/upload.inc.php';

?>