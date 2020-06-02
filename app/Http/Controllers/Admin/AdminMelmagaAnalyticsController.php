<?php

namespace App\Http\Controllers\Admin;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libs\SysLog;
use App\Model\Admin;
use App\Model\Year_result_access_log;
use App\Model\Month_result_access_log;
use App\Model\Day_result_access_log;
use App\Model\Melmaga_history_log;
use Utility;
use DB;

class AdminMelmagaAnalyticsController extends Controller
{
	private $log_obj;

	//
	public function __construct()
	{
		$this->log_obj = new SysLog(config('const.operation_export_log_name'), config('const.system_log_dir_path').config('const.operation_history_file_name'));
	}
	
	/*
	 * 集計-メルマガ解析-年
	 */
	public function index()
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['analysis_top'].",{$user['login_id']}");
		
		//メルマガ全配信データ取得
		$access_all_data = DB::table("melmaga_history_logs")->select("melmaga_id", DB::raw("count(melmaga_id) as count"), "sort_date")->groupBy("melmaga_id", "sort_date")->orderBy("sort_date", "desc")->paginate(config('const.admin_client_list_limit'));

		$listMelmaga = [];
		foreach($access_all_data as $lines){
			if( empty($listMelmaga[$lines->melmaga_id]) ){
				$listMelmaga[$lines->melmaga_id] = [
					'read'		 => 0,
					'no_read'	 => 0,
					'total'		 => 0,
					'send_date'		 => preg_replace("/(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/", "$1/$2/$3 $4:$5", $lines->sort_date),
				];
			}
			$listMelmaga[$lines->melmaga_id]['total'] = $lines->count;
		}

		//メルマガ配信閲覧済データ取得
		$access_data = DB::select("select melmaga_id,count(melmaga_id) as count from melmaga_history_logs where read_flg = 1 group by melmaga_id,sort_date order by sort_date desc limit ".config('const.admin_client_list_limit'));
		foreach($access_data as $lines){
			if( !empty($listMelmaga[$lines->melmaga_id]) ){
				$listMelmaga[$lines->melmaga_id]['read'] = $lines->count;
			}
		}
		
		//メルマガ配信未閲覧データ取得
		$access_data = DB::select("select melmaga_id,count(melmaga_id) as count from melmaga_history_logs where read_flg = 0 group by melmaga_id,sort_date order by sort_date desc limit ".config('const.admin_client_list_limit'));
		foreach($access_data as $lines){
			if( !empty($listMelmaga[$lines->melmaga_id]) ){
				$listMelmaga[$lines->melmaga_id]['no_read'] = $lines->count;
			}
		}

		$disp_data = [
			'total'			=> $access_all_data->total(),
			'currentPage'	=> $access_all_data->currentPage(),
			'lastPage'		=> $access_all_data->lastPage(),
			'links'			=> $access_all_data->links(),
			'db_data'		=> $listMelmaga,
			'ver'			=> time(),
		];
		
		return view('admin.analytics.melmaga.analysis', $disp_data);
	}

	/*
	 * 集計-メルマガ解析-閲覧済
	 */
	public function viewVisited($melmaga_id)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['analysis_top'].",{$user['login_id']}");

		//メルマガ閲覧済データ取得
		$access_data = DB::table("melmaga_history_logs")->select("client_id")->where("read_flg", 1)->where('melmaga_id', $melmaga_id)->orderBy("sort_date")->paginate(config('const.admin_client_list_limit'));

		$listMelmaga = [];
		foreach($access_data as $lines){
			$listMelmaga[$melmaga_id][] = $lines->client_id;
		}
		
		$disp_data = [
			'total'			=> $access_data->total(),
			'currentPage'	=> $access_data->currentPage(),
			'lastPage'		=> $access_data->lastPage(),
			'links'			=> $access_data->links(),
			'db_data'		=> $listMelmaga,
			'ver'			=> time(),
		];
		
		return view('admin.analytics.melmaga.analysis_visited', $disp_data);
	}

	/*
	 * 集計-メルマガ解析-閲覧済
	 */
	public function viewUnseen($melmaga_id)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['analysis_top'].",{$user['login_id']}");

		//メルマガ未閲覧データ取得
		$access_data = DB::table("melmaga_history_logs")->select("client_id")->where("read_flg", 0)->where('melmaga_id', $melmaga_id)->orderBy("sort_date")->paginate(config('const.admin_client_list_limit'));

		$listMelmaga = [];
		foreach($access_data as $lines){
			$listMelmaga[$melmaga_id][] = $lines->client_id;
		}
		
		$disp_data = [
			'total'			=> $access_data->total(),
			'currentPage'	=> $access_data->currentPage(),
			'lastPage'		=> $access_data->lastPage(),
			'links'			=> $access_data->links(),
			'db_data'		=> $listMelmaga,
			'ver'			=> time(),
		];
		
		return view('admin.analytics.melmaga.analysis_unseen', $disp_data);
	}
}
