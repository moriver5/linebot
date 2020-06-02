<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libs\SysLog;
use App\Model\LineOfficialAccount;
use App\Model\AdminChannelAllowList;
use App\Services\Line\ChannelAnalysisService;
use DB;
use Utility;
use Carbon\Carbon;

class AdminMasterLineChannelController extends Controller
{
	private $log_obj;

	public function __construct()
	{
		//ログファイルのインスタンス生成
		//引数：ログの操作項目、ログファイルまでのフルパス
		$this->log_obj = new SysLog(config('const.operation_export_log_name'), config('const.system_log_dir_path').config('const.operation_history_file_name'));
	}

	/*
	 *  LINEチャンネル画面表示
	 */
	public function index($line_basic_id)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();
		
		$db_data = LineOfficialAccount::query()
			->whereIn('line_official_accounts.line_basic_id',function($query) use($user){
				$query->select('line_basic_id')->from('admin_channel_allow_lists')->where('admin_id', $user['id']);
			})
			->where('line_official_accounts.line_basic_id', $line_basic_id)
			->paginate(config('const.admin_key_list_limit'));
		
		$disp_data = [
			'db_data' => $db_data,
			'ver' => time(),
		];
		
		return view('admin.channel.index', $disp_data);
	}

	/*
	 *  Channel管理-チャンネル新規追加画面(step1)
	 */
	public function createStep1()
	{		

		$disp_data = [
			'redirect_url'	=> config('const.base_url').config('const.channel_create_step2_url'),
			'ver'			=> time(),
		];
		
		return view('admin.channel.create_step1', $disp_data);
	}

	/*
	 *  Channel管理-チャンネル新規追加処理
	 */
	public function createSend(Request $request)
	{		
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();
		
		$this->validate($request, [
			'name'					=> 'required|max:'.config('const.group_name_max_length'),
			'memo'					=> 'required|max:'.config('const.group_memo_max_length'),
			'line_basic_id'			=> 'required|unique:line_official_accounts,line_basic_id|max:'.config('const.group_memo_max_length'),
			'line_channel_id'		=> 'required|max:'.config('const.group_memo_max_length'),
			'line_channel_secret'	=> 'required|max:'.config('const.group_memo_max_length'),
			'line_token'			=> 'required|max:'.config('const.group_memo_max_length'),
		]);
		
		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['group_add'].",{$user['login_id']}");	

		$db_value = [
			'name'					=> $request->input('name'),
			'memo'					=> $request->input('memo'),
			'line_basic_id'			=> $request->input('line_basic_id'),
			'line_channel_id'		=> $request->input('line_channel_id'),
			'line_channel_secret'	=> $request->input('line_channel_secret'),
			'line_token'			=> $request->input('line_token'),
			'qrcode'				=> $request->input('qrcode'),
		];
		
		//groupテーブルにグループ名を追加
		$db_obj = new LineOfficialAccount($db_value);
		
		//DB保存
		$db_obj->save();

		//権限追加にチェックされていたら
		if( !empty($request->input('allow_add')) ){
			//権限追加
			$db_obj = new AdminChannelAllowList([
				'admin_id'		 => $user['id'],
				'line_basic_id'	 => $request->input('line_basic_id')
			]);

			$db_obj->save();
		}

		//ベーシックIDの画像保存用ディレクトリを生成
		system("mkdir ".config('const.storage_home_path')."/".config('const.storage_public_dir_path')."/".config('const.landing_dir_path')."/".$request->input('line_basic_id')." ".config('const.storage_home_path')."/".config('const.storage_public_dir_path')."/".config('const.landing_dir_path')."/".$request->input('line_basic_id')."/img;chown -R apache:apache ".config('const.storage_home_path')."/".config('const.storage_public_dir_path')."/".config('const.landing_dir_path')."/".$request->input('line_basic_id').";sudo chmod -R 775 ".config('const.storage_home_path')."/".config('const.storage_public_dir_path')."/".config('const.landing_dir_path')."/".$request->input('line_basic_id').";");

		$disp_data = [
			'ver' => time(),
		];
		
		return null;
	}

	/*
	 *  Channel管理-チャンネル新規追加画面(step2)
	 */
	public function createStep2($line_basic_id)
	{		
		$db_data = LineOfficialAccount::where('line_basic_id', $line_basic_id)->first();

		$disp_data = [
			'channel_id'	=> $db_data->line_channel_id,
			'line_basic_id'	=> $line_basic_id,
			'redirect_url'	=> config('const.base_url').config('const.channel_create_step3_url').'/'.$line_basic_id,
			'ver'			=> time(),
		];
		
		return view('admin.channel.create_step2', $disp_data);
	}

	/*
	 *  Channel管理-チャンネル新規追加画面(step3)
	 */
	public function createStep3($line_basic_id)
	{		
		$db_data = LineOfficialAccount::where('line_basic_id', $line_basic_id)->first();

		$disp_data = [
			'channel_id'	=> $db_data->line_channel_id,
			'line_basic_id'	=> '@'.$line_basic_id,
			'redirect_url'	=> config('const.base_url').config('const.channel_create_step4_url').'/'.$line_basic_id,
			'ver'			=> time(),
		];
		
		return view('admin.channel.create_step3', $disp_data);
	}

	/*
	 *  Channel管理-チャンネル新規追加画面(step2)
	 */
	public function createStep4($line_basic_id)
	{		
		$db_data = LineOfficialAccount::where('line_basic_id', $line_basic_id)->first();

		$disp_data = [
			'channel_id'	=> $db_data->line_channel_id,
			'line_basic_id'	=> $line_basic_id,
			'redirect_url'	=> config('const.base_url').config('const.channel_create_step4_url'),
			'ver'			=> time(),
		];
		
		return view('admin.channel.create_step4', $disp_data);
	}

	/*
	 *  LINEチャンネル編集処理
	 */
	public function store(Request $request, $line_basic_id)
	{

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();
		
		//更新ログ
		if( empty($request->input('del_flg')) ){
			//ログ出力
			$this->log_obj->addLog(config('const.admin_display_list')['group_update'].",{$user['login_id']}");	

		//削除ログ
		}else{
			//ログ出力
			$this->log_obj->addLog(config('const.admin_display_list')['group_delete'].",{$user['login_id']}");	
		}	

		//配列のエラーチェック
		$this->validate($request, [
			'name'					=> 'required',
			'memo'					=> 'required',
			'line_channel_secret'	=> 'required',
			'line_token'			=> 'required',
		]);

		//
		if( !empty($request->input('del_flg')) ){
			//テーブルからデータ削除
			LineOfficialAccount::where('line_basic_id', $line_basic_id)->delete();

		}else{
			//グループ管理画面の更新処理
			$update = LineOfficialAccount::where('line_basic_id', $line_basic_id)
				->update([
					'name'					=> $request->input('name'),
					'memo'					=> $request->input('memo'),
					'line_channel_secret'	=> $request->input('line_channel_secret'),
					'line_token'			=> $request->input('line_token'),
					'qrcode'				=> $request->input('qrcode'),
				]);
		}

		//ベーシックIDの画像保存用ディレクトリまでのフルパス
		$save_basic_dir = config('const.storage_home_path')."/".config('const.storage_public_dir_path')."/".config('const.landing_dir_path')."/".$line_basic_id;

		//ベーシックIDの画像保存用ディレクトリが生成されていないとき
		if( !file_exists($save_basic_dir) ){
			//ベーシックIDの画像保存用ディレクトリを生成
			system("mkdir {$save_basic_dir} {$save_basic_dir}/img;chown -R apache:apache {$save_basic_dir};sudo chmod -R 775 {$save_basic_dir};");
		}
						
		return null;
	}

	/*
	 * LINEチャンネルのリスト
	 */
	public function channelList($type = null)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		$db_data = LineOfficialAccount::query()
			->select('line_basic_id', 'name', 'memo')
			->whereIn('line_basic_id',function($query) use($user){
				$query->select('line_basic_id')->from('admin_channel_allow_lists')->where('admin_id', $user['id']);
			})
			->paginate(config('const.admin_key_list_limit'));

		$disp_data = [
			'type'				=> $type,
			'db_data'			=> $db_data,
			'total'				=> $db_data->total(),
			'ver' => time(),
		];
		
		return view('admin.channel.line_channel_list', $disp_data);
	}

	/*
	 * LINEチャンネルのリスト
	 */
	public function channelDetail($channel_id, $type)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		$db_data = LineOfficialAccount::query()
			->join('line_users', 'line_users.line_basic_id', '=', 'line_official_accounts.line_basic_id')
			->select('line_users.line_basic_id', 'line_official_accounts.name', 'line_official_accounts.line_token', 'line_official_accounts.memo', DB::raw('count(line_users.line_basic_id) as count'))
			->whereIn('line_official_accounts.line_basic_id',function($query) use($user){
				$query->select('line_basic_id')->from('admin_channel_allow_lists')->where('admin_id', $user['id']);
			})
			->where('line_users.follow_flg', 1)
			->where('line_users.disable', 0)
			->where('line_official_accounts.line_basic_id', $channel_id)
			->groupBy('line_users.line_basic_id')
			->first();

		//登録者0人なら
		if( empty($db_data) ){
			$db_data = LineOfficialAccount::query()
				->select('line_official_accounts.line_basic_id', 'line_official_accounts.name', 'line_official_accounts.memo')
				->whereIn('line_official_accounts.line_basic_id',function($query) use($user){
					$query->select('line_basic_id')->from('admin_channel_allow_lists')->where('admin_id', $user['id']);
				})
				->where('line_official_accounts.line_basic_id', $channel_id)
				->first();

			if( empty($db_data) ){
				return redirect(config('const.base_admin_url').'/member/line/channel/list')->with('message', __('messages.check_approved'));
			}

			$db_data->count = 0;
		}

		$service = new ChannelAnalysisService();

		//昨日のメッセージ送信数を取得
		$push_data = $service->getDailyNumberDelivery($db_data->line_token, date('Ymd', strtotime('-1 day')));

		//昨日の友だち数を取得
		$followers_data = $service->getDailyNumberFollowers($db_data->line_token, date('Ymd', strtotime('-1 day')));

		//友だちの属性情報に基づく統計情報を取得
