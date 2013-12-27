<?php

class table_lucas_watermark extends discuz_table
{
	public function __construct() {

		$this->_table = 'lucas_watermark';
		$this->_pk    = 'uid';

		parent::__construct();
	}

	public function fetch_all_by_uid($uid) {
		return DB::fetch_first("SELECT * FROM %t WHERE uid=%d", array($this->_table, $uid));
	}
	
	public function insert($uid, $iswater, $local, $isround, $size, $color='', $text = '', $file = '', $ttf = '') {
		$text = trim($text);
		$file = trim($file);
		if (empty($ttf)) $ttf = '';
		if ($text == '' && $file == '') return false;
		$data = array(
						'uid' => dintval($uid),
						'iswater' => dintval($iswater),
						'local' => dintval($local),
						'isround' => dintval($isround),
						'size' => dintval($size),
						'color' => (string)$color,
						'text' => (string)$text,
						'ttf' => (string)$ttf,
						'file' => (string)$file,
						'dateline' => TIMESTAMP,
		);
		return DB::insert($this->_table, $data, false, true);
	}
	
	public function update_by_uid($uid) {
		
	}

}

?>