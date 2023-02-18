<?php
declare (strict_types = 1);

namespace App\Libs;

use Hyperf\Logger\LoggerFactory;

class Log
{
	public static function getLogger($sChannelName, $sSettingName = 'default')
	{
		return container()->get(LoggerFactory::class)->get($sChannelName, $sSettingName);
	}

	public static function info($sMsg, $aContext = [], $sChannelName = 'system')
	{
        return self::getLogger($sChannelName)->info($sMsg, $aContext);
	}

	public static function error($sMsg, $aContext = [], $sChannelName = 'system')
	{
        return self::getLogger($sChannelName)->error($sMsg, $aContext);
	}

}