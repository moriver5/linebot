<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libs\SysLog;
use App\Model\LineUser;
use App\Model\LineUserProfile;
use App\Model\LineOfficialAccount;
use DB;
use Utility;
use Carbon\Carbon;



class AdminLineClientController extends Controller
{
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//ログファイルのインスタンス生成
		//引数：ログの操作項目、ログファイルまでのフルパス
		$this->log_export_obj	 = new SysLog(config('const.operation_export_log_name'), config('const.system_log_dir_path').config('const.operation_export_file_name'));
		$this->log_history_obj	 = new SysLog(config('const.operation_export_log_name'), config('const.system_log_dir_path').config('const.operation_history_file_name'));
		$this->log_obj			 = new SysLog(config('const.operation_point_log_name'), config('const.system_log_dir_path').config('const.operation_point_history_file_name'));
	}

	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
		//動的クエリを生成するため
		$db_data = LineUser::paginate(config('const.admin_client_list_limit'));
		
		//画面表示用配列
		$disp_data = [
			'db_data'		=> $db_data,
			'total'			=> $db_data->total(),
			'currentPage'	=> $db_data->currentPage(),
			'lastPage'		=> $db_data->lastPage(),
			'links'			=> $db_data->links(),
			'ver'			=> time()
		];
		
		return view('admin.client.line_index', $disp_data);
	}

	/*
	 * LINEチャンネル別ユーザーリスト
	 */
	public function channelUserList($channel_id)
	{
		//ログイン管理者情報取得
//		$user = Utility::getAdminDefaultDispParam();
		
		$db_data = LineOfficialAccount::query()
			->join('line_users', 'line_users.line_basic_id', '=', 'line_official_accounts.line_basic_id')
			->join('line_user_profiles', 'line_user_profiles.user_line_id', '=', 'line_users.user_line_id')
			->select('line_users.id', 'line_users.disable', 'line_users.follow_flg', 'line_users.block24h', 'line_users.block24h', 'line_users.line_basic_id', 'line_users.user_line_id', 'line_user_profiles.name', 'line_user_profiles.message', 'line_users.created_at')
			->where('line_users.line_basic_id', $channel_id)
			->orderBy('line_users.id', 'desc')
			->paginate(config('const.admin_key_list_limit'));

		$disp_data = [
			'db_data'		=> $db_data,
			'channel_id'	=> $channel_id,
			'total'			=> $db_data->total(),
			'currentPage'	=> $db_data->currentPage(),
			'lastPage'		=> $db_data->lastPage(),
			'links'			=> $db_data->links(),
			'ver'			=> time(),
		];
		
		return view('admin.client.line_channel_user', $disp_data);
	}

	public function edit(Request $request, $page, $channel_id, $line_id)
	{		
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();
		
		//DBのusersテーブルからデータ取得
		$db_data = LineUser::join('line_user_profiles', 'line_user_profiles.user_line_id', '=', 'line_users.user_line_id')
			->select('line_user_profiles.user_line_id', 'line_user_profiles.name', 'line_user_profiles.image', 'line_user_profiles.message', 'line_users.follow_flg', 'line_users.block24h', 'line_users.created_at', 'line_users.updated_at', 'line_users.ad_cd')
			->where('line_users.line_basic_id', $channel_id)
			->where('line_user_profiles.user_line_id', $line_id)
			->first();

		//編集データがない場合、顧客データ一覧へリダイレクト
		if( empty($db_data) ){
			return redirect(config('const.base_admin_url').config('const.admin_client_path'));
		}

		//戻るリンクのデフォルトを顧客管理一覧に設定
		$back_url = config('const.base_admin_url').'/'.config('const.client_url_path').'?page='.$page;
		
		//閲覧者検索から来た場合の戻るリンク
		if( !empty($request->input('back')) ){
//			$back_url = config('const.base_admin_url').'/'.config('const.visitor_url_path').'?page=';
		}

		$back_btn_flg = 1;
		//戻るボタンを表示するかどうか
		if( !is_null($request->input('back_btn')) ){
			$back_btn_flg = $request->input('back_btn');
		}

		$last_click_date = "--";
		$db_click_data = DB::select("select * from line_click_users where user_line_id = '".$db_data->user_line_id."' and click > 0 order by updated_at desc limit 1");
		if( !empty($db_click_data) ){
			$last_click_date = $db_click_data[0]->updated_at;
		}

		//
		$disp_data = [
			'last_click_date'	=> $last_click_date,
			'basic_id'			=> $db_data->line_basic_id,
			'line_id'			=> $line_id,
			'back_btn_flg'		=> $back_btn_flg,
			'back_url'			=> $back_url,
			'db_data'			=> $db_data,
			'page'				=> $page,
			'ver'				=> time(),
		];
		
		return view('admin.client.line_edit', $disp_data); 
	}

	/*
	 * クライアント編集処理
	 */
	public function store(Request $request)
	{
		$basic_id = $request->input('basic_id');
		$line_id = $request->input('line_id');

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();
		
		$now_date = Carbon::now();

		$update_value = [
			'disable' => $request->input('del') ? $request->input('del'):0
		];

//error_log(print_r($update_user_value, true)."\n",3,"/data/www/melmaga/storage/logs/nishi_log.txt");
		$update = LineUser::where('line_basic_id', $basic_id)->where('user_line_id', $line_id)->update($update_value);

		//ログ出力
		$this->log_history_obj->addLog(config('const.admin_display_list')['client_edit_update'].",{$user['login_id']}");

		return null;
	}


}
