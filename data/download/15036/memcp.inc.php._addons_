<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(!$_G['uid']) {
	showmessage('not_loggedin', NULL, array(), array('login' => 1));
}

$ttflist = '';
$usergroups = (array)unserialize($_G['cache']['plugin']['lucas_watermark']['groupid']);
if(!in_array($_G['groupid'], $usergroups)) {
	showmessage(lang('plugin/lucas_watermark', 'group_nopremission'));
}
if(!empty($_G['cache']['plugin']['lucas_watermark']['ttf'])) {
	$ttflist = unserialize($_G['cache']['plugin']['lucas_watermark']['ttf']);
}

if($_GET['pluginop'] == 'update' && submitcheck('submitwater')) {
	$_POST = daddslashes($_POST);//debug($_FILES);
	$text = trim($_POST['text']);
	$file = $_POST['file_path'] != 'static/image/common/groupicon.gif' ? trim(substr($_POST['file_path'], strlen('data/lucas_watermark/'))) : '';
	$color = trim($_POST['color']);
	
	if ($_FILES['file']['error'] === 0) {
		require DISCUZ_ROOT.'./source/plugin/lucas_watermark/watermark_upload.class.php';
		$upload = new plugin_lucas_watermark_upload;
		$upload->init($_FILES['file'], 'temp', 101);
		$basedir = DISCUZ_ROOT.'./data/lucas_watermark/';

		$upload->attach['target'] = $basedir.'./'.$upload->attach['attachment'];
		if($upload->error()) {
			showmessage(lang('plugin/lucas_watermark', 'upload_failed'), 'home.php?mod=spacecp&ac=plugin&id=lucas_watermark:memcp');
		}
		if(!$upload->attach['isimage']) {
			showmessage(lang('plugin/lucas_watermark', 'unimgext'), 'home.php?mod=spacecp&ac=plugin&id=lucas_watermark:memcp');
		}
		if($upload->attach['size'] > 100000) {
			showmessage(lang('plugin/lucas_watermark', 'too_big'), 'home.php?mod=spacecp&ac=plugin&id=lucas_watermark:memcp');
		}
		$upload->save();//debug($upload);
		if($upload->error()) {
			showmessage(lang('plugin/lucas_watermark', 'save_failed'), 'home.php?mod=spacecp&ac=plugin&id=lucas_watermark:memcp');
		}
		$file = $upload->attach['attachment'];
	}
	if ($_POST['del'] == 'on') {
		$file_path = $_POST['file_path'];
		if ($file_path != 'static/image/common/groupicon.gif') {
			$full_path = DISCUZ_ROOT.$file_path;
			if(!unlink($full_path)) {
				showmessage(lang('plugin/lucas_watermark', 'del_failed'), 'home.php?mod=spacecp&ac=plugin&id=lucas_watermark:memcp');
			}
		}
		$file = '';
	}
	
	if (empty($text) && empty($file)) {
		showmessage(lang('plugin/lucas_watermark', 'not_empty'), 'home.php?mod=spacecp&ac=plugin&id=lucas_watermark:memcp');
	}
	if(C::t('#lucas_watermark#lucas_watermark')->insert($_G['uid'], $_POST['iswater'], $_POST['local'], $_POST['isround'], $_POST['size'], $color, $text, $file, $_POST['ttf'])) {
		showmessage(lang('plugin/lucas_watermark', 'success'), 'home.php?mod=spacecp&ac=plugin&id=lucas_watermark:memcp');
	}
}

$user = C::t('#lucas_watermark#lucas_watermark')->fetch_all_by_uid($_G['uid']);
if (empty($user['file'])) {
	$icon_file = "static/image/common/groupicon.gif";
} else {
	$icon_file = "data/lucas_watermark/".$user['file'];
}
?>