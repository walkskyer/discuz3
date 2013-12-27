<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(!$_G['uid']) {
	showmessage('not_loggedin', NULL, array(), array('login' => 1));
}

$usergroups = (array)unserialize($_G['cache']['plugin']['lucas_watermark']['groupid']);
if(!in_array($_G['groupid'], $usergroups)) {
	showmessage(lang('plugin/lucas_watermark', 'group_nopremission'));
}
$ttflist = unserialize($_G['cache']['plugin']['lucas_watermark']['ttf']);
$user = C::t('#lucas_watermark#lucas_watermark')->fetch_all_by_uid($_G['uid']);//debug($user);
include_once DISCUZ_ROOT.'./source/plugin/lucas_watermark/image.class.php';
$images = new plugin_image;
$images->preview = 1;
$images->Watermark(DISCUZ_ROOT.'./source/plugin/lucas_watermark/image/watermarkpreview.jpg', $user, $ttflist);//debug($images);
include template('common/header_ajax');
echo '<h3 class="flb"><em id="return_preview">preview</em><span><a href="javascript:;" class="flbc" onclick="hideWindow(\'preview\')" title="close">CLOSE</a></span></h3><img src="data/plugindata/watermark_temp_'.$_G['uid'].'.jpg?t='.TIMESTAMP.'" title="watermark preview"/>';
include template('common/footer_ajax');
dexit();
?>
