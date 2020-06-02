<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libs\SysLog;
use App\Model\Maintenance;
use Carbon\Carbon;
use Utility;
use DB;
use Artisan;

class AdminMasterMaintenanceController extends Controller
{
    //
	public function __construct()
	{
		//ログファイルのインスタンス生成
		//引数：ログの操作項目、ログファイルまでのフルパス
		$this->log_obj = new SysLog(config('const.operation_export_log_name'), config('const.system_log_dir_path').config('const.operation_history_file_name'));
	}

	/*
	 *  メンテナンス設定画面表示
	 */
	public function index()
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();
		
		$db_data = Maintenance::first();
		
		$disp_data = [
			'db_data'	=> $db_data,
			'ver'		=> time(),
		];
		
		return view('admin.master.maintenance', $disp_data);
	}

	/*
	 *  メンテナンス設定処理
	 */
	public function createSend(Request $request)
	{
		$this->validate($request, [
			'message'		=> 'bail|required|surrogate_pair_check|emoji_check'
		]);

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//現在時刻
		$now_date = Carbon::now();

		DB::insert("insert ignore into maintenances("
			. "mode, "
			. "body, "
			. "created_at, "
			. "updated_at) "
			. "values("
			. "".$request->input('mode').","
			. "'".addslashes($request->input('message'))."',"
			. "'".$now_date."', "
			. "'".$now_date."') "
			. "on duplicate key update "
			. "mode = {$request->input('mode')},"
			. "body = '".addslashes($request->input('message'))."'"
		);

		//メンテナンスモードON
		if( !empty($request->input('mode')) ){
			//メンテナンスモードをONにするコマンドを実行
			Artisan::call('down');

		//メンテナンスモードOFF
		}else{
			//メンテナンスモードをOFFにするコマンドを実行
			Artisan::call('up');			
		}
			
		return null;
	}

	/*
	 *  プレビュー画面表示
	 */
	public function preview()
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();
		
		$db_data = Maintenance::first();
		
		$disp_data = [
			'db_data'	=> $db_data,
			'ver'		=> time(),
		];
		
		return view('admin.master.maintenance_preview', $disp_data);
	}



}
