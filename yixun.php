<?php 
require_once 'log.php';
require_once 'proxy-pass.php';

jsondb_logger_init('yixun');

forward(function(&$url, &$data_to_post, &$headers)
{
}, 
function($info, &$headers, &$body)
{
	$url = $info['url'];

	$body_utf8 = iconv('GB2312', 'UTF-8', $body);
	$body_done = preg_replace('/({"data":")/i', '$1<img src=\"http:\/\/www.doctorcom.com\/statics\/images\/style2012\/logo.jpg\" \/>\n', $body_utf8, 1);
	$body = iconv('UTF-8', 'GB2312', $body_done);
	jsondb_logger('nofity', 'REP '.get_content_type($headers), ['url'=>$url,'info'=>$info,'headers'=>$headers,'ori_body'=>$body_utf8, 'body'=>json_decode($body_utf8,true)]);
});



