<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$sql = <<<EOF

DROP TABLE cdb_lucas_watermark;

EOF;
runquery($sql);


$finish = TRUE;