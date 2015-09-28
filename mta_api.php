<?php 


//根据个性化配置，获得MTA上下文对象
function initMTAConfig($configs)
{

}

//激活MTA，在其他方法调用前调用。并非必须。
function startStatService($context, $appkeyi, $mtaSdkVersion)
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
