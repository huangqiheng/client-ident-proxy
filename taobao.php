<?php 
require_once 'log.php';
require_once 'proxy-pass.php';

forward(function(&$url, &$data_to_post, &$headers)
{
}, function($info, &$headers, &$body)
{
	$body= preg_replace('/(<body.*?>)/i', '$1<img src=\"http://www.doctorcom.com/statics/images/style2012/logo.jpg\" />', $body);
});
