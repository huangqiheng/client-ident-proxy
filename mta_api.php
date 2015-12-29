<?php 
require_once 'log.php';

define('APP_KEY', 'ARJBX587JM7W');
define('APP_PAGEAGE_NAME', 'com.doctor.dtservice');
define('RC4_KEY', '03a976511e2cbe3a7f26808fb7af3c05');
define('HMAC_KEY', 'iikVs3FGzEQ23RaD1JlHsSWSI5Z26m2hX3gO51mH3ag=');
define('FB_KEY', 'nDkb9nMIizcj2RDehplOjn+Q');
define('SESSION_INTERNAL', 30);  //set session internal, spec 30s
define('PROCESS_INTERNAL_MIN', 8); //simunate app restart by user, this is min lower limit
define('PROCESS_INTERNAL_MAX', 20); //max lower limit that would be generated
define('DATA_DIR', __DIR__.'/data');

function first_active()
{
	$hard = get_device_info();
	$soft = get_appapk_info();

	$post_data = array(
		'ui' => $hard['ui'],
		'ky' => $soft['ky'],
		'idx' => '1',
		'ts' => '1446705480',
		'ut' => '0',
		'dts' => '0',
		'si' => '1645620817',
		'ncts' => '1',
		'ia' => '1',
		'mc' => 'db =>74 =>03 =>20 =>75 =>38',
		'mid' => '0',
		'et' => '2',
		"ev" => {
			"tn": "0",
			"abi": "x86",
			"id": "tt",
			"lch": "com.android.launcher3",
			"md": "TianTian",
			"fng": "TTAndroid/ttVM_Hdragon/ttVM_Hdragon:4.3/tt/eng.root.20151019.135622:userdebug/test-keys",
			"sv": "2.0.2",
			"mf": "TiantianVM",
			"dpi": "320.0*320.0",
			"abi2": "armeabi-v7a",
			"apn": "com.tencent.mtademo",
			"prod": "ttVM_Hdragon",
			"tags": "test-keys",
			"os": "1",
			"ov": "18",
			"rom": "33566/33820",
			"op": "46000",
			"sr": "720*1280",
			"cpu": array(
				'n' => 2,
				'na' => 0
			),
			"sd": "34896/34897",
			"pcn": APP_PAGEAGE_NAME,
			"av": "2.1.1",
			"tz": "Asia/Shanghai",
			"cn": "WIFI",
			"ram": "1890/2119",
			"osd": "ttVM_Hdragon-userdebug 4.3 tt eng.root.20151019.135622 test-keys",
			"lg": "zh",
			"ch": "play",
			"sen": "1",
			"wflist": array(
				'bs' => 
			
			), "[{\"bs\":\"01:80:c2:00:00:03\",\"ss\":\"WiredSSID\"}]",
			"wf": "{\"bs\":\"01:80:c2:00:00:03\",\"ss\":\"\\\"WiredSSID\\\"\"}",
			"osn": "4.3"
		}
	);
}

function get_session_info()
{
	$ts = time();
	$data = open_local('mta.session', true);

	update_si($ts, $data);
	update_index($ts, $data);
	update_idx($data);

	return save_local('mta.session', $data);
}

function get_device_info()
{
	if ($data = open_local('mta.hardware')) {return $data;}

	$data = array();
	$data['ui'] = imei_random();
	$data['mc'] = mac_random();

	$data['tn'] = '10';
	$data['abi'] = 'armeabi-v7a';
	$data['abi2'] = 'armeabi';
	$data['lch'] = 'com.doctor.launcher';
	$data['md'] = 'c6802';
	$data['mf'] = 'gz_drcom';
	$data['dpi'] = '342.899*341.034';
	$data['rom'] = '6233/12657';
	$data['sr'] = '1080*1824';
	$data['cpu'] = array(
		'n' => 4, 'fn' => 300,
		'na' => 'ARMv7 Processor rev 0 (v7l)'
	);
	$data['sd'] = '6233/12657';
	$data['ram'] = '360/1777';
	$data['sen'] = '1,2,14,4,16,8,5,9,10,11,18,19,17,15,20,3,33171006';

	//build options
	$data['id'] = 'doctor_id';
	$data['tags'] = 'release-keys';
	$data['prod'] = 'c6802';
	$data['osn'] = '4.4.4';
	$data['osd'] = $data['id'];
	$data['fng'] = implode('/',array($data['mf'],$data['prod'].':'.$data['osn'],$data['osd'],'k___jQ:user',$data['tags']));

	return save_local('mta.hardware', $data);
}

