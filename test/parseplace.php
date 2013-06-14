<?php
require_once "../inc/common.inc";
/* for echo */
header("Content-type: text/plain; charset=utf-8");

/* constants */
list($province_codes, $province_names, $secondary_names) = get_placenames();

//generate reg pattern
$cn_names = array_keys($province_names);
$pattern = '/'.join("|", $cn_names).'/';

$cn2_names = array_keys($secondary_names);
$pattern2 = '/'.join("|", $cn2_names).'/';

/* tools */
$mysql = new SaeMysql();

/* query all blank addr */
$sql = 'SELECT weibo_id, detail FROM weibo1 WHERE address IS NULL or address="wz"';
$data = $mysql->getData($sql);

if ($data != false)
for ($i = 0; $i < count($data); $i++) {
	$rec = $data[$i];
	$weibo_id = $rec["weibo_id"];
	$detail = $rec["detail"];

	preg_match($pattern, $detail, $matches, PREG_OFFSET_CAPTURE);
	
	$n = count($matches);
	if ($n == 0) {
		//not found in province_names, try secondary_names
		preg_match($pattern2, $detail, $matches2, PREG_OFFSET_CAPTURE);
		
		$n2 = count($matches2);
		if ($n2 == 0) {
			//still not found. addr = wz
			$addr = "wz";
		}
		else if ($n2 == 1) {
			$addr = $secondary_names[$matches2[0][0]];
		}
		else if ($n2 > 1) {
			echo "Warning: #$weibo_id more than one secondary names found!\n";
			$addr = $secondary_names[$matches2[0][0]];
		}
	}
	else if ($n == 1) {
		$addr = $province_names[$matches[0][0]];
	}
	else if ($n > 1) {
		echo "Warning: #$weibo_id more than one province found!\n";
		$addr = $province_names[$matches[0][0]];
	}

	$sql = "UPDATE weibo1 SET address=\"$addr\" WHERE weibo_id=\"$weibo_id\"";
	echo "Exec SQL: $sql\n";
	$result = $mysql->runSql($sql);
	if ($mysql->errno() != 0) {
		echo "Error:".$mysql->errmsg()."\n";
	}

}

?>
