<?php
namespace App\Services\Line;

use App\Http\Controllers\Controller;
use Goutte;


class ChannelAnalysisService extends Controller
{

	public function __construct()
	{

	}

	public function getDailyNumberDelivery($access_token, $target_date)
	{
		$header = [
			'Content-Type: application/json; charser=UTF-8',
			'Authorization: Bearer '.$access_token
		];

		$response = $this->getCurlAccess('https://api.line.me/v2/bot/insight/message/delivery?date='.$target_date, 'GET', $header);
//error_log(print_r($response,true)."\n",3,"/data/www/line/storage/logs/nishi_log.txt");

		return $response;
	}

	public function getDailyNumberFollowers($access_token, $target_date)
	{
		$header = [
			'Content-Type: application/json; charser=UTF-8',
			'Authorization: Bearer '.$access_token
		];

		$response = $this->getCurlAccess('https://api.line.me/v2/bot/insight/followers?date='.$target_date, 'GET', $header);
//error_log(print_r($response,true)."\n",3,"/data/www/line/storage/logs/nishi_log.txt");

		return $response;
	}

	public function getFollowersInfo($access_token)
	{
		$header = [
			'Content-Type: application/json; charser=UTF-8',
			'Authorization: Bearer '.$access_token
		];

		$response = $this->getCurlAccess('https://api.line.me/v2/bot/insight/demographic', 'GET', $header);
//error_log(print_r($response,true)."\n",3,"/data/www/line/storage/logs/nishi_log.txt");

		return $response;
	}

	public function getCurlAccess($access_url, $method = 'GET', $header = null, $list_post_data = [])
	{
		if( empty($access_url) ){
			return;
		}

		$ch = curl_init($access_url);

		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		if( preg_match("/post/i", $method) > 0 ){
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($list_post_data));
		}

		if( !is_null($header) ){
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		}

		$response = curl_exec($ch);

		curl_close($ch);

		return $response;
	}
}
