<?php
if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
/**
 * Created by PhpStorm.
 * User: adminer
 * Date: 13-12-24
 * Time: 上午11:17
 */
function wk_forum_upload(){
    global $_G;
    if(empty($_GET['simple'])) {
        $_FILES['Filedata']['name'] = diconv(urldecode($_FILES['Filedata']['name']), 'UTF-8');
        $_FILES['Filedata']['type'] = $_GET['filetype'];
    }
    $forumattachextensions = '';
    $fid = intval($_GET['fid']);
    if($fid) {
        $forum = $fid != $_G['fid'] ? C::t('forum_forum')->fetch_info_by_fid($fid) : $_G['forum'];
        if($forum['status'] == 3 && $forum['level']) {
            $levelinfo = C::t('forum_grouplevel')->fetch($forum['level']);
            if($postpolicy = $levelinfo['postpolicy']) {
                $postpolicy = dunserialize($postpolicy);
                $forumattachextensions = $postpolicy['attachextensions'];
            }
        } else {
            $forumattachextensions = $forum['attachextensions'];
        }
        if($forumattachextensions) {
            $_G['group']['attachextensions'] = $forumattachextensions;
        }
    }
    $upload = new wk_forum_upload();
}
class wk_forum_upload extends forum_upload{
    public function __construct($getaid = 0){

        global $_G;

        $_G['uid'] = $this->uid = intval($_GET['uid']);
        $swfhash = md5(substr(md5($_G['config']['security']['authkey']), 8).$this->uid);
        $this->aid = 0;
        $this->getaid = $getaid;
        $this->simple = !empty($_GET['simple']) ? $_GET['simple'] : 0;

        if($_GET['hash'] != $swfhash) {
            return $this->uploadmsg(10);
        }


        $upload = new discuz_upload();
        $upload->init($_FILES['Filedata'], 'forum');
        $this->attach = &$upload->attach;

        if($upload->error()) {
            return $this->uploadmsg(2);
        }

        $allowupload = !$_G['group']['maxattachnum'] || $_G['group']['maxattachnum'] && $_G['group']['maxattachnum'] > getuserprofile('todayattachs');;
        if(!$allowupload) {
            return $this->uploadmsg(6);
        }

        if($_G['group']['attachextensions'] && (!preg_match("/(^|\s|,)".preg_quote($upload->attach['ext'], '/')."($|\s|,)/i", $_G['group']['attachextensions']) || !$upload->attach['ext'])) {
            return $this->uploadmsg(1);
        }

        if(empty($upload->attach['size'])) {
            return $this->uploadmsg(2);
        }

        if($_G['group']['maxattachsize'] && $upload->attach['size'] > $_G['group']['maxattachsize']) {
            $this->error_sizelimit = $_G['group']['maxattachsize'];
            return $this->uploadmsg(3);
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
                return $this->uploadmsg(4);
            } elseif($upload->attach['size'] > $maxsize) {
                $this->error_sizelimit = $maxsize;
                return $this->uploadmsg(5);
            }
        }

