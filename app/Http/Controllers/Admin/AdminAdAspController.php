<?php

namespace App\Http\Controllers\Admin;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libs\SysLog;
use App\Model\Admin;
use App\Model\Group;
use App\Model\User;
use App\Model\Ad_code;
use App\Model\Agency;
use App\Model\AdminChannelAllowList;
use App\Model\Line_asp;
use Auth;
use Carbon\Carbon;
use Session;
use Utility;
use DB;
use Storage;
use File;

class AdminAdAspController extends Controller
{
	private $log_obj;

	public function __construct()
	{
		//ログファイルのインスタンス生成
		//引数：ログの操作項目、ログファイルまでのフルパス
//		$this->log_obj	 = new SysLog(config('const.operation_export_log_name'), config('const.system_log_dir_path').config('const.operation_history_file_name'));
	}

	/*
	 * ASP一覧表示
	 */
	public function index(Request $request)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ASPデータ取得
		$db_data = Line_asp::paginate(config('const.admin_client_list_limit'));

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

		return view('admin.ad.asp.index', $disp_data);
	}

	/*
	 * ASP新規作成画面
	 */
	public function create()
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		$disp_data = [
			'ver'							=> time()
		];

		return view('admin.ad.asp.create', $disp_data); 
	}

	/*
	 * ASP新規作成処理
	 */
	public function createSend(Request $request)
	{
		$validate = [
			'asp'			=> 'bail|required',
			'url'			=> 'bail|required'
		];

		$this->validate($request, $validate);

		$now_date = Carbon::now();

		$regist_data = [
			'asp'			=> $request->input('asp'),
			'kickback_url'	=> $request->input('url'),
			'created_at'	=> $now_date,
			'updated_at'	=> $now_date
		];

		//ad_codesテーブルにインサート
		$ad_codes = new Line_asp($regist_data);

		//データをinsert
		$ad_codes->save();

		return null;
	}

	/*
	 * ASP編集画面表示
	 */
	public function edit($page, $id)
	{
		//動的クエリを生成するため
		$db_data = Line_asp::where('id',$id)->first();

		//編集データがない場合、データ一覧へリダイレクト
		if( empty($db_data) ){
			return redirect(config('const.base_admin_url').config('const.admin_asp_path'));
		}

		//画面表示用配列
		$disp_data = [
			'edit_id'						=> $id,
			'db_data'						=> $db_data,
			'ver'							=> time()
		];

		return view('admin.ad.asp.edit', $disp_data);
	}

	/*
	 * ASP編集画面の編集処理
	 */
	public function store(Request $request)
	{
		$validate = [
			'asp'			=> 'bail|required',
			'url'			=> 'bail|required'
		];

		$this->validate($request, $validate);

		//削除
		if( $request->input('del') == 1 ){
			Line_asp::where('id', $request->input('edit_id'))->delete();

		//更新
		}else{
			$update_value = [
				'asp'			=> $request->input('asp'),
				'kickback_url'	=> $request->input('url'),
				'updated_at'	=> Carbon::now()
			];

			$update = Line_asp::where('id', $request->input('edit_id'))->update($update_value);
		}

		return null;
	}

	/*
	 * ASP一覧画面からの削除処理
	 */
	public function bulkDeleteSend(Request $request)
	{
		//ID取得
		$listId = $request->input('id');

		//削除ID取得
		$listDelId = $request->input('del');

		foreach($listId as $index => $id){
			//配列のエラーチェック
			$this->validate($request, [
				'del.*'		=> 'required',
			]);

			//$listDelIdが配列かつ削除IDがあれば
			if( is_array($listDelId) && in_array($id, $listDelId) ){
				//テーブルからデータ削除
				Line_asp::where('id', $id)->delete();

			}
		}

		return null;
	}

}