function get_appapk_info()
{
	if ($data = open_local('mta.software')) {return $data;}

	$data = array();
	$data['ky'] = APP_KEY;
	$data['av'] = '1.3.900';
	$data['sv'] = '2.0.2';
	$data['apn'] = 'com.drcom.DuoDian';
	$data['os'] = '1';
	$data['ov'] = '19';
	$data['op'] = '46001';
	$data['lg'] = 'zh';
	$data['ch'] = 'drcom';
	$data['pcn'] = 'com.drcom.DuoDian';
	$data['tz'] = 'Asia/Shanghai';
	$data['cn'] = 'WIFI';
	$data['wf'] = array('bs' => '08:57:00:61:95:ca','ss' => '"ands_home_play"');
	$data['wflist'] = array(
		array('bs' => '08:57:00:61:95:ca','ss' => 'ands_home_play'),
		array('bs' => '78:a1:06:6e:1e:f2','ss' => 'huazi'));

	return save_local('mta.software', $data);
}

function open_local($file, $ret_arr=false)
{
	$file = DATA_DIR . $file;
	$data = file_get_contents($file);
	if ($data) {
		$res_data = json_decode($data, true);
		if (!empty($res_data)) {
			return $res_data;
		}
	}

	if ($ret_arr) {
		return array();
	} else {
		return false;
	}
}

function save_local($file, $data)
{
	$file = DATA_DIR . $file;
	file_put_contents($file, json_encode($data));
	return $data;
}


$sample4 = array(
	"ui" => "357656050908647",
	"os" => "1",
	"ky" => "ARJBX587JM7W",
	"idx" => "53039",
	"ts" => "1442948000",
	"ut" => "1",
	"av" => "1.0",
	"dts" => "-1",
	"si" => "1910584280",
	"mc" => "4c:21:d0:43:2f:c2",
	"ei" => "trackCustomKVEvent",
	"mid" => "db4d04d2f59585e34e138b2e77d8afc686e2722d",
	"ch" => "play",
	"et" => "1000"
);


//$sample['ky'] = 'A54F7VWNHT4R';
//$sample['ky'] = 'ARJBX587JM7W';
//$sample['ky'] = 'A52PU69EEQKA';

$sample4['ky'] = 'A52PU69EEQKA';
$sample4['mid'] = "fb4d04d2f59585e34e138b2e77d8afc686e2722d";
$res = send_mta($sample4);

output($res, true);

function send_mta($data, $encode='rc4')
{
	$session = get_session_info();
	$data['ts'] = time();
	$data['idx'] = $session['idx'];
	$data['si'] = $session['si'];

	//生成url
	$url = 'http://pingma.qq.com:80/mstat/report/?index=' . $session['index'];

	output($url);
	output($data);
	output($session);

	//加密数据
	$en_data = mta_encode($data, $encode);

	//生产http头
	$headers = array(
		'Accept-Encoding' => 'gzip',
		'Connection' => 'Keep-Alive',
		'Content-Encoding' => $encode
	);

	//初始化curl选项
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        
	//将生成的头，设置在curl中
	$set_headers = array();
	foreach ($headers as $key => $value) {
		$set_headers[] = "$key: $value";
	}
	curl_setopt($ch, CURLOPT_HTTPHEADER, $set_headers);

	//设置POST数据
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $en_data);
        
	//执行curl请求
	//要防止长连接用这种方法 fixme 
        $data = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);


	//获取返回的body内容
        $body = $info["size_download"] ? substr($data, $info["header_size"], $info["size_download"]) : "";
        
        $headers_str = substr($data, 0, $info["header_size"]);
        $headers = get_response_headers($headers_str);
	$encoding = get_content_encoding($headers);

	//output($info);
        
	//输出html内容到浏览器
	$ori_res = mta_decode($body, $encoding);
	return $ori_res;
}

