<?php 
require_once 'log.php';
require_once 'proxy-pass.php';

forward('hook_request', 'hook_response');

function hook_request(&$url, &$post, &$headers)
{
	//jsondb_logger('nofity', $_SERVER['REQUEST_METHOD'].' '.$url, ['url'=>$url,'post'=>$post,'headers'=>$headers]);
}

function hook_response($info, &$headers, &$body)
{
	$headers[] = 'OMP: true';
	$url = $info['url'];

	if (preg_match('/<body.*?>/i', $body)) {
		$body= preg_replace('/(<body.*?>)/i', '$1<img src="http://www.doctorcom.com/statics/images/style2012/logo.jpg" />', $body);
	}

	//jsondb_logger('nofity', 'REP '.get_content_type($headers), ['url'=>$url,'info'=>$info,'headers'=>$headers,'body'=>$body]);
}
