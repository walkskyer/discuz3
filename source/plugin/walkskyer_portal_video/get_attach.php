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
$attach['filetype'] = attachtype($attach['filetype']."\t".$attach['filetype']);
$attach['filesize'] = sizecount($attach['filesize']);
include template('walkskyer_portal_video:attachment');

exit;