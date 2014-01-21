<?php
/**
 * Created by PhpStorm.
 * User: walkskyer
 * Date: 14-1-17
 * Time: 下午4:11
 */
if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
define('WS_FOLDER','plugin/walkskyer_portal_video');
class plugin_walkskyer_portal_video{
    public function __construct(){
    }
}
class plugin_walkskyer_portal_video_portal extends plugin_walkskyer_portal_video{
    function view_article_content(){
        global $_G;
        if($_GET['mod'] != 'view') return ;
        $navtitle = str_replace('{bbname}', $_G['setting']['bbname'], $_G['setting']['seotitle']['portal']);
        $_G['disabledwidthauto'] = 1;
        require_once libfile('portal/'.$_GET['mod'], WS_FOLDER.'/module');
    }
    function portalcp_extend(){
        return '<a href="javascript:void(0);" id="ws_hello">hello</a>';
    }
    function portalcp_bottom(){
        $script=<<<EOF
        <script src="http://ajax.aspnetcdn.com/ajax/jquery/jquery-1.7.2.js" type="text/javascript"></script>
<script type="text/javascript">
var j = jQuery.noConflict();
function createAttachBox(fn) {
	if(typeof fn == 'function' && !fn()) {
		return false;
	}
	var menu = $('icoAttach_attach_menu');
	if(menu) {
		if(menu.style.visibility == 'hidden') {
			menu.style.visibility = 'visible';
		} else {
			menu.style.width = '600px';
			showMenu({'ctrlid':'icoAttach_attach','mtype':'win','evt':'click','pos':'00','timeout':250,'duration':3,'drag':'icoAttach_attach_ctrl'});
		}
	}
}
j(document).ready(function(){
    j("a#ws_hello").click(function(){
        var strimg1='hello';
        edit_insert(strimg1);
    });
});
</script>
EOF;
        return $script;
    }
}