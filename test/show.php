<?php
/* tools */
$mysql = new SaeMysql();
$furl = new SaeFetchurl();

//don't calculate total
//later, can use memcache to improve the preformance

$sql = "SELECT * FROM weibo1 LIMIT 20";
$weibos = $mysql->getData($sql);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>@随手拍照解救乞讨儿童</title>

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
			width: 5em;
			height: 5em;
			line-height: 5em;
			text-align: center;
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
		    margin-bottom: 40px;
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

	</style>

	<script type="text/javascript" language="JavaScript" src="/js/jquery-1.2.6.min.js"></script>
	<script type="text/javascript" language="JavaScript" src="/js/jquery-ui-personalized-1.5.3.min.js"></script>
	<script type="text/javascript" language="JavaScript" src="/js/jquery.lightbox-0.5.min.js"></script>

    <script type="text/javascript">
    $(function() {
        $('#grid a').lightBox();
    });
	</script>
	<script type="text/javascript" charset="utf-8">

		$(document).ready(initializeGrid);

		function initializeGrid() {
			
			$("#grid_slider").slider({
				value: 50,
				max: 100,
				min: 10,
				slide: function(event, ui) {
					$('ul#grid li').css('font-size',ui.value+"px");
				}
			});
			
			$("ul#grid li img").each(function() {
				var width = $(this).width() / 100 + "em";
				var height = $(this).height() / 100 + "em";
				$(this).css("width",width);
				$(this).css("height",height);
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
<div style="float:left;line-height:60px;font-size:16px;margin-left:20px;">
	<a style="text-decoration:none;" href="mapnavi.php">按地理位置寻找孩子</a>	
</div>
<div style="float:right;line-height:60px;font-size:16px;">
	<a style="text-decoration:none;" href="#" onclick="$('#about').toggle();">如何参与？</a>
</div>
</h1>
</div><!-- container-->
</div><!-- header-->

<div class="container">

	<div id="about">
	<p>请热心的你加入随手拍照解救乞讨儿童的行动中来吧！参与流程非常简单：</p>
	<p>&nbsp;</p>
		
	<p class="process">注册<a href="http://t.sina.com.cn/">新浪微博</a>账号 ==&gt; 下载并安装微博<a href="http://t.sina.com.cn/mobile/wap?source=toptray">手机客户端</a>到你的手机 ==&gt; 见到乞讨儿童，随手拍照，写清楚时间 省市 街道等详细信息，发自己的微博并 @随手拍照解救乞讨儿童</p>
		
	<p>我们对你的爱心报告致以万分的感谢！经过 @随手拍照解救乞讨儿童 官方转发不久后，你拍的孩子照片及相关信息就会被聚合到本网站（
	<a href="http://99haizi.sinaapp.com/">http://99haizi.sinaapp.com/</a>）供家长/警方检索之用。</p>
		
		<div class="contact_info">
		<a href="http://t.sina.com.cn/1932619445">@随手拍照解救乞讨儿童</a> 官方微博<br>
		运营联络: <a href="http://t.sina.com.cn/yujianrong">@于建嵘</a>
		技术联络: <a href="http://t.sina.com.cn/qingyan">@刘青焱</a>
		<br>
		2011.2.5
		</div>
		<hr class="space">
	</div>

	
	<div id="grid_slider">
		<div class='ui-slider-handle'></div>
	</div>

	<ul id="grid">
<?php foreach ($weibos as $weibo) { 
	$photo_url = $weibo["photo_url"];
	$detail = $weibo["detail"];
?>
<li><a href="<?php echo $photo_url;?>" title="<?php echo $detail;?>">
<img src="<?php echo $photo_url;?>" height="500"
title="<?php echo $detail;?>"
alt="<?php echo $detail;?>"
/></a>
</li>
<?php } ?>
	</ul>


</div><!--container-->
</body>
</html>

