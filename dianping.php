<?php 
require_once 'log.php';
require_once 'proxy-pass.php';

jsondb_logger_init('dianping');

forward(function(&$url, &$data_to_post, &$headers)
{
}, 
function($info, &$headers, &$body)
{
	$url = $info['url'];
	$hex_str = '';
	$content_type = get_content_type($headers);
	if ($content_type === 'application/binary') {
		$hex_str = bin2hex($body);
	}
/*
	$body= preg_replace('/(<div.*?>)/i', '$1<img src=\"http:\/\/www.doctorcom.com\/statics\/images\/style2012\/logo.jpg\" \/>\n', $body, 1);
*/
	jsondb_logger('nofity', 'REP '.strlen($body), ['url'=>$url,'info'=>$info,'headers'=>$headers,'body'=>$body, 'hex'=>$hex_str]);
});