function output($obj, $is_exit=false) 
{
	echo print_r($obj, true) . "\r\n"; 
	if ($is_exit) {
		exit;
	}
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

function update_idx(&$data)
{
	if (isset($data['idx'])) {
		$idx = intval($data['idx']);
	} else {
		$idx = 0;
	}

	if ($idx > 0) {
		if ($idx % 1000 == 0) {
			$new_idx = $idx + 1000;
			if ($idx >= 2147383647) {
				$new_idx = 0;
			}
			$idx = $new_idx;
		}
	} else {
		$idx += 1000;
	}

	$idx++;
	$data['idx'] = $idx;
	return $idx;
}

function update_index($ts, &$data)
{
	$app_restart = false;

	if (isset($data['index'])) {
		$index = intval($data['index']);
		$counter = $data['index_restart_counter'];
		if ($counter > 0) {
			$index++;
			$data['index_restart_counter']--;
		} else {
			$index = $ts;
			$app_restart = true;
		}
	} else {
		$index = $ts;
		$app_restart = true;
	}

	$data['index'] = $index;

	if ($app_restart) {
		$data['index_restart_counter'] = mt_rand(PROCESS_INTERNAL_MIN, PROCESS_INTERNAL_MAX);
	}

	return $index;
}

function update_si($ts, &$data)
{
	if (isset($data['si'])) {
		$si = intval($data['si']);
		$last_active = intval($data['last_active']);
		if (($ts - $last_active) > SESSION_INTERNAL) {
			$si = mt_rand();
		}
	} else {
		$si = mt_rand();
	}

	$data['si'] = $si;
	$data['last_active'] = $ts;

	return $si;
}

function mta_encode($data, $encode_types)
{
	$types = explode(',', $encode_types);
	$types = array_reverse($types);

	if (count($types) > 1) {
		$packed = true;
	}

	$res_data = json_encode($data);
	foreach($types as $type) {
		if ($type == 'rc4') {
			$res_data = mta_rc4($res_data);
		} elseif ($type == 'gzip') {
			if ($packed) {
				$length = strlen($res_data);
				$res_data = gzencode($res_data);
				$res_data = pack('Na*', $length, $res_data);
			} else {
				$res_data = gzencode($res_data);
			}
		}
	}

	return $res_data;
}

function mta_decode($data, $encode_types)
{
	$types = explode(',', $encode_types);

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
		return null;
	}

	return json_decode($res_data);
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

function requst_mid_data()
{
	$data = array();
	//$data['ts'] = time();
	//$data['ky'] = );
	//$data['si'] = );
	$data['mid'] = '0';
	$data['rip'] = gethostbyname('pingmid.qq.com');
	$data['ui'] = imei_random();
	$data['mc'] = mac_random();
	$data['et'] = '2';


}

function mac_random()
{
	$vals = array('0','1','2','3','4','5','6','7','8','9','A','B','C','D','E','F');
	if (count($vals) >= 1) {
		$mac = array("00"); // set first two digits manually
		while (count($mac) < 6) {
			shuffle($vals);
			$mac[] = $vals[0] . $vals[1];
		}
		$mac = implode(":", $mac);
	}
	return $mac;
}

function imei_random() 
{
	$code = intRandom(14);
	$position = 0;
	$total = 0;
	while ($position < 14) {
		if ($position % 2 == 0) {
			$prod = 1;
		} else {
			$prod = 2;
		}
		$actualNum = $prod * $code[$position];
		if ($actualNum > 9) {
			$strNum = strval($actualNum);
			$total += $strNum[0] + $strNum[1];
		} else {
			$total += $actualNum;
		}
		$position++;
	}
	$last = 10 - ($total % 10);
	if ($last == 10) {
		$imei = $code . 0;
	} else {
		$imei = $code . $last;
	}
	return $imei;
}

function intRandom($size) 
{
	$validCharacters = utf8_decode("0123456789");
	$validCharNumber = strlen($validCharacters);
	$int = '';
	while (strlen($int) < $size) {
		$index = mt_rand(0, $validCharNumber - 1);
		$int .= $validCharacters[$index];
	}
	return $int;
}

function hmac_key($res_rand)
{
	$hmac_secret = decode_key(HMAC_KEY);
	$res = hash_hmac('sha1', $res_rand, $hmac_secret);
	return strtoupper($res);
}


function feedback_key()
{
	return decode_key(FB_KEY);
}

function decode_key($key)
{
	return mta_rc4(base64_decode($key));
}

function mta_rc4($data)
{
	return rc4(RC4_KEY, $data);
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

/*
ky	app key
ui	Unique Identifier, IMEI, mobile serial number
cui 	current user id
uid	user id, get from getDeviceID
mid	MID, MTA identify
mc	MAC adress
aid	android id

si	session identifier, change when session timeout
index	url querystring, break for app restart

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
av	current App version
mf	Manufacture
osv,ov	android sdk version
ver	version
ncts	need check times
cfg	configure json object
impt	is importance
ia	is the first time activate

jb	is jail break
apn	app package name
pcn	current process name
cn	current network name
tn	telephony network type
sen	all sensor
op 	sim card operator
lg	language
ram	system memory, ram capacity
rom	rom memory, rom capacity
asg	current app sha1 signature
im	device IMSI
sd	sd card capacity, external storage info
tz	timezone
osd	build display
osn	build version release
prod	build product name
tags	build tags
id	build id
fng	build finger print
lch	launcher package name, like com.sonyericsson.home 
dpi	dot per inch, screen resolution 
sr	screen resolution 
wf	wifi name now using
wflist	wifi state list: 
	bs:	like mac address
	dBm:	sinal strength
	ss: 	wifi ssid
*/

