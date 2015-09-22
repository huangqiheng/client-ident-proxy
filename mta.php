<?php 
require_once 'log.php';
require_once 'proxy-pass.php';

function before_post_data($post)
{
	return $post;
}

function before_upstream_callback(&$url, &$data_to_post, &$headers)
{
	$path = parse_url($url, PHP_URL_PATH);
	$dec_obj = mta_decode($headers, $data_to_post, 'before_post_data');

	if (($dec_obj['status'] == 'ok') and ($dec_obj['res'])) {
		$data_to_post = $dec_obj['res'];
	}

	$mta_content = $dec_obj['ori'];
	$title = 'send '.$path.' '.keyval_str($mta_content);

	jsondb_logger('nofity', $title, [
		'url'=>$url,
		'post'=>$data_to_post,
		'bin2hex' => hex_view($data_to_post),
		'decode' => $mta_content,
		'headers'=>$headers,
		'dec_obj' => $dec_obj
	]);
} 

//id:  3101936025
//key: ARJBX587JM7W

function after_return_data($result)
{
	return $result;
}

function after_upstream_callback($info, &$headers, &$body)
{
	$url = $info['url'];
	$path = parse_url($url, PHP_URL_PATH);
	$dec_obj = mta_decode($headers, $body, 'after_return_data');

	if (($dec_obj['status'] == 'ok') and ($dec_obj['res'])) {
		$body = $dec_obj['res'];
	}

	$mta_content = $dec_obj['ori'];
	$title = 'recv '.$path.' '.keyval_str($mta_content);

	jsondb_logger('nofity', $title, [
		'url'=> $url,
		'info'=>$info,
		'headers'=>$headers,
		'body'=>$body,
		'bin2hex' => hex_view($body),
		'decode' => $mta_content,
		'dec_obj' => $dec_obj
	]);
}

forward('before_upstream_callback', 'after_upstream_callback');

//=========================
//=========================

function mta_decode($headers, $data, $cb_fliter=null)
{
	$encode_types = get_content_encoding($headers);;
	$types = explode(',', $encode_types);

	//jsondb_logger('notify', 'before: '.bin2hex($data));

	$packed = false;
	$res_data = $data;
	foreach($types as $type) {
		if ($type == 'rc4') {
			$res_data = mta_rc4($res_data);
		} elseif ($type == 'gzip') {
			$header = unpack('Nlength/Lgzip', $res_data);
			if (intval($header['gzip']) === 0x00088b1f) {
				$header = unpack('Nlength/a*body', $res_data);
				$res_data = $header['body'];
				$packed = true;
				//jsondb_logger('notify', 'len1: '.$header['length'], $header);
			}

			$res_data = gzdecode($res_data);
		}
	}

	if (empty($res_data)) {
		jsondb_logger('notify', 'error '.bin2hex($data));
		return array('status'=>'error', 'error'=>'decryption error');
	}

	$ori_data = json_decode($res_data);

	if ($cb_fliter) {
		$new_data = call_user_func($cb_fliter, $ori_data);
		if ($new_data) {
			$types = array_reverse($types);

			$res_data = json_encode($new_data);
			foreach($types as $type) {
				if ($type == 'rc4') {
					$res_data = mta_rc4($res_data);
					//jsondb_logger('notify', 'after rc4: '.bin2hex($res_data));
				} elseif ($type == 'gzip') {
					if ($packed) {
						$length = strlen($res_data);
						$res_data = gzencode($res_data);
						$res_data = pack('Na*', $length, $res_data);

						//jsondb_logger('notify', 'len2: '.$length);
					} else {
						$res_data = gzencode($res_data);
					}
					//jsondb_logger('notify', 'after zip: '.bin2hex($res_data));
				}
			}

			return array('status'=>'ok', 'ori'=>$ori_data, 'new'=>$new_data, 'res'=>$res_data);
		}
	}

	return array('status'=>'ok', 'ori'=>$ori_data, 'new'=>null, 'res'=>null);
}

function is_echoable($item)
{
	if(
		( !is_array( $item ) ) &&
		( ( !is_object( $item ) && settype( $item, 'string' ) !== false ) ||
		  ( is_object( $item ) && method_exists( $item, '__toString' ) ) )
	  )
	{
		return true;
	}
	return false;
}

function keyval_str($data)
{
	$res = '';

	if (is_array($data)) {
		foreach($data as $item) {
			foreach($item as $key=>$val) {
				if (is_echoable($val)) {
					$res .= $key.'='.$val.'; ';
				} else {
					$res .= $key.'='.json_encode($val).'; ';
				}
			}
		}
	} else {
		$res = json_encode($data).'; ';
	}
	return $res;
}

function hex_view($input)
{
	$input = bin2hex($input);
	return chunk_split($input,2,' ');
}

function mta_rc4($data)
{
	return rc4('03a976511e2cbe3a7f26808fb7af3c05', $data);
}

function mb_chr($char) {
	return mb_convert_encoding('&#'.intval($char).';', 'UTF-8', 'HTML-ENTITIES');
}

function mb_ord($char) {
	$result = unpack('N', mb_convert_encoding($char, 'UCS-4BE', 'UTF-8'));

	if (is_array($result) === true) {
		return $result[1];
	}
	return ord($char);
}

function mb_rc4($key, $str) {
	if (extension_loaded('mbstring') === true) {
		mb_language('Neutral');
		mb_internal_encoding('UTF-8');
		mb_detect_order(array('UTF-8', 'ISO-8859-15', 'ISO-8859-1', 'ASCII'));
	}

	$s = array();
	for ($i = 0; $i < 256; $i++) {
		$s[$i] = $i;
	}
	$j = 0;
	for ($i = 0; $i < 256; $i++) {
		$j = ($j + $s[$i] + mb_ord(mb_substr($key, $i % mb_strlen($key), 1))) % 256;
		$x = $s[$i];
		$s[$i] = $s[$j];
		$s[$j] = $x;
	}
	$i = 0;
	$j = 0;
	$res = '';
	for ($y = 0; $y < mb_strlen($str); $y++) {
		$i = ($i + 1) % 256;
		$j = ($j + $s[$i]) % 256;
		$x = $s[$i];
		$s[$i] = $s[$j];
		$s[$j] = $x;

		$res .= mb_chr(mb_ord(mb_substr($str, $y, 1)) ^ $s[($s[$i] + $s[$j]) % 256]);
	}
	return $res;
}

function rc4($key, $str) 
{
	$s = array();
	for ($i = 0; $i < 256; $i++) {
		$s[$i] = $i;
	}
	$j = 0;
	for ($i = 0; $i < 256; $i++) {
		$j = ($j + $s[$i] + ord($key[$i % strlen($key)])) % 256;
		$x = $s[$i];
		$s[$i] = $s[$j];
		$s[$j] = $x;
	}
	$i = 0;
	$j = 0;
	$res = '';
	for ($y = 0; $y < strlen($str); $y++) {
		$i = ($i + 1) % 256;
		$j = ($j + $s[$i]) % 256;
		$x = $s[$i];
		$s[$i] = $s[$j];
		$s[$j] = $x;
		$res .= $str[$y] ^ chr($s[($s[$i] + $s[$j]) % 256]);
	}
	return $res;
}

