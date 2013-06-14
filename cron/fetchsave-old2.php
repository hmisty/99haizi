<?php
/* definition */
$user = "qingyan123@gmail.com";
$passwd = "a7s8d9f0";
$appkey = "2555687341";
$url = "http://api.t.sina.com.cn/statuses/friends_timeline.json?source=$appkey&count=100&page=1";

$weibo_timefmt = "D M d H:i:s O Y"; //Sat Feb 05 15:00:40 +0800 2011
$mysql_timefmt = "Y-m-d H:i:s"; //2011-02-05 15:00:40

/* constants */
$province_codes = array(
"bj"	=>	"北京",
"ah"	=>	"安徽",
"am"	=>	"澳门",
"cq"	=>	"重庆",
"fj"	=>	"福建",
"gs"	=>	"甘肃",
"gd"	=>	"广东",
"gx"	=>	"广西",
"gg"	=>	"贵州",
"han"	=>	"海南",
"hb"	=>	"河北",
"hlj"	=>	"黑龙江",
"hn"	=>	"河南",
"hub"	=>	"湖北",
"hun"	=>	"湖南",
"js"	=>	"江苏",
"jx"	=>	"江西",
"jl"	=>	"吉林",
"ln"	=>	"辽宁",
"nmg"	=>	"内蒙古",
"nx"	=>	"宁夏",
"qh"	=>	"青海",
"s3x"	=>	"陕西",
"sd"	=>	"山东",
"sh"	=>	"上海",
"sx"	=>	"山西",
"sc"	=>	"四川",
"tw"	=>	"台湾",
"tj"	=>	"天津",
"xg"	=>	"香港",
"xj"	=>	"新疆",
"xz"	=>	"西藏",
"yn"	=>	"云南",
"zj"	=>	"浙江",
"wz"	=>	"未知",
	);

$province_names = array_flip($province_codes);

//generate reg pattern
$cn_names = array_values($province_codes);
$pattern = '/'.join("|", $cn_names).'/';


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

	preg_match($pattern, $fields["detail"], $matches, PREG_OFFSET_CAPTURE);
	$n = count($matches);
	if ($n == 0) {
		$addr = "wz";
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
