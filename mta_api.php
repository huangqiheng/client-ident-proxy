<?php 
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