<?php


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class plugin_image extends image {
	protected $text_font = array();
	protected $text_fontpathch = './data/lucas_watermark/';
	protected $text_fontpathen = './static/image/seccode/font/en/';
	protected $user = array();
	protected $watermarktrans = 50;
	protected $recwater = 0;
	public $preview = 0;
	protected $watermarkquality = 100;
	
	public function Watermark($source, $user, $font) {//forum only
		global $_G;
		$return = $this->init('watermask', $source,($this->preview ? 'tmp' :  ''));
		if($return <= 0) {//debug($return);
			return $this->returncode($return);
		}
		$this->user = $user;
		$this->text_font = $font;//debug($this);
		
		if(function_exists('imagecopy') && function_exists('imagealphablending') && function_exists('imagecopymerge')&&!$this->imginfo['animated']) {
			$dst_photo = imagecreatetruecolor($this->imginfo['width'], $this->imginfo['height']);//debug($this);
			$imagecreatefromfunc = &$this->imagecreatefromfunc;
			$target_photo = @$imagecreatefromfunc($this->preview ? $this->source : $this->target);
			@imageCopy($dst_photo, $target_photo, 0, 0, 0, 0, $this->imginfo['width'], $this->imginfo['height']);
			
			if(!empty($this->user['file'])) {
				$this->user['file'] = DISCUZ_ROOT.'./data/lucas_watermark/'.$this->user['file'];
				if (!file_exists($this->user['file'])) {
					return $this->returncode(-106);
				}
				$water_info = getimagesize($this->user['file']);//TODO
				if (empty($water_info[2])) {
					return $this->returncode(-100);
				}
				$wate_pos = self::wposition($this->imginfo['width'], $this->imginfo['height'], $this->user['local'], $water_info[0], $water_info[1], 1);
				switch($water_info[2]){
					case 1:
						$water_logo = imagecreatefromgif($this->user['file']);
						break;
					case 2:
						$water_logo = imagecreatefromjpeg($this->user['file']);
						break;
					case 3:
						$water_logo = imagecreatefrompng($this->user['file']);
						break;
				}
				if(!$water_info['2'] == 3) {
					if($water_info['2'] == 1) {
						imageAlphaBlending($water_logo, true);
					}
					imageCopyMerge($dst_photo, $water_logo , $wate_pos[0],$wate_pos[1], 0, 0, $water_info[0], $water_info[1], $this->watermarktrans);
				} else {
					imageCopy($dst_photo, $water_logo , $wate_pos[0],$wate_pos[1], 0, 0, $water_info[0], $water_info[1]);
				}
				$img_water = true;
			}
			
			if (!empty($this->user['text']) && $img_water !== true) {
				$color = self::hex2rgb($this->user['color']);
				$color = $color == false ? array('r' => 255, 'g' => 255, 'b' => 255) : $color;
				$water_color = imagecolorallocate($dst_photo, $color['r'], $color['g'], $color['b']);
			
				if ($this->text_font != '') {
					$index = 0;
					foreach ($this->text_font as $k => $v) {
						if ($v['unique'] == $this->user['ttf']) {
							$index = $k;
							break;
						}
					}
					$water_font = DISCUZ_ROOT.$this->text_fontpathch.$this->text_font[$index]['file'];
				} 
				
				if (empty($water_font) || !file_exists($water_font)) {
					$water_font = DISCUZ_ROOT.$this->text_fontpathen.'FetteSteinschrift.ttf';
				}
				
				$water_size = $this->user['size'];
				$water_text = diconv($this->user['text'], CHARSET, 'UTF-8');
				$w_b = imagettfbbox($water_size, 0, $water_font, $water_text);
				if ($this->imginfo['width'] < 360) {
					return $this->returncode(-101);
				}
				$water_tbox = array(round($w_b[2]- $w_b[0]), round($w_b[1]- $w_b[7]));
				$wate_tpos = self::wposition($this->imginfo['width'], $this->imginfo['height'], $this->user['local'], $water_tbox['0'], $water_tbox['1']);
				imagettftext($dst_photo, $water_size, 0, $wate_tpos[0], $wate_tpos[1] , $water_color, $water_font, $water_text);
			}

			if($this->user['isround']) {
				$roundpic = $this->imginfo['width'] <= 500 ? imagecreatefrompng(DISCUZ_ROOT.'./source/plugin/lucas_watermark/image/waterpic_min.png') : imagecreatefrompng(DISCUZ_ROOT.'./source/plugin/lucas_watermark/image/waterpic.png');
				$r_w = $this->imginfo['width'] <= 500 ? 20 : 30;
				imagecopy($dst_photo, $roundpic, 0, 0, 0, 0, $r_w, $r_w);
				imagecopy($dst_photo, $roundpic, $this->imginfo['width'] - $r_w, 0, $r_w, 0, $r_w, $r_w);
				imagecopy($dst_photo, $roundpic, 0, $this->imginfo['height'] - $r_w, 0, $r_w, $r_w, $r_w);
				imagecopy($dst_photo, $roundpic, $this->imginfo['width'] - $r_w, $this->imginfo['height'] - $r_w, $r_w, $r_w, $r_w, $r_w);
			}
			 
			$this->target = !$this->preview ? $this->target : DISCUZ_ROOT.'./data/plugindata/watermark_temp_'.$_G['uid'].'.jpg';
			clearstatcache();//debug($this);
			$imagefunc = &$this->imagefunc;
			if($this->imginfo['mime'] == 'image/jpeg') {
				$imagefunc($dst_photo, $this->target, $this->watermarkquality);
			} else {
				$imagefunc($dst_photo, $this->target);
			}
			return $this->sleep(0);
		}
	}
	
	static function wposition($img_w, $img_h, $xy = "rand", $lx = 0,$ly = 0, $isimg = 0, $fixed = 0) {
		$x = $y = 0;
		$in_x = array(
			$fixed,
			($img_w - $lx)/2,
			$img_w - $lx - $fixed
		);
		$in_y = array(
			$isimg ? $fixed : $ly + $fixed,
			($img_h - $ly) / 2,
			$isimg ? $img_h - $ly - $fixed : $img_h - $fixed
		);
	
		switch($xy){
			case rand:
				$x = $in_x[rand(0,2)];
				$y = $in_y[rand(0,2)];
				break;
			case 3:
				$x = $in_x['0'];
				$y = $in_y['0'];
				break;
			case 4:
				$x = $in_x['2'];
				$y = $in_y['0'];
				break;
			case 2:
				$x = $in_x['1'];
				$y = $in_y['1'];
				break;
			case 0:
				$x = $in_x['0'];
				$y = $in_y['2'];
				break;
			case 1:
				$x = $in_x['2'];
				$y = $in_y['2'];
				break;
		}
		return array($x, $y);
	}
	
	static function check_remote_file_exists($url) {
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_NOBODY, true);
		$result = curl_exec($curl);
		$found = false;
		if ($result !== false) {
			$statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			if ($statusCode == 200) {
				$found = true;
			}
		}
		curl_close($curl);
		return $found;
	}
	
	static function hex2rgb($colour) {
		if ($colour[0] == '#') {
			$colour = substr($colour, 1);
		}
		if (strlen($colour) == 6) {
			list($r, $g, $b) = array($colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5]);
		} elseif (strlen($colour) == 3) {
			list($r, $g, $b) = array($colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2]);
		} else {
			return false;
		}
		$r = hexdec($r);
		$g = hexdec($g);
		$b = hexdec($b);
		return array('r' => $r, 'g' => $g, 'b' => $b);
	}
}
