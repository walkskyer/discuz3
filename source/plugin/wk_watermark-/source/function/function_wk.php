<?php
/**
 * Created by PhpStorm.
 * User: walkskyer
 * Date: 14-1-10
 * Time: 下午3:15
 */
function wk_libfile($libname, $folder = ''){
    $libpath = '/source/plugin/wk_watermark/source/'.$folder;
    if(strstr($libname, '/')) {
        list($pre, $name) = explode('/', $libname);
        $path = "{$libpath}/{$pre}/{$pre}_{$name}";
    } else {
        $path = "{$libpath}/{$libname}";
    }
    $match=preg_match('/^[\w\d\/_]+$/i', $path);
    $tp_dir=DISCUZ_ROOT.$path.'.php';
    $temp=realpath(DISCUZ_ROOT.$path.'.php');
    return preg_match('/^[\w\d\/_]+$/i', $path) ? realpath(DISCUZ_ROOT.$path.'.php') : false;
}