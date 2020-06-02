<?php

namespace App\Http\Controllers\Admin;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libs\SysLog;
use App\Model\Admin;
//use App\Model\Group;
use App\Model\User;
use App\Model\Agency;
use App\Model\Ad_code;
use Auth;
use Carbon\Carbon;
use Session;
use Utility;
use DB;
use Storage;
use File;

class AdminAdAgencyController extends Controller
{
	private $log_obj;

	public function __construct()
	{
		//ログファイルのインスタンス生成
		//引数：ログの操作項目、ログファイルまでのフルパス
		$this->log_obj	 = new SysLog(config('const.operation_export_log_name'), config('const.system_log_dir_path').config('const.operation_history_file_name'));
	}

	/*
	 * 代理店一覧画面表示
	 */
	public function index(Request $request)
	{
		//ログイン管理者情報取得
		$user = Utility::getAgencyDefaultDispParam('agency');

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['agency_top'].",{$user['login_id']}");

		$db_data = Agency::query()->paginate(config('const.admin_client_list_limit'));

		//件数取得
		$total = $db_data->total();

		//画面表示用配列
		$disp_data = [
			'db_data'			=> $db_data,
			'total'				=> $total,
			'currentPage'		=> $db_data->currentPage(),
			'lastPage'			=> $db_data->lastPage(),
			'links'				=> $db_data->links(),
			'ver'				=> time()
		];

		return view('admin.ad.agency.index', $disp_data);
	}

	/*
	 * 代理店新規作成画面
	 */
	public function create()
	{
		//
		$disp_data = [
			'ver'							=> time()
		];

		return view('admin.ad.agency.create', $disp_data); 
	}

	/*
	 * 代理店新規作成処理
	 */
	public function createSend(Request $request)
	{
		$this->validate($request, [
			'agency'		=> 'bail|required|surrogate_pair_check|emoji_check',
			'login_id'		=> 'bail|required|surrogate_pair_check|emoji_check|use_char_check|max:'.config('const.agency_login_id_max_length').'|unique:agencies,login_id',
			'password'		=> 'bail|required|surrogate_pair_check|emoji_check|use_char_check|max:'.config('const.password_max_length').'|min:'.config('const.password_length'),
			'description'	=> 'bail|surrogate_pair_check|emoji_check',
		]);

		$now_date = Carbon::now();

		$regist_data = [
			'name'				=> $request->input('agency'),
			'login_id'			=> $request->input('login_id'),
			'password'			=> bcrypt($request->input('password')),
			'password_raw'		=> $request->input('password'),
			'remember_token'	=> session_create_id(),
			'memo'				=> $request->input('description'),
			'created_at'		=> $now_date,
			'updated_at'		=> $now_date
		];

		//agencyテーブルにインサート
		$agency = new Agency($regist_data);

		//データをinsert
		$agency->save();

		//ログイン管理者情報取得
		$user = Utility::getAgencyDefaultDispParam('agency');

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['agency_create'].",{$user['login_id']}");

		return null;
	}

	/*
	 * 代理店編集画面表示
	 */
	public function edit($page, $id)
	{
		//動的クエリを生成するため
		$db_data = Agency::where('id',$id)->first();

		//編集データがない場合、データ一覧へリダイレクト
		if( empty($db_data) ){
			return redirect(config('const.base_admin_url').config('const.admin_agency_path'));
		}

		//画面表示用配列
		$disp_data = [
			'edit_id'						=> $id,
			'db_data'						=> $db_data,
			'ver'							=> time()
		];

		return view('admin.ad.agency.edit', $disp_data);
	}

	/*
	 * 代理店編集画面の編集処理
	 */
	public function store(Request $request)
	{
		$edit_id = $request->input('edit_id');

		$this->validate($request, [
			'agency'		=> 'bail|required|surrogate_pair_check|emoji_check',
			'login_id'		=> 'bail|required|surrogate_pair_check|emoji_check|use_char_check|max:'.config('const.agency_login_id_max_length').'|unique:agencies,login_id,'.$edit_id.',id',
			'password'		=> 'bail|required|surrogate_pair_check|emoji_check|use_char_check|max:'.config('const.password_max_length').'|min:'.config('const.password_length'),
			'description'	=> 'bail|surrogate_pair_check|emoji_check',
		]);

		//削除
		if( $request->input('del') == 1 ){
			Agency::where('id', $edit_id)->delete();

			//代理店を削除したら広告コードの代理店を自社(agency_id=0)として更新する
			Ad_code::where('agency_id', $edit_id)->update([
				'agency_id'	=> 0
			]);

		//更新
		}else{
			$update_value = [
				'name'			=> $request->input('agency'),
				'login_id'		=> $request->input('login_id'),
				'password'		=> bcrypt($request->input('password')),
				'password_raw'	=> $request->input('password'),
				'memo'			=> $request->input('description'),
				'updated_at'	=> Carbon::now()
			];

			$update = Agency::where('id', $edit_id)->update($update_value);
		}

		//ログイン管理者情報取得
		$user = Utility::getAgencyDefaultDispParam('agency');

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['agency_edit'].",{$user['login_id']}");

		return null;
	}

	/*
	 * 代理店一覧画面での削除処理
	 */
	public function bulkDeleteSend(Request $request)
	{
		//ログイン管理者情報取得
		$user = Utility::getAgencyDefaultDispParam('agency');

		//ID取得
		$listId = $request->input('id');

		//削除ID取得
		$listDelId = $request->input('del');

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['agency_del'].",{$user['login_id']}");	

		foreach($listId as $index => $id){
			//配列のエラーチェック
			$this->validate($request, [
				'del.*'		=> 'required',
			]);

			//$listDelIdが配列かつ削除IDがあれば
			if( is_array($listDelId) && in_array($id, $listDelId) ){
				//テーブルからデータ削除
				Agency::where('id', $id)->delete();

				//代理店を削除したら広告コードの代理店を自社(agency_id=0)として更新する
				Ad_code::where('agency_id', $id)->update([
					'agency_id'	=> 0
				]);
			}
		}

		return null;
	}

}
