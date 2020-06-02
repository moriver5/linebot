<?php

namespace App\Libs;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use App\Model\Access_log;

class SysLog
{
	protected $custom_log;

	/*
	 * ログ書き込むための初期処理
	 * 引数：ログの操作項目、ログファイルまでのフルパス
	 */
	public function __construct($logName, $saveFile)
	{
		$this->custom_log = new Logger($logName);
		$this->custom_log->pushHandler(new StreamHandler($saveFile, Logger::INFO));
	}
	
	/*
	 * ログを書き込む
	 */
	public function addLog($log_value)
	{
		$this->custom_log->addInfo($log_value);
	}
}



