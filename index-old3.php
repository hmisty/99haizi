<?php
/* get param */
$page = isset($_REQUEST["page"])?$_REQUEST["page"]:1; //page number
$addr = isset($_REQUEST["addr"])?$_REQUEST["addr"]:null; //address
$size = isset($_REQUEST["size"])?$_REQUEST["size"]:20; //images per page


$SCRIPT_URI = $_SERVER["SCRIPT_URI"];
$QUERY_STRING = $_SERVER["QUERY_STRING"];

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

/* tools */
$mysql = new SaeMysql();
$furl = new SaeFetchurl();

//calculate sum by province codes
$sql = "SELECT address, count(*) AS sum FROM weibo1 GROUP BY address ORDER BY sum DESC";
$counts = $mysql->getData($sql);
$sumofsum = 0;
$sumn = array();
foreach ($counts as $count) {
	$address = $count["address"];
	$sum = $count["sum"];
	$sumn[$address] = $sum;
	$sumofsum += $sum;
}

////// param related ///////
$addr_sql_where = "";
$addr_sql_and = "";
if ($addr != null) {
	$addr_sql_where = "WHERE address=\"$addr\"";
	$addr_sql_and = "AND address=\"$addr\"";
}

//calculate total
$sql = "SELECT count(*) AS total FROM weibo1 $addr_sql_where";
$total = $mysql->getData($sql);
$totaln = $total[0]["total"];

//calculate pages
$total_pages = 1 +  (int)($totaln / $size);

$offset = ($page - 1) * $size;

if ($page < 1 || $offset > $totaln)
	exit;

//echo "page: $page, size: $size, offset: $offset, totaln: $totaln, total_pages: $total_pages";

//later, can use memcache to improve the preformance
$sql = "SELECT * FROM weibo1 $addr_sql_where ORDER BY report_time DESC LIMIT $offset, $size";
$weibos = $mysql->getData($sql);

?>
<?php
function uri_append_param($param, $value) {
	global $SCRIPT_URI;
	global $QUERY_STRING;
	$uri = $SCRIPT_URI;
	$qstr = $QUERY_STRING;
	$params = array();
	parse_str($qstr, $params);

	unset($params["page"]); //don't keep page (切换城市的时候须回到第1页)
	$params[$param] = $value;
	$qstr = http_build_query($params);
	return $uri."?".$qstr;
}

function show_pager($current_page, $total_pages) {
	$page = $current_page;
	echo "第";
	for ($i = 0; $i < $total_pages; $i++) {
		$pagen = $i + 1;

		//仅显示当前页前后10页以及第1页和最后一页的页码超链
		if ($pagen == 1 || $pagen == $total_pages
			|| abs($pagen - $page) <= 10) { 

				//显示...
				if ($pagen == $page - 10 
					&& $pagen > 2)
					echo "...&nbsp;";

				//显示页码超链接
				if ($page != $pagen) {
					$url = uri_append_param("page", $pagen);
					echo "<a href=\"$url\">";
				}
				echo $pagen;
				if ($page != $pagen)
					echo "</a>";
				echo "&nbsp;";

				//显示...
				if ($pagen == $page + 10 
					&& $pagen < $total_pages - 1)
					echo "...&nbsp;";
			}
	}
	echo "页";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>99haizi - @随手拍照解救乞讨儿童</title>

	<link rel="stylesheet" href="/css/ui.all.css" type="text/css" media="screen" title="no title" charset="utf-8"/>
	    <link rel="stylesheet" type="text/css" href="/css/jquery.lightbox-0.5.css" media="screen" />
	    <!-- Framework CSS -->
	    <link rel="stylesheet" href="/css/blueprint/screen.css" type="text/css" media="screen, projection">
	    <link rel="stylesheet" href="/css/blueprint/print.css" type="text/css" media="print">

	    <!--[if lt IE 8]><link rel="stylesheet" href="../../blueprint/ie.css" type="text/css" media="screen, projection"><![endif]-->

	    <!-- Import fancy-type plugin -->
	    <link rel="stylesheet" href="/css/blueprint/plugins/fancy-type/screen.css" type="text/css" media="screen, projection">


	<style type="text/css" media="screen">
		ul#grid li {
			list-style: none outside;
			float: left;
			margin-right: 20px;
			margin-bottom: 20px;
			font-size: 50px;
			/*width: 5em;
			height: 5em;
			line-height: 5em;
			text-align: center;*/
		}
			ul#grid li img {
				vertical-align: middle;
			}
		.ui-slider-handle { left: 45%; }
	</style>
   	<style type="text/css">
	/* jQuery lightBox plugin - Gallery style */
	#gallery {
		background-color: #444;
		padding: 10px;
		width: 520px;
	}
	#gallery ul { list-style: none; }
	#gallery ul li { display: inline; }
	#gallery ul img {
		border: 5px solid #3e3e3e;
		border-width: 5px 5px 20px;
	}
	#gallery ul a:hover img {
		border: 5px solid #fff;
		border-width: 5px 5px 20px;
		color: #fff;
	}
	#gallery ul a:hover { color: #fff; }
	</style>
	<style type="text/css" media="screen">
		#header {
		    background: none repeat scroll 0 0 #F6F6FA;
		    height: 60px;
		    margin-bottom: 5px;
		    position: relative;
		}

		#header h1 {
		    float: left;
		    margin-right: 12px;
		}
		
		#about {
			display:none;
			font-size:14px;
			line-height:150%;
		}
		
		#about .process {
			font-style:bold;
			font-size:24px;
			line-height:150%;
		}
		
		#about .contact_info {
			float:right;
			text-align:right;
			color:#666;
			font-size:12px;
		}
		
		#about .contact_info a {
			text-decoration:none;
			color:#66f;
		}

		.container {
		    margin: 0 auto;
		    position: relative;
		    width: 940px;
		}
		
		.pager {
			clear:both;
			text-align:center;
			font-size:14px;
		}

		#address {
			clear:both;
			text-align:left;
			font-size:14px;
			border:1px dotted #f6f6fa;
			padding:5px;
			margin-bottom:5px;
		}

	</style>

	<script type="text/javascript" language="JavaScript" src="/js/jquery-1.2.6.min.js"></script>
	<script type="text/javascript" language="JavaScript" src="/js/jquery-ui-personalized-1.5.3.min.js"></script>
	<script type="text/javascript" language="JavaScript" src="/js/jquery.lightbox-0.5.min.js"></script>

	<script type="text/javascript" charset="utf-8">

		$(document).ready(initializeGrid);

		function initializeGrid() {
			$('#grid a').lightBox();
			
			$("#grid_slider").slider({
				value: 50,
				max: 100,
				min: 10,
				slide: function(event, ui) {
					$('ul#grid li').css('font-size',ui.value+"px");
				}
			});
			
			$("ul#grid li img").each(function() {
				//var width = $(this).width() / 100 + "em";
				//var height = $(this).height() / 100 + "em";
				//$(this).css("width",width);
				//$(this).css("height",height);
			});
			
	 	}

	</script>
    <script type="text/javascript">

	</script>	
