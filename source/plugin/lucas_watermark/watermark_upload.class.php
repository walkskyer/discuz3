<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class plugin_lucas_watermark_upload extends discuz_upload {
	
	function get_target_dir($type, $extid = '', $check_exists = true) {
		$subdir = $subdir1 = $subdir2 = '';
		if($type == 'temp') {
			if ($extid == 100) {
				$subdir1 = 'ttf';
				$subdir2 = date('Ym');
			} else {
				$subdir1 = 'icon';
				$subdir2 = date('Ym');
			}
			$subdir = $subdir1.'/'.$subdir2.'/';
		}
	
		$check_exists && self::check_dir_exists($type, $subdir1, $subdir2);
	
		return $subdir;
	}
	
	function get_target_filename($type, $extid = 0, $forcename = '') {
		global $_G;
		if ($extid == 100) {
			$filename = date('His').strtolower(random(16));
		} else {
			$filename = substr(md5($_G['uid']), 0, 10).'_'.$_G['uid'];
		}
		return $filename;
	}
	
	function get_target_extension($ext) {
		static $safeext  = array('attach', 'jpg', 'jpeg', 'gif', 'png', 'swf', 'bmp', 'txt', 'zip', 'rar', 'mp3', 'ttf');
		return strtolower(!in_array(strtolower($ext), $safeext) ? 'attach' : $ext);
	}
	
	function check_dir_exists($type = '', $sub1 = '', $sub2 = '') {
		global $_G;
		$type = discuz_upload::check_dir_type($type);
	
		$basedir = DISCUZ_ROOT.'./data/lucas_watermark';
	
		$subdir1  = $sub1 !== '' ?  ($basedir.'/'.$sub1) : '';
		$subdir2  = $sub2 !== '' ?  ($subdir1.'/'.$sub2) : '';
	
		$res = $subdir2 ? is_dir($subdir2) : ($subdir1 ? is_dir($subdir1) : is_dir($basedir));
		if(!$res) {
			$res = $basedir && discuz_upload::make_dir($basedir);
			$res && $subdir1 && ($res = discuz_upload::make_dir($subdir1));
			$res && $subdir1 && $subdir2 && ($res = discuz_upload::make_dir($subdir2));
		}
	
		return $res;
	}
}