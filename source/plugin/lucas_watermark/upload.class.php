<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class plugin_forum_upload extends forum_upload{
	protected $user;//user watermark setting
	
	public function __construct() {
		global $_G;
		
		$_G['uid'] = $this->uid = intval($_GET['uid']);
		$swfhash = md5(substr(md5($_G['config']['security']['authkey']), 8).$this->uid);
		$this->aid = 0;
		$this->simple = !empty($_GET['simple']) ? $_GET['simple'] : 0;

		if($_GET['hash'] != $swfhash) {
			$this->uploadmsg(10);
		}

		$upload = new discuz_upload();
		$upload->init($_FILES['Filedata'], 'forum');
		$this->attach = &$upload->attach;

		if($upload->error()) {
			$this->uploadmsg(2);
		}

		$allowupload = !$_G['group']['maxattachnum'] || $_G['group']['maxattachnum'] && $_G['group']['maxattachnum'] > getuserprofile('todayattachs');;
		if(!$allowupload) {
			$this->uploadmsg(6);
		}

		if($_G['group']['attachextensions'] && (!preg_match("/(^|\s|,)".preg_quote($upload->attach['ext'], '/')."($|\s|,)/i", $_G['group']['attachextensions']) || !$upload->attach['ext'])) {
			$this->uploadmsg(1);
		}

		if(empty($upload->attach['size'])) {
			$this->uploadmsg(2);
		}

		if($_G['group']['maxattachsize'] && $upload->attach['size'] > $_G['group']['maxattachsize']) {
			$this->error_sizelimit = $_G['group']['maxattachsize'];
			$this->uploadmsg(3);
		}

		loadcache('attachtype');
		if($_G['fid'] && isset($_G['cache']['attachtype'][$_G['fid']][$upload->attach['ext']])) {
			$maxsize = $_G['cache']['attachtype'][$_G['fid']][$upload->attach['ext']];
		} elseif(isset($_G['cache']['attachtype'][0][$upload->attach['ext']])) {
			$maxsize = $_G['cache']['attachtype'][0][$upload->attach['ext']];
		}
		if(isset($maxsize)) {
			if(!$maxsize) {
				$this->error_sizelimit = 'ban';
				$this->uploadmsg(4);
			} elseif($upload->attach['size'] > $maxsize) {
				$this->error_sizelimit = $maxsize;
				$this->uploadmsg(5);
			}
		}

		if($upload->attach['size'] && $_G['group']['maxsizeperday']) {
			$todaysize = getuserprofile('todayattachsize') + $upload->attach['size'];
			if($todaysize >= $_G['group']['maxsizeperday']) {
				$this->error_sizelimit = 'perday|'.$_G['group']['maxsizeperday'];
				$this->uploadmsg(11);
			}
		}
		updatemembercount($_G['uid'], array('todayattachs' => 1, 'todayattachsize' => $upload->attach['size']));
		$upload->save();
		if($upload->error() == -103) {
			$this->uploadmsg(8);
		} elseif($upload->error()) {
			$this->uploadmsg(9);
		}
		
		$thumb = $remote = $width = 0;
		if($_GET['type'] == 'image' && !$upload->attach['isimage']) {
			$this->uploadmsg(7);
		}
		if($upload->attach['isimage']) {
			if($_G['setting']['showexif']) {
				require_once libfile('function/attachment');
				$exif = getattachexif(0, $upload->attach['target']);
			}
			//watermark begin
			$this->user = C::t('#lucas_watermark#lucas_watermark')->fetch_all_by_uid($_G['uid']);
			if ($this->user['iswater']) {
				loadcache('plugin');
				$ttflist = unserialize($_G['cache']['plugin']['lucas_watermark']['ttf']);
				include_once DISCUZ_ROOT.'./source/plugin/lucas_watermark/image.class.php';
				$images = new plugin_image;
				$images->Watermark($upload->attach['target'], $this->user, $ttflist);
			}
			if($_G['setting']['thumbsource'] || $_G['setting']['thumbstatus']) {
				require_once libfile('class/image');
				$image = new image;
			}
			if($_G['setting']['thumbsource'] && $_G['setting']['sourcewidth'] && $_G['setting']['sourceheight']) {
				$thumb = $image->Thumb($upload->attach['target'], '', $_G['setting']['sourcewidth'], $_G['setting']['sourceheight'], 1, 1) ? 1 : 0;
				$width = $image->imginfo['width'];
				$upload->attach['size'] = $image->imginfo['size'];
			}
			if($_G['setting']['thumbstatus']) {
				$thumb = $image->Thumb($upload->attach['target'], '', $_G['setting']['thumbwidth'], $_G['setting']['thumbheight'], $_G['setting']['thumbstatus'], 0) ? 1 : 0;
				$width = $image->imginfo['width'];
			}
			
			if($_G['setting']['thumbsource'] || !$_G['setting']['thumbstatus']) {
				list($width) = @getimagesize($upload->attach['target']);
			}
		}
		if($_GET['type'] != 'image' && $upload->attach['isimage']) {
			$upload->attach['isimage'] = -1;
		}
		$this->aid = $aid = getattachnewaid($this->uid);
		$insert = array(
			'aid' => $aid,
			'dateline' => $_G['timestamp'],
			'filename' => censor($upload->attach['name']),
			'filesize' => $upload->attach['size'],
			'attachment' => $upload->attach['attachment'],
			'isimage' => $upload->attach['isimage'],
			'uid' => $this->uid,
			'thumb' => $thumb,
			'remote' => $remote,
			'width' => $width,
		);
		C::t('forum_attachment_unused')->insert($insert);
		if($upload->attach['isimage'] && $_G['setting']['showexif']) {
			C::t('forum_attachment_exif')->insert($aid, $exif);
		}
		$this->uploadmsg(0);
	}
}

?>