</head>

<body>

<div id="header">
<div class="container">
<h1><img src="/images/savchildren.jpg" height="60" alt="救救孩子" title="救救孩子"></h1>
<div style="float:left;">
	<div style="font-size:24px;">救救孩子</div>
	<div style="font-size:12px;"><a style="text-decoration:none;" href="http://t.sina.com.cn/1932619445">@随手拍照解救乞讨儿童</a> 聚合网站</div>
</div>
<div style="float:left;margin-top:12px;margin-left:20px;">
	<input style="width:210px;" type="text" disabled></input>
	<input type="button" value="搜索" disabled></input>
</div>
<!--div style="float:left;line-height:60px;font-size:14px;margin-left:20px;">
	<a style="text-decoration:none;" href="#" onclick="$('#address').toggle();">按地理位置寻找孩子</a>	
</div-->
<div style="float:right;line-height:60px;font-size:14px;">
	<a style="text-decoration:none;" href="#" onclick="$('#about').toggle();">如何参与？</a>
</div>
</h1>
</div><!-- container-->
</div><!-- header-->

<div class="container">

	<div id="about">
	<hr class="space">
	<p>请充满爱心的你立即加入随手拍照解救乞讨儿童的行动中来吧！参与方法非常简单：</p>
	<p>&nbsp;</p>
		
	<p class="process">注册<a href="http://t.sina.com.cn/">新浪微博</a>账号 ==&gt; 下载并安装微博<a href="http://t.sina.com.cn/mobile/wap?source=toptray">手机客户端</a>到你的手机 ==&gt; 见到乞讨儿童，随手拍照，写清楚时间 省市 街道等详细信息，发自己的微博并 @随手拍照解救乞讨儿童</p>
		
	<p>我们对你的爱心报告致以万分的感谢！经过 @随手拍照解救乞讨儿童 官方转发不久后，你拍的孩子照片及相关信息就会被聚合到本网站（<a href="http://99haizi.sinaapp.com/">http://99haizi.sinaapp.com/</a>）供家长/警方检索之用。</p>
		
		<div class="contact_info">
		<a href="http://t.sina.com.cn/1932619445">@随手拍照解救乞讨儿童</a> 官方微博<br>
		运营联络: <a href="http://t.sina.com.cn/yujianrong">@于建嵘</a>
		技术联络: <a href="mailto:99haizi@gmail.com">99haizi@gmail.com</a>
		<br>
		Powered By: <a href="http://t.sina.com.cn/">新浪微博</a> <a href="http://sae.sina.com.cn/">新浪AppEngine</a>
		<br>
		2011.2.5-
		</div>
		<hr class="space">
	</div>

	<div id="address">
	共有 <?php echo $sumofsum; ?>名等待回家的孩子：
	<?php
	if ($addr != null)
		echo "<a href=\"$SCRIPT_URI\">";
	echo "全部($sumofsum)";
	if ($addr != null)
		echo "</a>";
       	echo " &nbsp; ";
	foreach ($sumn as $k=>$v) {
		$url = uri_append_param("addr", $k);
		if ($addr != $k)
			echo "<a href=\"$url\">";
		echo $province_codes[$k]."($v)";
		if ($addr != $k)
			echo "</a>";
		echo " &nbsp; ";
	}
	?>
	</div>

	<div id="pages_up" class="pager">
	<div style="float:left;">点击可看大图↓</div>
	<div style="float:right;"><?php show_pager($page, $total_pages); ?></div>
	</div>
	
	<!--div id="grid_slider">
		<div class='ui-slider-handle'></div>
	</div-->
	<hr>

	<ul id="grid">
<?php foreach ($weibos as $weibo) { 
	$photo_url = $weibo["photo_url"];
	$detail = $weibo["detail"];
	$reporter_name = $weibo["reporter_name"];
	$report_time = $weibo["report_time"];

	$caption = "由 @$reporter_name 于 $report_time 报告：$detail";
?>
<li><a href="<?php echo $photo_url;?>" title="<?php echo $caption;?>">
<img src="<?php echo $photo_url;?>" height="300"
title="<?php echo $caption;?>"
alt="<?php echo $caption;?>"
/></a>
</li>
<?php } ?>
	</ul>
	
	<hr>
	<div id="pages_down" class="pager">
	<?php show_pager($page, $total_pages); ?>
	</div>
	
	<hr class="space">

</div><!--container-->
</body>
</html>

