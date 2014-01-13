<?php
/**
 * Created by PhpStorm.
 * User: adminer
 * Date: 13-12-27
 * Time: 上午10:58
 */
if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

$_G['uid'] = intval($_POST['uid']);

if((empty($_G['uid']) && $_GET['operation'] != 'upload') || $_POST['hash'] != md5(substr(md5($_G['config']['security']['authkey']), 8).$_G['uid'])) {
    exit();
} else {
    if($_G['uid']) {
        $_G['member'] = getuserbyuid($_G['uid']);
    }
    $_G['groupid'] = $_G['member']['groupid'];
    loadcache('usergroup_'.$_G['member']['groupid']);
    $_G['group'] = $_G['cache']['usergroup_'.$_G['member']['groupid']];
}
function wk_portal_upload(){


    $aid = intval($_POST['aid']);
    $catid = intval($_POST['catid']);
    $msg = '';
    $errorcode = 0;
    require_once libfile('function/portalcp');
    if($aid) {
        $article = C::t('portal_article_title')->fetch($aid);
        if(!$article) {
            $errorcode = 1;
        }

        if(check_articleperm($catid, $aid, $article, false, true) !== true) {
            $errorcode = 2;
        }

    } else {
        if(check_articleperm($catid, $aid, null, false, true) !== true) {
            $errorcode = 3;
        }
    }

    $upload = new discuz_upload();

    $_FILES["Filedata"]['name'] = addslashes(diconv(urldecode($_FILES["Filedata"]['name']), 'UTF-8'));
    $upload->init($_FILES['Filedata'], 'portal');
    $attach = $upload->attach;
    if(!$upload->error()) {
        $upload->save();
    }
    if($upload->error()) {
        $errorcode = 4;
    }
    if(!$errorcode) {
        if($attach['isimage'] && empty($_G['setting']['portalarticleimgthumbclosed'])) {
            require_once DISCUZ_ROOT.'./source/plugin/wk_watermark/wk_image.class.php';
            $image = new wk_image;
            $thumbimgwidth = $_G['setting']['portalarticleimgthumbwidth'] ? $_G['setting']['portalarticleimgthumbwidth'] : 300;
            $thumbimgheight = $_G['setting']['portalarticleimgthumbheight'] ? $_G['setting']['portalarticleimgthumbheight'] : 300;
            $attach['thumb'] = $image->Thumb($attach['target'], '', $thumbimgwidth, $thumbimgheight, 2);
            $image->Watermark($image->target, '', 'portal');
            $image->Watermark($attach['target'], '', 'portal');
        }

        if(getglobal('setting/ftp/on') && ((!$_G['setting']['ftp']['allowedexts'] && !$_G['setting']['ftp']['disallowedexts']) || ($_G['setting']['ftp']['allowedexts'] && in_array($attach['ext'], $_G['setting']['ftp']['allowedexts'])) || ($_G['setting']['ftp']['disallowedexts'] && !in_array($attach['ext'], $_G['setting']['ftp']['disallowedexts']))) && (!$_G['setting']['ftp']['minsize'] || $attach['size'] >= $_G['setting']['ftp']['minsize'] * 1024)) {
            if(ftpcmd('upload', 'portal/'.$attach['attachment']) && (!$attach['thumb'] || ftpcmd('upload', 'portal/'.getimgthumbname($attach['attachment'])))) {
                @unlink($_G['setting']['attachdir'].'/portal/'.$attach['attachment']);
                @unlink($_G['setting']['attachdir'].'/portal/'.getimgthumbname($attach['attachment']));
                $attach['remote'] = 1;
            } else {
                if(getglobal('setting/ftp/mirror')) {
                    @unlink($attach['target']);
                    @unlink(getimgthumbname($attach['target']));
                    $errorcode = 5;
                }
            }
        }

        $setarr = array(
            'uid' => $_G['uid'],
            'filename' => $attach['name'],
            'attachment' => $attach['attachment'],
            'filesize' => $attach['size'],
            'isimage' => $attach['isimage'],
            'thumb' => $attach['thumb'],
            'remote' => $attach['remote'],
            'filetype' => $attach['extension'],
            'dateline' => $_G['timestamp'],
            'aid' => $aid
        );
        $setarr['attachid'] = C::t('portal_attachment')->insert($setarr, true);
        if($attach['isimage']) {
            require_once libfile('function/home');
            $smallimg = pic_get($attach['attachment'], 'portal', $attach['thumb'], $attach['remote']);
            $bigimg = pic_get($attach['attachment'], 'portal', 0, $attach['remote']);
            $coverstr = addslashes(serialize(array('pic'=>'portal/'.$attach['attachment'], 'thumb'=>$attach['thumb'], 'remote'=>$attach['remote'])));
            echo "{\"aid\":$setarr[attachid], \"isimage\":$attach[isimage], \"smallimg\":\"$smallimg\", \"bigimg\":\"$bigimg\", \"errorcode\":$errorcode, \"cover\":\"$coverstr\"}";
            exit();
        } else {
            $fileurl = 'portal.php?mod=attachment&id='.$attach['attachid'];
            echo "{\"aid\":$setarr[attachid], \"isimage\":$attach[isimage], \"file\":\"$fileurl\", \"errorcode\":$errorcode}";
            exit();
        }
    } else {
        echo "{\"aid\":0, \"errorcode\":$errorcode}";
    }
}