<?php

/* for echo */
header("Content-type: text/plain; charset=utf-8");

/* constants */
$weibo_id = '6159946893';
$comment_text = "本条信息已成功聚合至99haizi.sinaapp.com，点击查看：http://99haizi.sinaapp.com/ （多图；分省份查看）";

/* definition */
$user = "qingyan123@gmail.com";
$passwd = "a7s8d9f0";
$appkey = "2555687341";

$url_comment = "http://api.t.sina.com.cn/statuses/comment.json?source=$appke";

/* tools */
$mysql = new SaeMysql();
$furl = new SaeFetchurl();
$furl->debug(true);
$furl->setHttpAuth($user, $passwd);

/* post comment */
$succ = true;
if ($succ) {
	//post comment
	$furl->setMethod('POST');
	$furl->setPostData(array(
		'source' => $appkey,
		'id' => $weibo_id,
		'comment' => $comment_text,
	));

	$url_comment .= "&id=$weibo_id&comment=".urlencode($comment_text);
	$ret = $furl->fetch($url_comment);

	if ($ret === false)
		echo ("Comment failed: errno = "
		.$furl->errno()
		." errmsg = "
		.$furl->errmsg()
		." body = "
		.$furl->body());
	else 
		echo "Comment successfully!";
}

?>
