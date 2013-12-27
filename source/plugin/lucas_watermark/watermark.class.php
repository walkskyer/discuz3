<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class plugin_lucas_watermark {
	protected $vars;
	protected $forumid;
	protected $groupid;
	
	public function __construct() {
		global $_G;
		$this->vars = $_G['cache']['plugin']['lucas_watermark'];
		$this->forumid = unserialize($this->vars['forumid']);
		$this->groupid = unserialize($this->vars['groupid']);
	}
	
 	public function common() {
 		global $_G;
 		if (empty($_FILES) || $_GET['mod'] != 'swfupload' || $_GET['action'] != 'swfupload') return false;
 		$_G['uid'] = intval($_POST['uid']);
 		if((empty($_G['uid']) && $_GET['operation'] != 'upload') || $_POST['hash'] != md5(substr(md5($_G['config']['security']['authkey']), 8).$_G['uid'])) {
 			exit;
 		} else {
 			if($_G['uid']) {
 				$_G['member'] = getuserbyuid($_G['uid']);
 			}
 			$_G['groupid'] = $_G['member']['groupid'];
 			loadcache('usergroup_'.$_G['member']['groupid']);
 			$_G['group'] = $_G['cache']['usergroup_'.$_G['member']['groupid']];
 		}
 		
 		if($_GET['operation'] == 'upload') {
 			if(empty($_GET['simple'])) {
 				$_FILES['Filedata']['name'] = addslashes(diconv(urldecode($_FILES['Filedata']['name']), 'UTF-8'));
 				$_FILES['Filedata']['type'] = $_GET['filetype'];
 			}
 			require_once DISCUZ_ROOT.'./source/plugin/lucas_watermark/upload.class.php';
 			$upload = new plugin_forum_upload;
 		}
 	}
	
	public function lucas_water_output() {
		global $_G;
		$html = '';
		
		$user = C::t('#lucas_watermark#lucas_watermark')->fetch_all_by_uid($_G['uid']);
		
		if ($this->vars['isopen'] == false || !in_array($_G['fid'], $this->forumid) || !in_array($_G['groupid'], $this->groupid)) return $html;
		if (empty($user)) {
			$html = lang('plugin/lucas_watermark', 'no_setting');
		} else {
			$tmpvar = $user['iswater'] ? lang('plugin/lucas_watermark', 'pre_settging_y') : lang('plugin/lucas_watermark', 'pre_settging_n');
			$html = '<strong>'.$tmpvar.lang('plugin/lucas_watermark', 'setting');
		}
		return $html;
	}
}

class plugin_lucas_watermark_forum extends plugin_lucas_watermark {
	
	function post_middle() {
		return $this->lucas_water_output();
	}
}