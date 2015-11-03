<?php 
require_once 'log.php';

define('RC4_KEY', '03a976511e2cbe3a7f26808fb7af3c05');
define('FB_KEY', 'nDkb9nMIizcj2RDehplOjn+Q');
define('REQUEST_TIMEOUT', 5);
define('SESSION_INTERNAL', 30);  //设置session间隔是30秒
define('PROCESS_RESTART_MIN', 8); //设置进程从启时机的计数，至少是
define('PROCESS_RESTART_MAX', 20); //设置进程从启时机的计数，最长是

$sample = array(
	'ui' =>  '357656050908647',
	'os' =>  '1',
	'ky' =>  'ARJBX587JM7W',
	'ev' =>  array(
		'tn' =>  '10',
		'abi' =>  'armeabi-v7a',
		'id' =>  '14.4.A.0.108',
		'lch' =>  'com.sonyericsson.home',
		'md' =>  'C6802',
		'fng' =>  'Sony/C6802/C6802 => 4.4.4/14.4.A.0.108/k___jQ => user/release-keys',
		'sv' =>  '2.0.4',
		'mf' =>  'Sony',
		'dpi' =>  '342.899*341.034',
		'abi2' =>  'armeabi',
		'apn' =>  'com.tencent.mtademo',
		'prod' =>  'C6802',
		'wflist' => array(
			array('bs'=>'08:57:00:61:95:ca', 'dBm'=> -34, 'ss'=>'ands_home_play'),
			array('bs'=>'78:a1:06:6e:1e:f2', 'dBm'=> -82, 'ss'=>'huazi')
		),
		'tags' =>  'release-keys',
		'os' =>  '1',
		'ov' =>  '19',
		'rom' =>  '6233/12657',
		'op' =>  '46001',
		'sr' =>  '1080*1824',
		'cpu' => array(
			'n' => 4,
			'fn' => 300,
			'na' => 'ARMv7 Processor rev 0 (v7l)'
		),
		'sd' =>  '6233/12657',
		'im' =>  '460018768600330',
		'pcn' =>  'com.tencent.mtademo',
		'av' =>  '1.0',
		'asg' => "89:6E:F8:F1:42:3B:32:5A:03:CE:AF:3D:78:7C:8D:3A:32:E4:84:B5",
		'tz' =>  'Asia/Shanghai',
		'cn' =>  'WIFI',
		'ram' =>  '315/1777',
		'osd' =>  '14.4.A.0.108',
		'lg' =>  'zh',
		'ch' =>  'play',
		'sen' =>  '1,2,14,4,16,8,5,9,10,11,18,19,17,15,20,3,33171006',
		'wf' => array(
			'bs' => '08:57:00:61:95:ca',
			'ss' => 'ands_home_play'
		),
		'osn' =>  '4.4.4'
	),
	'idx' =>  '52001',
	'ts' =>  '1442947739',
	'mc' =>  '4c:21:d0:43:2f:c2',
	'mid' =>  'db4d04d2f59585e34e138b2e77d8afc686e2722d',
	'ut' =>  '1',
	'et' =>  '2',
	'dts' =>  '-1',
	'si' =>  '660673021'
);

$sample2 = array(
    "av" => "1.3.900",
    "ch" => "drcom",
    "dts" =>  "-2",
    "ei" =>  "Event_MTAServer_Start",
    "et" =>  "1000",
    "idx" =>  "296014",
    "kv" => array(
        "model" => "C6802",
        "tokenid_model" => "01fd53f575a51f75d887570121ff3741_C6802_wifi",
        "version" => "1.3.900"
    ),
    "ky" => "WU6JJ64X7PWN",
    "mc" => "4c:21:d0:43:2f:c2",
    "mid" => "db4d04d2f59585e34e138b2e77d8afc686e2722d",
    "si" => "1907732466",
    "ts" => "1446462307",
    "ui" => "357656050908647",
    "ut" => "1"
);

$sample3 = array(
	"ui" =>  "357656050908647",
	"ky" => "ARJBX587JM7W",
	"idx" => "19020",
	"ts" => "1442951601",
	"kv" => array(
		"model" => "C6802",
		"tokenid_model" => "01fd53f575a51f75d887570121ff3741_C6802_wifi",
		"version" => "1.3.900"
	),
	"ut" => "1",
	"av" =>  "1.3.900",
	"dts" => "-2",
	"si" => "557613767",
	"mc" => "4c:21:d0:43:2f:c2",
	"ei" => "Event_MTAServer_Start",
	"mid" => "db4d04d2f59585e34e138b2e77d8afc686e2722d",
	"ch" => "drcom",
	"et" => "1000"

);

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
//$sample2['ky'] = 'A54F7VWNHT4R';
$sample['ky'] = 'ARJBX587JM7W';
$sample2['ky'] = 'ARJBX587JM7W';
$sample3['ky'] = 'A54F7VWNHT4R';

$sample4['ky'] = 'ARJBX587JM7W';
$sample4['ut'] = 1;
$res = send_mta($sample4);
echo json_encode($res, true) . "\r\n";
/*
$res = send_mta($sample4);
echo json_encode($res, true) . "\r\n";
*/

function send_mta($data, $encode='rc4')
{
	$session = get_session();
	$data['ts'] = time();
	$data['idx'] = $session['idx'];
	$data['si'] = $session['si'];

	echo json_encode($data,true) . "\r\n";

	//加密数据
	$en_data = mta_encode($data, $encode);

	//生产http头
	$headers = array(
		'Accept-Encoding' => 'gzip',
		'Connection' => 'Keep-Alive',
		'Content-Encoding' => $encode
	);

	//生成url
	$url = 'http://pingma.qq.com:80/mstat/report/?index=' . $session['index'];

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

	echo json_encode($info,true) . "\r\n";
        
        
	//输出html内容到浏览器
	$ori_res = mta_decode($body, $encoding);
	return $ori_res;
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
		$idx = -1;
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
		$data['index_restart_counter'] = mt_rand(PROCESS_RESTART_MIN, PROCESS_RESTART_MAX);
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

function get_session()
{
	$ts = time();
	$data = file_get_contents('mta.session');
	$data = json_decode($data, true);

	if (empty($data)) {
		$data = array();
	}

	update_si($ts, $data);
	update_index($ts, $data);
	update_idx($data);

	file_put_contents('mta.session', json_encode($data));
	return $data;
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

function feedback_key()
{
	$str = base64_decode(FB_KEY);
	return mta_rc4($str);
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
ia	is the first time activate

jb	is jail break
apn	app package name
pcn	current process name
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

