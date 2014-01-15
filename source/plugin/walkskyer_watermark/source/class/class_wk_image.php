<?php
if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
/**
 * Created by PhpStorm.
 * User: adminer
 * Date: 13-12-24
 * Time: 下午3:08
 */
class wk_image extends image{
    protected $ws;
    public function __construct(){
        global $_G;
        parent::image();
        $this->ws=$_G['cache']['plugin']['walkskyer_watermark'];
        if($this->ws['wk_rate']==0 || $this->ws['wk_rate']>100)
            $this->ws['wk_rate']=50;
    }

    function Watermark_GD($type = 'forum') {
        if(!function_exists('imagecreatetruecolor')) {
            return -4;
        }

        $imagefunc = &$this->imagefunc;

        if($this->param['watermarktype'][$type] != 'text') {
            if(!function_exists('imagecopy') || !function_exists('imagecreatefrompng') || !function_exists('imagecreatefromgif') || !function_exists('imagealphablending') || !function_exists('imagecopymerge')) {
                return -4;
            }
            $watermarkinfo = @getimagesize($this->param['watermarkfile'][$type]);
            if($watermarkinfo === FALSE) {
                return -3;
            }
            $watermark_logo	= $this->param['watermarktype'][$type] == 'png' ? @imageCreateFromPNG($this->param['watermarkfile'][$type]) : @imageCreateFromGIF($this->param['watermarkfile'][$type]);
            if(!$watermark_logo) {
                return 0;
            }
            list($logo_w, $logo_h) = $watermarkinfo;
            //if(($this->imginfo['width'] - $logo_w) < 10 || ($this->imginfo['height'] - $logo_h) < 10){
            $wm_dst_w=$this->imginfo['width']*((int)$this->ws['wk_rate'])/100;
            $wm_dst_h = (int)$wm_dst_w/$logo_w*$logo_h;
            $target = imagecreatetruecolor($wm_dst_w, $wm_dst_h);
            if($this->param['watermarktype'][$type] == 'png') imagefill($target, 0, 0, imagecolorallocatealpha($target, 0, 0, 0, 127));
            imagecopyresampled($target,$watermark_logo,0,0,0,0,$wm_dst_w,$wm_dst_h,$logo_w,$logo_h);
            if($this->param['watermarktype'][$type] == 'png') imagesavealpha($target, true);
            $watermark_logo =$target;
            $logo_w=$wm_dst_w;
            $logo_h=$wm_dst_h;
            //}
        } else {
            if(!function_exists('imagettfbbox') || !function_exists('imagettftext') || !function_exists('imagecolorallocate')) {
                return -4;
            }
            if(!class_exists('Chinese')) {
                include libfile('class/chinese');
            }

            $watermarktextcvt = pack("H*", $this->param['watermarktext']['text'][$type]);
            $box = imagettfbbox($this->param['watermarktext']['size'][$type], $this->param['watermarktext']['angle'][$type], $this->param['watermarktext']['fontpath'][$type], $watermarktextcvt);
            $logo_h = max($box[1], $box[3]) - min($box[5], $box[7]);
            $logo_w = max($box[2], $box[4]) - min($box[0], $box[6]);
            $ax = min($box[0], $box[6]) * -1;
            $ay = min($box[5], $box[7]) * -1;
        }
        $wmwidth = $this->imginfo['width'] - $logo_w;
        $wmheight = $this->imginfo['height'] - $logo_h;


        if($wmwidth > 10 && $wmheight > 10 && !$this->imginfo['animated']) {
            switch($this->param['watermarkstatus'][$type]) {
                case 1:
                    $x = 5;
                    $y = 5;
                    break;
                case 2:
                    $x = ($this->imginfo['width'] - $logo_w) / 2;
                    $y = 5;
                    break;
                case 3:
                    $x = $this->imginfo['width'] - $logo_w - 5;
                    $y = 5;
                    break;
                case 4:
                    $x = 5;
                    $y = ($this->imginfo['height'] - $logo_h) / 2;
                    break;
                case 5:
                    $x = ($this->imginfo['width'] - $logo_w) / 2;
                    if($this->ws['wk_5_position'] == 0)
                        $y = ($this->imginfo['height'] - $logo_h) / 2;
                    elseif($this->ws['wk_5_position']==1){
                        $y = ($this->imginfo['height'] - $logo_h) / 4;
                    }elseif($this->ws['wk_5_position']==-1){
                        $y = ($this->imginfo['height'] - $logo_h) *3/ 4;
                    }
                    break;
                case 6:
                    $x = $this->imginfo['width'] - $logo_w;
                    $y = ($this->imginfo['height'] - $logo_h) / 2;
                    break;
                case 7:
                    $x = 5;
                    $y = $this->imginfo['height'] - $logo_h - 5;
                    break;
                case 8:
                    $x = ($this->imginfo['width'] - $logo_w) / 2;
                    $y = $this->imginfo['height'] - $logo_h - 5;
                    break;
                case 9:
                    $x = $this->imginfo['width'] - $logo_w - 5;
                    $y = $this->imginfo['height'] - $logo_h - 5;
                    break;
            }
            if($this->imginfo['mime'] != 'image/png') {
                $color_photo = imagecreatetruecolor($this->imginfo['width'], $this->imginfo['height']);
            }
            $dst_photo = $this->loadsource();
            if($dst_photo < 0) {
                return $dst_photo;
            }
            imagealphablending($dst_photo, true);
            imagesavealpha($dst_photo, true);
            if($this->imginfo['mime'] != 'image/png') {
                imageCopy($color_photo, $dst_photo, 0, 0, 0, 0, $this->imginfo['width'], $this->imginfo['height']);
                $dst_photo = $color_photo;
            }
            if($this->param['watermarktype'][$type] == 'png') {
                imageCopy($dst_photo, $watermark_logo, $x, $y, 0, 0, $logo_w, $logo_h);
            } elseif($this->param['watermarktype'][$type] == 'text') {
                if(($this->param['watermarktext']['shadowx'][$type] || $this->param['watermarktext']['shadowy'][$type]) && $this->param['watermarktext']['shadowcolor'][$type]) {
                    $shadowcolorrgb = explode(',', $this->param['watermarktext']['shadowcolor'][$type]);
                    $shadowcolor = imagecolorallocate($dst_photo, $shadowcolorrgb[0], $shadowcolorrgb[1], $shadowcolorrgb[2]);
                    imagettftext($dst_photo, $this->param['watermarktext']['size'][$type], $this->param['watermarktext']['angle'][$type], $x + $ax + $this->param['watermarktext']['shadowx'][$type], $y + $ay + $this->param['watermarktext']['shadowy'][$type], $shadowcolor, $this->param['watermarktext']['fontpath'][$type], $watermarktextcvt);
                }

                $colorrgb = explode(',', $this->param['watermarktext']['color'][$type]);
                $color = imagecolorallocate($dst_photo, $colorrgb[0], $colorrgb[1], $colorrgb[2]);
                imagettftext($dst_photo, $this->param['watermarktext']['size'][$type], $this->param['watermarktext']['angle'][$type], $x + $ax, $y + $ay, $color, $this->param['watermarktext']['fontpath'][$type], $watermarktextcvt);
            } else {
                imageAlphaBlending($watermark_logo, true);
                imageCopyMerge($dst_photo, $watermark_logo, $x, $y, 0, 0, $logo_w, $logo_h, $this->param['watermarktrans'][$type]);
            }

            clearstatcache();
            if($this->imginfo['mime'] == 'image/jpeg') {
                @$imagefunc($dst_photo, $this->target, $this->param['watermarkquality'][$type]);
            } else {
                @$imagefunc($dst_photo, $this->target);
            }
        }
        return 1;
    }
    function Watermark_IM($type = 'forum') {

        switch($this->param['watermarkstatus'][$type]) {
            case 1:
                $gravity = 'NorthWest';
                break;
            case 2:
                $gravity = 'North';
                break;
            case 3:
                $gravity = 'NorthEast';
                break;
            case 4:
                $gravity = 'West';
                break;
            case 5:
                $gravity = 'Center';
                break;
            case 6:
                $gravity = 'East';
                break;
            case 7:
                $gravity = 'SouthWest';
                break;
            case 8:
                $gravity = 'South';
                break;
            case 9:
                $gravity = 'SouthEast';
                break;
        }

        if($this->param['watermarktype'][$type] != 'text') {
            $waterMark=$this->wk_Detect(DISCUZ_ROOT.$this->param['watermarkfile'][$type],$this->target);
            if(!$waterMark){
                $waterMark=$this->param['watermarkfile'][$type];
            }
            if($gravity == 'Center'){
                $imginfo=$this->wk_image_size_im($waterMark);
                $logoWH=$this->wk_image_size_im($this->target);
                if($this->ws['wk_5_position'] == 0)
                    $geometry = '';
                elseif($this->ws['wk_5_position']==1){
                    $geometry = ' -geometry +0+'.($imginfo['height'] - $logoWH['height']) / 3;
                }elseif($this->ws['wk_5_position']==-1){
                    $geometry = ' -geometry +0-'.($imginfo['height'] - $logoWH['height']) / 3;
                }
            }
            $exec_str = $this->param['imageimpath'].'/composite'.
                ($this->param['watermarktype'][$type] != 'png' && $this->param['watermarktrans'][$type] != '100' ? ' -watermark '.$this->param['watermarktrans'][$type] : '').
                ' -quality '.$this->param['watermarkquality'][$type].
                ' -gravity '.$gravity.$geometry.
                ' '.$waterMark.' '.$this->source.' '.$this->target;
        } else {
            $watermarktextcvt = str_replace(array("\n", "\r", "'"), array('', '', '\''), pack("H*", $this->param['watermarktext']['text'][$type]));
            $angle = -$this->param['watermarktext']['angle'][$type];
            $translate = $this->param['watermarktext']['translatex'][$type] || $this->param['watermarktext']['translatey'][$type] ? ' translate '.$this->param['watermarktext']['translatex'][$type].','.$this->param['watermarktext']['translatey'][$type] : '';
            $skewX = $this->param['watermarktext']['skewx'][$type] ? ' skewX '.$this->param['watermarktext']['skewx'][$type] : '';
            $skewY = $this->param['watermarktext']['skewy'][$type] ? ' skewY '.$this->param['watermarktext']['skewy'][$type] : '';
            $exec_str = $this->param['imageimpath'].'/convert'.
                ' -quality '.$this->param['watermarkquality'][$type].
                ' -font "'.$this->param['watermarktext']['fontpath'][$type].'"'.
                ' -pointsize '.$this->param['watermarktext']['size'][$type].
                (($this->param['watermarktext']['shadowx'][$type] || $this->param['watermarktext']['shadowy'][$type]) && $this->param['watermarktext']['shadowcolor'][$type] ?
                    ' -fill "rgb('.$this->param['watermarktext']['shadowcolor'][$type].')"'.
                    ' -draw "'.
                    ' gravity '.$gravity.$translate.$skewX.$skewY.
                    ' rotate '.$angle.
                    ' text '.$this->param['watermarktext']['shadowx'][$type].','.$this->param['watermarktext']['shadowy'][$type].' \''.$watermarktextcvt.'\'"' : '').
                ' -fill "rgb('.$this->param['watermarktext']['color'][$type].')"'.
                ' -draw "'.
                ' gravity '.$gravity.$translate.$skewX.$skewY.
                ' rotate '.$angle.
                ' text 0,0 \''.$watermarktextcvt.'\'"'.
                ' '.$this->source.' '.$this->target;
        }
        if(isset($exec_str)&&!empty($exec_str)) exec($exec_str);
        if(!file_exists($this->target)) {
            return -3;
        }
        return 1;
    }

    /**
     * 使用imagick 等比缩放图片
     * @param string $source  源图片地址
     * @param string $target  缩放后图片地址
     * @return bool
     */
    public function wk_Detect($source,$target){
        if(!is_file($source)){ //判断源图片是否存在
            return false;
        }
        $srcWH=$this->wk_image_size_im($source);
        $dstWH=$this->wk_image_size_im($target);
        /*if($srcWH['width']>$dstWH['width']){*/
        $srcW = $dstWH['width']*((int)$this->ws['wk_rate'])/100;
        $srcH = $srcW/$srcWH['width']*$srcWH['height'];
        /*}else{
            return false;
        }*/
        $dstFile=DISCUZ_ROOT."./data/plugindata/wk_watermark.resize.".fileext($source);
        exec($this->param['imageimpath']."/convert -resize {$srcW} ".$source." $dstFile");
        return is_file($dstFile)?$dstFile:false;
    }
    public function wk_image_size_im($imageFile){
        $output='';
        exec($this->param['imageimpath'].'/identify '.$imageFile,$output);
        $info=explode(' ',$output[0]);
        $size=explode('x',$info[2]);
        return array('width'=>$size[0],'height'=>$size[1]);
    }
}