//		$followers_info_data = $service->getFollowersInfo($db_data->line_token);

		$click_count = 0;
		$db_click_data = DB::select('select sum(click) as click from line_click_users where line_push_id = 0 group by line_push_id');
		if( !empty($db_click_data) ){
			$click_count = $db_click_data[0]->click;
		}

		$disp_data = [
			'click_count'		=> $click_count,
			'type'				=> $type,
			'push_data'			=> json_decode($push_data, true),
			'followers_data'	=> json_decode($followers_data, true),
			'channel_id'		=> $channel_id,
			'db_data'			=> $db_data,
			'ver' => time(),
		];
		
		return view('admin.channel.line_channel_detail', $disp_data);
	}

	/*
	 *  Channel管理-チャンネル編集画面
	 */
	public function edit($line_basic_id)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		$db_data = LineOfficialAccount::query()
			->whereIn('line_official_accounts.line_basic_id',function($query) use($user){
				$query->select('line_basic_id')->from('admin_channel_allow_lists')->where('admin_id', $user['id']);
			})
			->where('line_official_accounts.line_basic_id', $line_basic_id)
			->first();

		if( empty($db_data) ){
			return redirect(config('const.base_admin_url').'/member/line/channel/list')->with('message', __('messages.check_approved'));
		}

		$disp_data = [
			'db_data'	=> $db_data,
			'ver'		=> time(),
		];
		
		return view('admin.channel.edit', $disp_data);
	}
}
