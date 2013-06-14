<?php
require_once "../inc/common.inc";

/* for echo */
header("Content-type: text/plain; charset=utf-8");

/* definition */
$user = "qingyan123@gmail.com";
$passwd = "a7s8d9f0";
$appkey = "2555687341";
$url = "http://api.t.sina.com.cn/statuses/friends_timeline.json?source=$appkey&count=100&page=1";

$weibo_timefmt = "D M d H:i:s O Y"; //Sat Feb 05 15:00:40 +0800 2011
$mysql_timefmt = "Y-m-d H:i:s"; //2011-02-05 15:00:40

/* constants */
list($province_codes, $province_names, $secondary_names) = get_placenames();

//generate reg pattern
$cn_names = array_values($province_codes);
$pattern = '/'.join("|", $cn_names).'/';

$cn2_names = array_keys($secondary_names);
$pattern2 = '/'.join("|", $cn2_names).'/';

/* tools */
$mysql = new SaeMysql();
$furl = new SaeFetchurl();

/* fetch it */
$furl->setHttpAuth($user, $passwd);
$content = $furl->fetch($url);
//header("Content-type: application/json; charset=utf-8");
//echo $content;

/* parse it */
$weibos = json_decode($content);

/* save it */
header("Content-type: text/plain; charset=utf-8");
foreach ($weibos as $weibo) {
	$fields = array();
	$fields["weibo_id"] = $weibo->{"id"};
	$fields["editor_id"] = $weibo->{"user"}->{"id"};
	$origin = $weibo->{"retweeted_status"};
	$fields["report_time"] = $origin->{"created_at"};
	$fields["orig_weibo_id"] = $origin->{"id"};
	$fields["detail"] = $origin->{"text"};
	$fields["photo_url"] = $origin->{"original_pic"};
	$fields["reporter_id"] = $origin->{"user"}->{"id"};
	$fields["reporter_name"] = $origin->{"user"}->{"screen_name"};

	$detail = $fields["detail"];
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
	$fields["address"] = $addr;

	if ($fields["photo_url"] != "") //only save weibo with photo
		save_fields_to_mysql($fields);
}

/* finalize */
$mysql->closeDb();

///////////////////////////////////////////////////////////////
/* save_fields_to_mysql */
function save_fields_to_mysql($fields) {
	global $mysql;
	global $weibo_timefmt;
	global $mysql_timefmt;

	/* convert time format */
	$date = DateTime::createFromFormat($weibo_timefmt, $fields["report_time"]);
	$fields["report_time"] = $date->format($mysql_timefmt); 

	/* escape all user generated contents */
	//mysql_real_escape_string has been disabled by SAE
	$fields["detail"] = $mysql->escape($fields["detail"]);
	$fields["reporter_name"] = $mysql->escape($fields["reporter_name"]);

	/* gen SQL */
	$keys = array_keys($fields);
	$values = array_values($fields);
	$keylist = join(",", $keys);
	$valuelist = join("','", $values);

	$sql = "INSERT INTO weibo1($keylist) VALUES ('$valuelist')";
	echo "Exec SQL: $sql\n";

	$mysql->runSql($sql);
	if ($mysql->errno() != 0) {
		echo "Error:".$mysql->errmsg()."\n";
	}
}

?>
