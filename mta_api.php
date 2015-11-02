<?php 
require_once 'log.php';
require_once 'proxy-pass.php';

/*
id:  3101936025
key: ARJBX587JM7W
{
    "av": "1.3.900",
    "ch": "drcom",
    "dts": "-2",
    "ei": "Event_MTAServer_Start",
    "et": "1000",
    "idx": "296014",
    "kv": {
        "model": "C6802",
        "tokenid_model": "01fd53f575a51f75d887570121ff3741_C6802_wifi",
        "version": "1.3.900"
    },
    "ky": "WU6JJ64X7PWN",
    "mc": "4c:21:d0:43:2f:c2",
    "mid": "db4d04d2f59585e34e138b2e77d8afc686e2722d",
    "si": "1907732466",
    "ts": "1446462307",
    "ui": "357656050908647",
    "ut": "1"
}
*/


/*
ky	app key
ui	Unique Identifier, IMEI, mobile serial number
uid	user id, get from getDeviceID
mid	MID, MTA identify
mc	MAC adress
aid	android id

ei	event id
et	event type
ut	user type
ts	timestamps
dts	diff time
ch	channel
os	operating system type

sm	SleepTime minutes
sv	SDK version
md	Model of device
av	App version
mf	Manufacture
osv,ov	android sdk version
ver	version
ncts	need check times
cfg	configure json object
ia	is the first time activate

lg	language
ram	ram capacity
rom	rom capacity
sd	sd card capacity
tz	timezone
prod	product name
dpi	dot per inch, screen resolution 
sr	screen resolution 
wf	wifi name now using
wflist	wifi state list: 
	bs:	like mac address
	dBm:	sinal strength
	ss: 	wifi ssid
*/

define('SESSION_INTERNAL', 30);

function get_index()
{
	$ts = time();
	$data = file_get_contents('mta.session');

	if (empty($data)) {
		file_put_contents('mta.session', array('index'=>$ts, 'session_start'=>$ts, 'last_active'=>$ts));
		return $ts;
	}

}


function send_mta($data, $encode)
{
	$timestamp = time();
	//生成url
	$host = 'pingma.qq.com:80';
	$url = 'http://' . $host . 'mstat/report/?index=' . ;



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
		call_user_func_array($cb_before, [&$url, &$data_to_post, &$headers]);
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
	$accept_keys = array('ui','os','ky','id','abi','mf','sr','pcn','ram','ch','et','ei','pi');

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



//根据个性化配置，获得MTA上下文对象
function initMTAConfig($appKey, $ui, $uid, $mac)
{

}

//激活MTA，在其他方法调用前调用。并非必须。
function startStatService($context, $appKey, $mtaSdkVersion)
{

}

//主动触发一次会话。官方的SDK会话统计是自动的，当然这个版本就是手动的了。
function startNewSession($context)
{

}

//===============================================

function trackBeginPage($context, $pageName)
{

}


function trackEndPage($context, $pageName)
{

}

//===============================================

function trackCustomKVEvent($context, $event_id, $properties)
{

}

function trackCustomBeginKVEvent($context, $event_id, $properties)
{

}

function trackCustomEndKVEvent($context, $event_id, $properties)
{

}

//===============================================

function trackCustomEvent($content, $event_id, $args)
{

}

function trackCustomBeginEvent($content, $event_id, $args)
{

}

function trackCustomEndEvent($content, $event_id, $args)
{

}
