<?php
/**
 * Created by PhpStorm.
 * User: walkskyer
 * Date: 14-1-29
 * Time: 上午10:49
 */
define('APPTYPEID', 40001);
define('CURSCRIPT', 'portal_play');

require '../../../source/class/class_core.php';
$discuz = C::app();

$cachelist = array('userapp', 'portalcategory', 'diytemplatenameportal');
$discuz->cachelist = $cachelist;
$discuz->init();

require DISCUZ_ROOT.'./source/function/function_home.php';
require DISCUZ_ROOT.'./source/function/function_portal.php';

define('CURMODULE', $_GET['mod']);

$id = empty($_GET['id']) ? 0 : intval($_GET['id']);
$aid = empty($_GET['aid']) ? '' : intval($_GET['aid']);
$attach = C::t('portal_attachment')->fetch($id);
if(empty($attach)) {
    showmessage('portal_attachment_noexist');
}

$filename = realpath($_G['setting']['attachdir'].'/portal/'.$attach['attachment']);
$filesize = !$attach['remote'] ? filesize($filename) : $attach['filesize'];

dheader('Date: '.gmdate('D, d M Y H:i:s', $attach['dateline']).' GMT');
dheader('Last-Modified: '.gmdate('D, d M Y H:i:s', $attach['dateline']).' GMT');
dheader('Content-Encoding: none');
dheader('Content-Disposition: inline; filename='.$attach['filename']);
dheader('Content-Type: application/octet-stream');
dheader('Content-Length: '.$filesize);

$attach['remote'] ? getremotefile($attach['attachment']) : getlocalfile($filename);

function getremotefile($file) {
    global $_G;
    @set_time_limit(0);
    if(!@readfile($_G['setting']['ftp']['attachurl'].'forum/'.$file)) {
        $ftp = ftpcmd('object');
        $tmpfile = @tempnam($_G['setting']['attachdir'], '');
        if($ftp->ftp_get($tmpfile, 'forum/'.$file, FTP_BINARY)) {
            @readfile($tmpfile);
            @unlink($tmpfile);
        } else {
            @unlink($tmpfile);
            return FALSE;
        }
    }
    return TRUE;
}

function getlocalfile($filename, $readmod = 2, $range = 0) {
    if($readmod == 1 || $readmod == 3 || $readmod == 4) {
        if($fp = @fopen($filename, 'rb')) {
            @fseek($fp, $range);
            if(function_exists('fpassthru') && ($readmod == 3 || $readmod == 4)) {
                @fpassthru($fp);
            } else {
                echo @fread($fp, filesize($filename));
            }
        }
        @fclose($fp);
    } else {
        @readfile($filename);
    }
    @flush(); @ob_flush();
}