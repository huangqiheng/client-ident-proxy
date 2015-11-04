<?php 
require_once 'log.php';
require_once 'proxy-pass.php';

function before_post_data($headers, $post)
{
	foreach($post as $item) {
		$item->ky = 'ARJBX587JM7W';
	}

        $uri = $_SERVER['REQUEST_URI'];
        $title = '┗send '.$uri.' ┅ '.keyval_str($post);

        jsondb_logger('nofity', $title, [
                'url'=>$uri,
                'headers'=>$headers,
                'post'=>$post
        ]);

        return $post;
}

function after_return_data($headers, $result)
{
        $uri = $_SERVER['REQUEST_URI'];
        $title = '┏recv '.$uri.' ┅ '.keyval_str($result);

        jsondb_logger('nofity', $title, [
                'url'=>$uri,
                'headers'=>$headers,
                'result'=>$result
        ]);
        return $result;
}

function before_upstream_callback(&$url, &$data_to_post, &$headers)
{
        $dec_obj = mta_decode($headers, $data_to_post, 'before_post_data');

        if (($dec_obj['status'] == 'ok') and ($dec_obj['res'])) {
                $data_to_post = $dec_obj['res'];
        }
}

function after_upstream_callback($info, &$headers, &$body)
{
        $dec_obj = mta_decode($headers, $body, 'after_return_data');

        if (($dec_obj['status'] == 'ok') and ($dec_obj['res'])) {
                $body = $dec_obj['res'];
        }
}


forward('before_upstream_callback', 'after_upstream_callback');

//=========================
//=========================

function mta_decode($headers, $data, $cb_fliter=null)
{
        if (empty($data)) {
                return array('status'=>'error', 'error'=>'no data posted');
        }

	$encode_types = get_content_encoding($headers);;
	$types = explode(',', $encode_types);

	//jsondb_logger('notify', 'before: '.bin2hex($data));

	$packed = false;
	$res_data = $data;
	foreach($types as $type) {
		if ($type == 'rc4') {
			$res_data = mta_rc4($res_data);
		} elseif ($type == 'gzip') {
			$header = unpack('Nlength/Sgzip', $res_data);
			if (intval($header['gzip']) === 0x8b1f) {
				$header = unpack('Nlength/a*body', $res_data);
				$res_data = $header['body'];
				$packed = true;
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
		$new_data = call_user_func($cb_fliter, $headers, $ori_data);
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
	if (empty($data)) {
		return '';
	}

	$accept_keys = array('idx','si','ts','ui','ky','mf','apn','pcn','ch','et','ei','pi');

	$res = '';
	if (is_array($data)) {
		foreach($data as $item) {
			foreach($item as $key=>$val) {
				if (is_echoable($val)) {
					$appends = $key.'='.$val.'; ';
				} else {
					$appends = $key.'='.json_encode($val).'; ';
				}

				if (in_array($key, $accept_keys)){
					$res .= $appends;
					$accept_keys = array_diff($accept_keys, array($key));
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

