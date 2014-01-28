<?php
/**
 * Created by PhpStorm.
 * User: walkskyer
 * Date: 14-1-28
 * Time: 上午11:32
 */
define('APPTYPEID', 400000);
define('CURSCRIPT', 'get_attach');

require '../../../source/class/class_core.php';
$discuz = C::app();

$cachelist = array('userapp', 'portalcategory', 'diytemplatenameportal');
$discuz->cachelist = $cachelist;
$discuz->init();

require DISCUZ_ROOT.'./source/function/function_home.php';
require DISCUZ_ROOT.'./source/function/function_portal.php';

/*if(empty($_GET['mod']) || !in_array($_GET['mod'], array('list', 'view', 'comment', 'portalcp', 'topic', 'attachment', 'rss', 'block'))) $_GET['mod'] = 'index';


define('CURMODULE', $_GET['mod']);*/

$navtitle = str_replace('{bbname}', $_G['setting']['bbname'], $_G['setting']['seotitle']['portal']);
$_G['disabledwidthauto'] = 1;


//$operation = $_GET['op'] ? $_GET['op'] : '';

$id = empty($_GET['id']) ? 0 : intval($_GET['id']);
$aid = empty($_GET['aid']) ? '' : intval($_GET['aid']);
$attach = C::t('portal_attachment')->fetch($id);
if(empty($attach)) {
    showmessage('portal_attachment_noexist');
}

require_once libfile('function/attachment');
if($attach['isimage']) {
    require_once libfile('function/home');
    $smallimg = pic_get($attach['attachment'], 'portal', $attach['thumb'], $attach['remote']);
    $bigimg = pic_get($attach['attachment'], 'portal', 0, $attach['remote']);
    $coverstr = addslashes(serialize(array('pic'=>'portal/'.$attach['attachment'], 'thumb'=>$attach['thumb'], 'remote'=>$attach['remote'])));
}
if($attach['filetype']=='attach') $attach['ext']=$attach['filetype']=fileext($attach['filename']);
$attach['filetype'] = attachtype($attach['filetype']."\t".$attach['filetype']);
$attach['filesize'] = sizecount($attach['filesize']);
//if($attach['filetype']) $mediaString="\"".addslashes(parsemedia(array($attach['ext'],400,300),"{$_G[siteurl]}portal.php?mod=attachment&id={$attach['attachid']}"))."\"";
//if($attach['filetype']) $mediaString=parsemedia(array($attach['ext'],400,300),"{$_G[siteurl]}portal.php?mod=attachment&id={$attach['attachid']}");
if($attach['ext']=='flv'){
    //$mediaString=parsemedia(array($attach['ext'],400,300),"{$_G[siteurl]}portal.php?mod=attachment&id={$attach['attachid']}");
    //$mediaString=str_replace('"','\"',$mediaString);
    //$mediaString=str_replace('\'','\\\'',$mediaString);
    //$mediaString=htmlentities($mediaString);
    $mediaString="[flv,400,300]{$_G[setting][discuzurl]}/portal.php?mod=attachment&id={$attach['attachid']}[/flv]";
}
//$mediaString='hello!!!!!';
include template('walkskyer_portal_video:attachment');

exit;


function parsemedia($params, $url) {
    //$params = explode(',', $params);
    $width = intval($params[1]) > 800 ? 800 : intval($params[1]);
    $height = intval($params[2]) > 600 ? 600 : intval($params[2]);

    $url = addslashes($url);
    if(!in_array(strtolower(substr($url, 0, 6)), array('http:/', 'https:', 'ftp://', 'rtsp:/', 'mms://')) && !preg_match('/^static\//', $url) && !preg_match('/^data\//', $url)) {
        $url = 'http://'.$url;
    }

    /*if($flv = parseflv($url, $width, $height)) {
        return $flv;
    }*/
    if(in_array(count($params), array(3, 4))) {
        $type = $params[0];
        //$url = htmlspecialchars(str_replace(array('<', '>'), '', str_replace('\\"', '\"', $url)));
        $url = str_replace(array('<', '>'), '', str_replace('\\"', '\"', $url));
        switch($type) {
            case 'mp3':
            case 'wma':
            case 'ra':
            case 'ram':
            case 'wav':
            case 'mid':
                return parseaudio($url, $width);
            case 'rm':
            case 'rmvb':
            case 'rtsp':
                $mediaid = 'media_'.random(3);
                return '<object classid="clsid:CFCDAA03-8BE4-11cf-B84B-0020AFBBCCFA" width="'.$width.'" height="'.$height.'"><param name="autostart" value="0" /><param name="src" value="'.$url.'" /><param name="controls" value="imagewindow" /><param name="console" value="'.$mediaid.'_" /><embed src="'.$url.'" autostart="0" type="audio/x-pn-realaudio-plugin" controls="imagewindow" console="'.$mediaid.'_" width="'.$width.'" height="'.$height.'"></embed></object><br /><object classid="clsid:CFCDAA03-8BE4-11CF-B84B-0020AFBBCCFA" width="'.$width.'" height="32"><param name="src" value="'.$url.'" /><param name="controls" value="controlpanel" /><param name="console" value="'.$mediaid.'_" /><embed src="'.$url.'" autostart="0" type="audio/x-pn-realaudio-plugin" controls="controlpanel" console="'.$mediaid.'_" width="'.$width.'" height="32"></embed></object>';
            case 'flv':
                $randomid = 'flv_'.random(3);
                return '<span id="'.$randomid.'"></span><script type="text/javascript" reload="1">$(\''.$randomid.'\').innerHTML=AC_FL_RunContent(\'width\', \''.$width.'\', \'height\', \''.$height.'\', \'allowNetworking\', \'internal\', \'allowScriptAccess\', \'never\', \'src\', \''.STATICURL.'image/common/flvplayer.swf\', \'flashvars\', \'file='.rawurlencode($url).'\', \'quality\', \'high\', \'wmode\', \'transparent\', \'allowfullscreen\', \'true\');</script>';
            case 'swf':
                $randomid = 'swf_'.random(3);
                return '<span id="'.$randomid.'"></span><script type="text/javascript" reload="1">$(\''.$randomid.'\').innerHTML=AC_FL_RunContent(\'width\', \''.$width.'\', \'height\', \''.$height.'\', \'allowNetworking\', \'internal\', \'allowScriptAccess\', \'never\', \'src\', encodeURI(\''.$url.'\'), \'quality\', \'high\', \'bgcolor\', \'#ffffff\', \'wmode\', \'transparent\', \'allowfullscreen\', \'true\');</script>';
            case 'asf':
            case 'asx':
            case 'wmv':
            case 'mms':
            case 'avi':
            case 'mpg':
            case 'mpeg':
                return '<object classid="clsid:6BF52A52-394A-11d3-B153-00C04F79FAA6" width="'.$width.'" height="'.$height.'"><param name="invokeURLs" value="0"><param name="autostart" value="0" /><param name="url" value="'.$url.'" /><embed src="'.$url.'" autostart="0" type="application/x-mplayer2" width="'.$width.'" height="'.$height.'"></embed></object>';
            case 'mov':
                return '<object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" width="'.$width.'" height="'.$height.'"><param name="autostart" value="false" /><param name="src" value="'.$url.'" /><embed src="'.$url.'" autostart="false" type="video/quicktime" controller="true" width="'.$width.'" height="'.$height.'"></embed></object>';
            default:
                return '<a href="'.$url.'" target="_blank">'.$url.'</a>';
        }
    }
    return;
}