        if($upload->attach['size'] && $_G['group']['maxsizeperday']) {
            $todaysize = getuserprofile('todayattachsize') + $upload->attach['size'];
            if($todaysize >= $_G['group']['maxsizeperday']) {
                $this->error_sizelimit = 'perday|'.$_G['group']['maxsizeperday'];
                return $this->uploadmsg(11);
            }
        }
        updatemembercount($_G['uid'], array('todayattachs' => 1, 'todayattachsize' => $upload->attach['size']));
        $upload->save();
        if($upload->error() == -103) {
            return $this->uploadmsg(8);
        } elseif($upload->error()) {
            return $this->uploadmsg(9);
        }
        $thumb = $remote = $width = 0;
        if($_GET['type'] == 'image' && !$upload->attach['isimage']) {
            return $this->uploadmsg(7);
        }
        if($upload->attach['isimage']) {
            if(!in_array($upload->attach['imageinfo']['2'], array(1,2,3,6))) {
                return $this->uploadmsg(7);
            }
            if($_G['setting']['showexif']) {
                require_once libfile('function/attachment');
                $exif = getattachexif(0, $upload->attach['target']);
            }
            if($_G['setting']['thumbsource'] || $_G['setting']['thumbstatus']) {
                require_once libfile('class/image');
                require_once DISCUZ_ROOT.'./source/plugin/wk_watermark/wk_image.class.php';
                $image = new wk_image;
            }
            if($_G['setting']['thumbsource'] && $_G['setting']['sourcewidth'] && $_G['setting']['sourceheight']) {
                $thumb = $image->Thumb($upload->attach['target'], '', $_G['setting']['sourcewidth'], $_G['setting']['sourceheight'], 1, 1) ? 1 : 0;
                $width = $image->imginfo['width'];
                $upload->attach['size'] = $image->imginfo['size'];
            }
            if($_G['setting']['thumbstatus']) {
                $thumb = $image->Thumb($upload->attach['target'], '', $_G['setting']['thumbwidth'], $_G['setting']['thumbheight'], $_G['setting']['thumbstatus'], 0) ? 1 : 0;
                $width = $image->imginfo['width'];
                $image->Watermark($image->target, '', 'forum');
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
            'filename' => dhtmlspecialchars(censor($upload->attach['name'])),
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
        return $this->uploadmsg(0);
    }

    function Watermark_IM($type = 'forum') {
        switch($this->param['watermarkstatus'][$type]) {
            case 1:
                $gravity = 'NorthWest';
                break;
            case 2:
                $gravity = 'North';
                break;
            case 3:
                $gravity = 'NorthEast';
                break;
            case 4:
                $gravity = 'West';
                break;
            case 5:
                $gravity = 'Center';
                break;
            case 6:
                $gravity = 'East';
                break;
            case 7:
                $gravity = 'SouthWest';
                break;
            case 8:
                $gravity = 'South';
                break;
            case 9:
                $gravity = 'SouthEast';
                break;
        }

        if($this->param['watermarktype'][$type] != 'text') {
            $exec_str = $this->param['imageimpath'].'/composite'.
                ($this->param['watermarktype'][$type] != 'png' && $this->param['watermarktrans'][$type] != '100' ? ' -watermark '.$this->param['watermarktrans'][$type] : '').
                ' -quality '.$this->param['watermarkquality'][$type].
                ' -gravity '.$gravity.
                ' '.$this->param['watermarkfile'][$type].' '.$this->source.' '.$this->target;
        } else {
            $watermarktextcvt = str_replace(array("\n", "\r", "'"), array('', '', '\''), pack("H*", $this->param['watermarktext']['text'][$type]));
            $angle = -$this->param['watermarktext']['angle'][$type];
            $translate = $this->param['watermarktext']['translatex'][$type] || $this->param['watermarktext']['translatey'][$type] ? ' translate '.$this->param['watermarktext']['translatex'][$type].','.$this->param['watermarktext']['translatey'][$type] : '';
            $skewX = $this->param['watermarktext']['skewx'][$type] ? ' skewX '.$this->param['watermarktext']['skewx'][$type] : '';
            $skewY = $this->param['watermarktext']['skewy'][$type] ? ' skewY '.$this->param['watermarktext']['skewy'][$type] : '';
            $exec_str = $this->param['imageimpath'].'/convert'.
                ' -quality '.$this->param['watermarkquality'][$type].
                ' -font "'.$this->param['watermarktext']['fontpath'][$type].'"'.
                ' -pointsize '.$this->param['watermarktext']['size'][$type].
                (($this->param['watermarktext']['shadowx'][$type] || $this->param['watermarktext']['shadowy'][$type]) && $this->param['watermarktext']['shadowcolor'][$type] ?
                    ' -fill "rgb('.$this->param['watermarktext']['shadowcolor'][$type].')"'.
                    ' -draw "'.
                    ' gravity '.$gravity.$translate.$skewX.$skewY.
                    ' rotate '.$angle.
                    ' text '.$this->param['watermarktext']['shadowx'][$type].','.$this->param['watermarktext']['shadowy'][$type].' \''.$watermarktextcvt.'\'"' : '').
                ' -fill "rgb('.$this->param['watermarktext']['color'][$type].')"'.
                ' -draw "'.
                ' gravity '.$gravity.$translate.$skewX.$skewY.
                ' rotate '.$angle.
                ' text 0,0 \''.$watermarktextcvt.'\'"'.
                ' '.$this->source.' '.$this->target;
        }
        exec($exec_str);
        if(!file_exists($this->target)) {
            return -3;
        }
        return 1;
    }
} 