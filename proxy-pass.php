<?php

define('REQUEST_TIMEOUT', 5);

function forward($cb_before=null, $cb_after=null, $url=null, $headers=null, $input_post=null)
{
	//生成url
	if (empty($url)) {
		$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	} else {
		if (isset($headers) && (isset($headers['Host']))) {

		} else {
			if (!preg_match("/https?:/i", $url)) {
				if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
				    $url = "https://" . $_SERVER['HTTP_HOST'] . "/" . ltrim($url, "/");
				} else {
				    $url = "http://" . $_SERVER['HTTP_HOST'] . "/" . ltrim($url, "/");
				}
			}
		}
	}

	//获取转发需要的头内容
	if (empty($headers)) {
		$headers = get_request_headers();
	}

	//转发POST内容
	$data_to_post = null;
	if (empty($input_post)) {
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			if(in_array(get_content_type($headers), array('application/x-www-form-urlencoded','multipart/form-data'))) {
				$data_to_post = $_POST;
			} else {
				//就抓出原始的post数据即可
				$fp = fopen('php://input','r');
				$post = stream_get_contents($fp);
				fclose($fp);
				$data_to_post = $post;
			}
		}
	} else {
		$data_to_post = $input_post;
	}

	if ($cb_before) {
		if ($data_to_post) {
			call_user_func_array($cb_before, [&$url, &$data_to_post, &$headers]);
		} else {
			call_user_func_array($cb_before, [&$url, null, &$headers]);
		}
	}

	//初始化curl选项
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, REQUEST_TIMEOUT);
        
	//将生成的头，设置在curl中
        set_request_headers($ch, $headers);
        
	//设置POST数据
	if ($data_to_post) {
		set_post($ch, $data_to_post);
	}
        
	//执行curl请求
	//要防止长连接用这种方法 fixme 
        $data = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

	//获取返回的body内容
        $body = $info["size_download"] ? substr($data, $info["header_size"], $info["size_download"]) : "";
        
        $headers_str = substr($data, 0, $info["header_size"]);
        $headers = get_response_headers($headers_str);

	if ($cb_after) {
		$encoding = get_content_encoding($headers);

		$body_str = $body;
		if ($encoding === 'deflate') {
			$body_str = gzinflate($body);
		}

		if ($encoding === 'gzip') {
			$body_str = gzdecode($body);
		}

		//调用过滤钩子，检测是否有修改内容
		$old_md5 = md5($body_str);
		call_user_func_array($cb_after, [$info, &$headers, &$body_str]);
		$new_md5 = md5($body_str);

		//如果内容有修改，则需要从新打包，和计算内容长度
		if ($old_md5 !== $new_md5) {
			switch ($encoding) {
				case 'deflate': 
					$body = gzdeflate($body_str );
					break;
				case 'gzip': 
					$body = gzencode($body_str );
					break;
				default:
					$body = $body_str;
			}

			//修正发出的内容长度
			$headers = set_content_length($headers, strlen($body));
		}
	}

	//转发返回的头内
	set_response_headers($headers);
        
	//输出html内容到浏览器
        echo $body;

	//函数返回结果给缓存使用
	return [$headers, $body];
}

function get_content_type($headers)
{
	return enum_get_header($headers, function($key, $val) {
		if( 'content-type' == strtolower($key) ){
			$parts = explode(';', $val);
			return strtolower($parts[0]);
		}
		return false;
	});
}
    
function get_content_encoding($headers)
{
	return enum_get_header($headers, function($key, $val) {
		if( 'content-encoding' == strtolower($key) ){
			return strtolower(trim($val));
		}
		return false;
	});
}

function enum_get_header($headers, $callback)
{
	foreach( $headers as $name => $value ){
		$cmp_key = $name;
		$cmp_val = $value;
		if (is_numeric($name)) {
			$pos = strpos($value, ":");
			if ($pos === FALSE) {
				continue;
			}
			$cmp_key = substr($value, 0, $pos);
			$cmp_val = substr($value, $pos+1);
		}

		if ($res = call_user_func($callback, $cmp_key, $cmp_val)) {
			return $res;
		}
	}
	return null;

}

function set_content_length($headers, $length)
{
	$new_headers = enum_set_header($headers, function($key, $value) use($length) {
		if( 'content-length' == strtolower($key) ){
			return strval($length);
		}
		return $value;
	});
	return ($new_headers)? $new_headers : $headers;
}

function enum_set_header($headers, $callback)
{
	$result = [];
	$modified = false;
	foreach( $headers as $name => $value ){
		if (is_numeric($name)) {
			$pos = strpos($value, ":");
			if ($pos === FALSE) {
				$result[] = $value;
				continue;
			}
			$cmp_key = substr($value, 0, $pos);
			$cmp_val = trim(substr($value, $pos+1));
			$new_val = call_user_func($callback, $cmp_key, $cmp_val);
			if ($cmp_val != $new_val) {
				$modified = true;
			}
			$result[] = $cmp_key.': '.$new_val;
		} else {
			$new_val = call_user_func($callback, $name, $value);
			if ($value != $new_val) {
				$modified = true;
			}
			$result[$name] = $new_val;
		}
	}
	return $modified? $result : false;

}

function get_request_headers()
{
	//使用原生的函数来获取是最好的
	if (function_exists('getallheaders')) return getallheaders();

	//自己构造一个
	$headers = '';
	foreach ($_SERVER as $name => $value) {
		if (substr($name, 0, 5) == 'HTTP_') {
			$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
		}
	}
	return $headers;
}

function set_request_headers($ch, $request)
{
	// headers to strip
	$strip = array("Content-Length", "Host");

	$headers = array();
	foreach ($request as $key => $value)
	{
		if ($key && !in_array($key, $strip))
		{
			$headers[] = "$key: $value";
		}
	}

	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
}
    
function get_response_headers($response)
{
	//需要忽略的头 
	$strip = array("Transfer-Encoding");

	//分割返回头成为数组
	$headers = explode("\n", $response);

	//逐个设置返回的头
	$result = [];
	foreach ($headers as &$header) {
		if (!$header) continue;
		$header = trim($header);
		if ($header === '') continue;

		$pos = strpos($header, ":");
		$key = substr($header, 0, $pos);

		if (!in_array($key, $strip)) {
			$result[] = trim($header);
		}
	}
	return $result;
}

function set_response_headers($headers)
{
	foreach ($headers as &$header) {
		header($header, FALSE);
	}
}
    
/**
* 设置POST数据
* @param array $post
*/
function set_post($ch, $post)
{
	//支持文件上传
	if (count($_FILES)) {
		foreach ($_FILES as $key => $file) {
			$parts = pathinfo($file["tmp_name"]);
			$name = $parts["dirname"] . "/" . $file["name"];
			rename($file["tmp_name"], $name);
			$post[$key] = "@" . $name;
		}
	} else if( is_array( $post ) ) {
		$post = http_build_query($post);
	}

	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
}
