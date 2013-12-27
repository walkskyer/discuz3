<?php

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$setted = 0;
$index = 0;
$pluginvarid = 0;
$url = 'plugins&operation=config&do='.$pluginid.'&identifier=lucas_watermark&pmod=ttf';
$plugin = C::t('common_pluginvar')->fetch_all_by_pluginid($pluginid);
foreach ($plugin as $k => $v) {
	if ($v['variable'] == 'ttf') {
		$setted = 1;
		$index = $k;
		$pluginvarid = $v['pluginvarid'];
	}
}
if ($setted == 0) {
	$data = array(
			'pluginid' => $pluginid,
			'displayorder' => 10,
			'title' => lang('plugin/lucas_watermark', 'font'),
			'variable' => 'ttf',
			'type' => 'text',
			'value' => '',
	);
	C::t('common_pluginvar')->insert($data);
	cpmsg(lang('plugin/lucas_watermark', 'font_inited'), 'action='.$url, 'success');
}

if (submitcheck('ttfsubmit')) {
	if($_FILES['ttf']['size'] == 0 && empty($_POST['ttf_name'])) {
		cpmsg(lang('plugin/lucas_watermark', 'not_full'), '', 'error');
	}
	if ($_FILES['ttf']['size'] != 0) {
		$ext = strtolower(fileext($_FILES['ttf']['name']));
		if ($_FILES['ttf']['type'] != 'application/octet-stream' || $ext != 'ttf') {
			cpmsg(lang('plugin/lucas_watermark', 'not_ttf'), '', 'error');
		} elseif ($_FILES['ttf']['error'] != 0) {
			cpmsg(lang('plugin/lucas_watermark', 'upload_failed'), '', 'error');
		}
	}
	
	require DISCUZ_ROOT.'./source/plugin/lucas_watermark/watermark_upload.class.php';
	$upload = new plugin_lucas_watermark_upload;
	$upload->init($_FILES['ttf'], 'temp', 100);
	$basedir = DISCUZ_ROOT.'./data/lucas_watermark/';
	
	$upload->attach['target'] = $basedir.'./'.$upload->attach['attachment'];
	$upload->save();
	if($upload->error()) {
		cpmsg(lang('plugin/lucas_watermark', 'save_failed'), '', 'error');
	}
	$file = $upload->attach['attachment'];
	
	$ttfary = array(
		'file' => $file,
		'name' => daddslashes($_POST['ttf_name']),
		'unique' => substr(md5($file), 0, 5),
	);
	if (empty($plugin[$index]['value'])) {
		$data[] = $ttfary;
		$datas = serialize($data);
	} else {
		$data = unserialize($plugin[$index]['value']);
		$data[] = $ttfary;
		$datas = serialize($data);
	}
	C::t('common_pluginvar')->update($pluginvarid, array('value' => $datas));
	$plugin = C::t('common_pluginvar')->fetch_all_by_pluginid($pluginid);
}

if (submitcheck('ttfdelsubmit')) {
	$ttflist =unserialize($plugin[$index]['value']);
	foreach ($ttflist as $k => &$v) {
		if (in_array($v['unique'], $_POST['tidarray'])) {
			$full_path = DISCUZ_ROOT."./data/lucas_watermark/".$v['file'];
			if (unlink($full_path)) {
				$v = null;
			}
		}
	}
	$ttflist = array_diff($ttflist, array(null,'null','',' '));
	$datas = serialize($ttflist);
	C::t('common_pluginvar')->update($pluginvarid, array('value' => $datas));
	$plugin = C::t('common_pluginvar')->fetch_all_by_pluginid($pluginid);
}

showtips(lang('plugin/lucas_watermark', 'bg_tips'));
showtableheader(lang('plugin/lucas_watermark', 'font_upload'));
showformheader($url, 'enctype');
showsetting(lang('plugin/lucas_watermark', 'font_name'), 'ttf_name', '', 'text', 0, 0, '');
showsetting(lang('plugin/lucas_watermark', 'font_file'), 'ttf', '', 'file', '', '', lang('plugin/lucas_watermark', 'no_ttf'));
showsubmit('ttfsubmit');
showformfooter();
showtablefooter();
showtableheader(lang('plugin/lucas_watermark', 'ttf_list'));
$ttflist =unserialize($plugin[$index]['value']);
if(empty($ttflist)) {
	showtablerow('', 'colspan="3"', cplang(lang('plugin/lucas_watermark', 'need_font')));
} else {
	showformheader($url);
	showsubtitle(array('', '', lang('plugin/lucas_watermark', 'ttf_name'), lang('plugin/lucas_watermark', 'ttf_path')));
	foreach($ttflist as $thread) {
		$threads .= showtablerow('', array('class="td25"', '', '', '', '', ''), array(
			"<input class=\"checkbox\" type=\"checkbox\" name=\"tidarray[]\" value=\"$thread[unique]\" />",
			"",
			"{$thread['name']}",
			"{$thread['file']}",
		), TRUE);
	}
	echo $threads;
	showtablerow('', array('class="td25" colspan="7"'), array('<input name="chkall" id="chkall" type="checkbox" class="checkbox" onclick="checkAll(\'prefix\', this.form, \'tidarray\', \'chkall\')" /><label for="chkall">'.cplang('select_all').'</label>'));
	$multi = '';
	showsubmit('ttfdelsubmit', 'del', '', '', $multi);
	showformfooter();
}
showtablefooter();
?>