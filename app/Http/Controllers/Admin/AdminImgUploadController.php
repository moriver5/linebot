<?php

namespace App\Http\Controllers\Admin;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libs\SysLog;
use App\Model\Admin;
use App\Model\Upload_image;
use Carbon\Carbon;
use Session;
use Utility;
use DB;
use File;

class AdminImgUploadController extends Controller
{
	private $log_obj;

	public function __construct()
	{
		//ログファイルのインスタンス生成
		//引数：ログの操作項目、ログファイルまでのフルパス
		$this->log_obj	 = new SysLog(config('const.operation_export_log_name'), config('const.system_log_dir_path').config('const.operation_history_file_name'));
	}

	/*
	 * マスタ-出力用画像設定画面表示
	 */
	public function index($sort_type = null)
	{
		if( empty($sort_type) ){
			$sort_type = 'created_at';
		}

		//動的クエリを生成するため
		$db_data = Upload_image::orderBy($sort_type, 'desc')->paginate(config('const.admin_client_list_limit'));

		$list_img = [];
		if( count($db_data) > 0 ){
			foreach($db_data as $lines){
				$list_img[] = [
					'img'		=> $lines->id.'.'.$lines->ext,
					'size'		=> Utility::calcFileSize($lines->size),
					'date'		=> preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1/$3/$5", $lines->created_at)
				];
			}
		}

		//画面表示用配列
		$disp_data = [
			'img_path'			=> '/'.config('const.upload_img_path'),
			'list_img'			=> $list_img,
			'total'				=> $db_data->total(),
			'currentPage'		=> $db_data->currentPage(),
			'lastPage'			=> $db_data->lastPage(),
			'links'				=> $db_data->links(),
			'ver'				=> time()
		];

		return view('admin.master.img_upload.index', $disp_data);
	}

	/*
	 * 画像のアップロード処理
	 */
	public function uploadImg(Request $request)
	{
		//アップロード画像情報取得
		$file = $request->file('import_file');

		//画像名をupload_imagesテーブルにinsert
		DB::transaction(function() use($file){
			$img_name = $file->getClientOriginalName();
			$extension = $file->getClientOriginalExtension();
			
			$now_date = Carbon::now();
			$id = Upload_image::insertGetId([
				'ext'				=> $extension,
				'size'				=> $file->getClientSize(),
				'created_at'		=> $now_date,
				'updated_at'		=> $now_date
			]);
 
			//画像の保存先を移動
			//保存先：/data/www/melmaga/public/upload_images
			$file->move(config('const.project_home_path').'/'.config('const.public_dir_path').'/'.config('const.upload_img_path').'/', $img_name);

			//アップロード時の画像名を変更
			rename(config('const.project_home_path').'/'.config('const.public_dir_path').'/'.config('const.upload_img_path').'/'.$img_name, config('const.project_home_path').'/'.config('const.public_dir_path').'/'.config('const.upload_img_path').'/'.$id.'.'.$extension);
		});

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['img_upload'].",{$user['login_id']}");

		return null;
	}

	/*
	 * 画像の削除処理
	 */
	public function deleteImg(Request $request)
	{
		$this->validate($request, [
			'del_img'	=> 'required',
		]);

		//削除する画像を取得
		$listDelImg = $request->input('del_img');

		//選択した画像を削除
		foreach($listDelImg as $img){
			//ディレクトリから画像削除
			system("rm ".config('const.project_home_path').'/'.config('const.public_dir_path').'/'.config('const.upload_img_path').'/'.$img);

			//upload_imageテーブルから画像削除
			$delete = Upload_image::where('id', preg_replace("/(.+?)\./", "$1", $img))->delete();
		}

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['img_upload_delete'].",{$user['login_id']}");

		return null;
	}
}
