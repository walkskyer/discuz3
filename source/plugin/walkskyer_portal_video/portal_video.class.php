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
j(document).ready(function(){
    j("a#ws_hello").click(function(){
        var htmlEditor = j("#HtmlEditor").contents().find("body");
        var html=j("body", window.frames["HtmlEditor"].document).html();
        var a=1;
        var s=htmlEditor.text();
        var b=2;
    });
});
</script>
EOF;
        return $script.'<a>hello</a>';
    }
}