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
    $params['ws_catid'] = intval($params['ws_catid']) > 0? intval($params['ws_catid']): 0;
    if($data['catid'] <=0){
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
    if($art_aid){
        C::t('portal_category')->increase($params['ws_catid'],array('articles'=>1));
        C::t('common_moderate')->insert('aid',array('id'=>$art_aid,'dateline'=>time()));
        $art_content=array(
            'aid'=> $art_aid,
            'title'=> $art_title['title'],
            'content' => $data['content'],
            'pageorder'=>1,
        );
        C::t('portal_article_content')->insert($art_content);
        showmessage('文章发布成功，等待管理员审核','/plugin.php?id=walkskyer_push_article');
    }
}
$catid = intval($_GET['catid']);
$article = array();
loadcache('portalcategory');
require_once libfile('function/portalcp');
$categoryselect = category_showselect('portal', 'catid', true, !empty($article['catid']) ? $article['catid'] : $catid);
@include template(MYS.':portalcp_article');