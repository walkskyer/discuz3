<?php
if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
/**
 * Created by PhpStorm.
 * User: adminer
 * Date: 13-12-24
 * Time: 上午10:42
 */
class plugin_wk_watermark{
    protected $vars;
    protected $forumid;
    protected $groupid;

    public function __construct() {
        global $_G;
        $this->vars = $_G['cache']['plugin']['wk_watermark'];
        if(!$this->vars['wk_watermark_on']) return;
        $this->forumid = unserialize($this->vars['forumid']);
        $this->groupid = unserialize($this->vars['groupid']);
    }
    function common(){
        global $_G;
        if(!$this->vars['wk_watermark_on']) return;
        if (empty($_FILES) || $_GET['mod'] != 'swfupload' || $_GET['action'] != 'swfupload') return false;
        $_G['uid'] = intval($_POST['uid']);
        if((empty($_G['uid']) && $_GET['operation'] != 'upload') || $_POST['hash'] != md5(substr(md5($_G['config']['security']['authkey']), 8).$_G['uid'])) {
            exit;
        }
        if($_GET['operation'] == 'upload' && $this->vars['wk_forum']) {
            if(empty($_GET['simple'])) {
                $_FILES['Filedata']['name'] = addslashes(diconv(urldecode($_FILES['Filedata']['name']), 'UTF-8'));
                $_FILES['Filedata']['type'] = $_GET['filetype'];
            }
            require_once DISCUZ_ROOT.'./source/plugin/wk_watermark/wk_forum_upload.class.php';
            wk_forum_upload();
        }elseif($_GET['operation'] == 'portal' && $this->vars['wk_portal']) {
            require_once DISCUZ_ROOT.'./source/plugin/wk_watermark/wk_portal_upload.class.php';
            wk_portal_upload();
        }elseif($_GET['operation'] == 'album' && $this->vars['wk_album']) {
            require_once DISCUZ_ROOT.'./source/plugin/wk_watermark/wk_album_upload.class.php';
            wk_album_upload();
        }
    }
}