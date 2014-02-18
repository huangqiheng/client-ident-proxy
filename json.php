<?php 
require_once 'log.php';
require_once 'proxy-pass.php';

forward(null, function($info, &$headers, &$body)
{
	$body= preg_replace('/(<body.*?>)/i', '$1<img src=\"http://www.doctorcom.com/statics/images/style2012/logo.jpg\" />', $body);
	//$url = $info['url'];
	//jsondb_logger('nofity', 'REP '.get_content_type($headers), ['url'=>$url,'info'=>$info,'headers'=>$headers,'body'=>$body]);
});
