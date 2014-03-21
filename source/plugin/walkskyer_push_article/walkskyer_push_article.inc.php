<?php
/**
 * Created by PhpStorm.
 * User: walkskyer
 * Date: 14-3-19
 * Time: 下午2:07
 */
define('MYS','walkskyer_push_article');
$params=$_G['cache']['plugin']['walkskyer_push_article'];
require DISCUZ_ROOT.'./source/function/function_home.php';
require DISCUZ_ROOT.'./source/function/function_portal.php';
require_once libfile('function/upload');
$swfconfig = getuploadconfig($_G['uid'], 0, false);
require_once libfile('function/spacecp');
$albums = getalbums($_G['uid']);
if(!empty($_POST) && isset($_POST['title'])){
    //var_dump($_POST);
    $data=$_POST;
    $data['dateline'] = strtotime($data['dateline']);
    if($data['catid'] != $params['ws_catid'] || empty($params['ws_catid'])){
        showmessage('文章发布功能配置错误或您的提交的数据有问题，请联系管理员。');
    }
    $art_title = array(
        'title' => htmlspecialchars($data['title']),
        'highlight' => implode('|', $data['highlight_style']),
        'catid' => intval($data['catid']),
        'dateline' => $data['dateline']?$data['dateline']:time(),
        'author' => htmlspecialchars($data['author']),
        'contents'=>1,
        'summary' => htmlspecialchars($data['summary']),
        'aid' => (int)$data['aid'],
        'status' => 1,
    );
    $art_aid=C::t('portal_article_title')->insert($art_title,true);
    $art_content=array(
        'aid'=> $art_aid,
        'title'=> $art_title['title'],
        'content' => $data['content'],
        'pageorder'=>1,
    );
    C::t('portal_article_content')->insert($art_content);
    showmessage('文章发布成功，等待管理员审核','/plugin.php?id=walkskyer_push_article');
}
@include template(MYS.':portalcp_article');