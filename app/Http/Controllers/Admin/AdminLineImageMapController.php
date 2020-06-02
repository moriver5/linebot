<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Line_2choices_log;
use App\Model\Line_2choices_template;
use App\Model\Line_4choices_log;
use App\Model\Line_4choices_template;
use App\Model\Line_postback_template;
use App\Model\Line_imagemap_log;
use Carbon\Carbon;
use Session;
use Utility;
use FormParts;
use DB;
use File;
use Image;

class AdminLineImageMapController extends Controller
{
    //
	public function __construct()
	{

	}

	public function index($channel_id, $id = "")
	{
		$disp_data			 = [];
		$db_data			 = [];
		$listArea			 = [];
		$list_msg			 = [];
		$img				 = '';
		$reserve_date		 = '';
		$send_status		 = '';
		$alttext			 = '';

		if( !empty($id) ){
			//シナリオデータ取得
			$db_data = Line_imagemap_log::where('id', $id)->orderBy('id')->first();
//error_log(print_r($db_data,true)."name\n",3,"/data/www/line/storage/logs/nishi_log.txt");

			if( !empty($db_data) ){
				$send_status		 = $db_data->send_status;
				$alttext			 = $db_data->alttext;
				$reserve_date		 = $db_data->reserve_send_date;
				$img				 = $db_data->img;

				if( !empty($db_data->area_json) ){
					$listTmpArea = json_decode($db_data->area_json);
					foreach($listTmpArea as $lines){
						$listArea[] = explode(",", $lines);
					}
//error_log(print_r($listArea,true)."name\n",3,"/data/www/line/storage/logs/nishi_log.txt");
				}
			}
		}

		//画面表示用配列
		$disp_data = [
			'send_status'		=> $send_status,
			'alttext'			=> $alttext,
			'reserve_date'		=> $reserve_date,
			'img'				=> $img,
			'channel_id'		=> $channel_id,
			'edit_id'			=> $id,
			'db_data'			=> $db_data,
			'list_msg'			=> $list_msg,
			'list_area'			=> $listArea,
			'channel_id'		=> $channel_id,
			'redirect_url'		=> config('const.base_admin_url').'/member/line/setting/imagemap/'.$channel_id,
			'save_redirect_url'	=> config('const.base_admin_url').'/member/line/setting/imagemap/'.$channel_id,
			'ver'				=> time()
		];

		return view('admin.push.imagemap', $disp_data);
	}

	/*
	 * 設定更新
	 */
	public function saveImageMapSetting(Request $request, $channel_id){
		$db_data = [];
		$db_logs_data = [];
//error_log(print_r($request->all(),true)."name\n",3,"/data/www/line/storage/logs/nishi_log.txt");

		$validate = [
			'reserve_date'	 => 'bail|required|date_format_check|surrogate_pair_check|emoji_check',
			'alttext'		 => 'bail|required|surrogate_pair_check',
		];

		if( !empty($request->input('text')) ){
			$validate['text'] = 'bail|required|surrogate_pair_check';
		}

		if( !empty($request->input('alttext')) ){
			$db_data['alttext'] = $request->input('alttext');
		}

		if( !empty($request->input('imagemap')) ){
			$listArea = explode("|", $request->input('imagemap'));
			$json = json_encode($listArea);
			$db_data['area_json'] = $json;
		}

		//予約配信
		if( $request->input('send_type') == 1 ){
			$db_data['reserve_send_date'] = $request->input('reserve_date');
			$db_data['sort_reserve_send_date'] = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $request->input('reserve_date')).'00';
		}

		//毎週配信
		if( $request->input('send_type') == 3 ){
			$db_data['send_week'] = $request->input('reserve_week');
			$validate['reserve_week'] = 'bail|required|surrogate_pair_check|emoji_check';
		}

		//毎日・毎週配信
		if( $request->input('send_type') == 2 || 
			$request->input('send_type') == 3 ){
			$db_data['send_regular_time'] = $request->input('regular_time');
			$validate['regular_time'] = 'bail|required|surrogate_pair_check|emoji_check';
		}

		//登録後配信
		if( $request->input('send_type') == 4 ){
			$db_data['send_after_day'] = $request->input('after_day');
			$db_data['send_regular_time'] = $request->input('regular_time');
			$validate['after_day'] = 'bail|required|surrogate_pair_check|emoji_check';
			$validate['regular_time'] = 'bail|required|surrogate_pair_check|emoji_check';
		}
		$this->validate($request, $validate);

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
//		$this->log_obj->addLog(config('const.admin_display_list')['melmaga_reserve_send'].",{$user['login_id']}");

		//現在時刻
		$now_date = Carbon::now();

		$line_basic_id	 = $request->input('channel_id');
		$line_push_id	 = $request->input('edit_id');
		$send_type		 = $request->input('send_type');

		if( empty($line_push_id) ){
			$db_data = array_merge($db_data, [
				'line_basic_id'			=> $line_basic_id,
				'send_type'				=> $send_type,	
				'send_status'			=> $request->input('send_status'),				//送信状況:0(配信待ち)
				'send_count'			=> 0,											//送信数
				'created_at'			=> $now_date,
				'updated_at'			=> $now_date
			]);

			//メルマガログに送信情報を登録
			$line_push_id = line_imagemap_log::insertGetId($db_data);

			$db_logs_data = array_merge($db_logs_data, [
				'created_at'			=> $now_date,
				'updated_at'			=> $now_date
			]);

			//メルマガログに送信情報を登録
			line_imagemap_log::insert($db_logs_data);
		}else{
			$db_data = array_merge($db_data, [
				'line_basic_id'			=> $line_basic_id,
				'send_type'				=> $send_type,								//予約配信:1
				'send_status'			=> $request->input('send_status'),			//送信状況:0(配信待ち)
				'send_count'			=> 0,										//送信数
				'created_at'			=> $now_date,
				'updated_at'			=> $now_date
			]);

			//メルマガログに送信情報を登録
			line_imagemap_log::where('id', $line_push_id)->update($db_data);

		}

		return $line_push_id;
	}

	/*
	 * 画像のアップロード処理
	 */
	public function uploadImageMapImgUpload(Request $request, $channel_id)
	{
		//アップロード画像情報取得
		$file = $request->file('import_file');

		$this->validate($request, [
			'import_file' => 'bail|required|check_upload_image_width'
		]);

		//landing_pagesテーブルに登録されているidを取得
		$id					 = $request->input('edit_id');
		$img				 = $request->input('msg');
		$send_type			 = $request->input('send_type');
		$reserve_date		 = $request->input('tmp_reserve_date');
		$alttext			 = $request->input('tmp_alttext');

		$now_date			 = Carbon::now();
//error_log("$channel_id, $file, $id, $img, $send_type, $now_date, $reserve_date, $alttext::name\n",3,"/data/www/line/storage/logs/nishi_log.txt");

		try{
			$sort_reserve_date = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $reserve_date.'00');
			$id = DB::transaction(function() use($channel_id, $file, $id, $img, $send_type, $reserve_date, $sort_reserve_date, $alttext, $now_date){
				//line_carousel_templatesテーブルにまだ登録されていないとき
				if( is_null($id) ){
					//最初の１回目の画像アップロードはinsert
					$id = Line_imagemap_log::insertGetId([
						'line_basic_id'			=> $channel_id,
						'send_type'				=> $send_type,		//配信タイプ:
						'send_status'			=> 99,				//配信状況:99(送信前の保存)
						'send_count'			=> 0,
						'alttext'				=> $alttext,
						'reserve_send_date'		=> $reserve_date,
						'sort_reserve_send_date'=> $sort_reserve_date,
						'created_at'			=> $now_date,
						'updated_at'			=> $now_date
					]);
				}
//error_log("$channel_id, $file, $id, $img, ".$file->getClientOriginalExtension()."::name\n",3,"/data/www/line/storage/logs/nishi_log.txt");

				//画像名をid名にするためupdateを行う
				Line_imagemap_log::where('id', $id)->update([
					'send_status'			=> 99,														//配信状況:99(送信前の保存)
					'alttext'				=> $alttext,
					'reserve_send_date'		=> $reserve_date,
					'sort_reserve_send_date'=> $sort_reserve_date,
					'img'					=> 'imagemap_'.$id.'.'.$file->getClientOriginalExtension(),
				]);
//				throw new \Exception("テスト例外エラー");
				return $id;
			});
		}catch(\Exception $e){
			return response()->json(['error' => [__("messages.dialog_update_failed")]],400);
		}

		$image_path = config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$channel_id.'/img';
		$image_name = 'imagemap_'.$id.'.'.$file->getClientOriginalExtension();

		//画像の保存先を移動
		$file->move($image_path, $image_name);

		$resize_path = $image_path.'/imagemap_'.$id.'_1040.'.$file->getClientOriginalExtension();
		$original_image = Image::make($image_path.'/'.$image_name);
		$original_image->resize(1040, null, function ($constraint) {
			$constraint->aspectRatio();
		});
		$original_image->save($resize_path);

		$resize_path = $image_path.'/imagemap_'.$id.'_700.'.$file->getClientOriginalExtension();
		$original_image = Image::make($image_path.'/'.$image_name);
		$original_image->resize(700, null, function ($constraint) {
			$constraint->aspectRatio();
		});
		$original_image->save($resize_path);

		$resize_path = $image_path.'/imagemap_'.$id.'_460.'.$file->getClientOriginalExtension();
		$original_image = Image::make($image_path.'/'.$image_name);
		$original_image->resize(460, null, function ($constraint) {
			$constraint->aspectRatio();
		});
		$original_image->save($resize_path);

		$resize_path = $image_path.'/imagemap_'.$id.'_300.'.$file->getClientOriginalExtension();
		$original_image = Image::make($image_path.'/'.$image_name);
		$original_image->resize(300, null, function ($constraint) {
			$constraint->aspectRatio();
		});
		$original_image->save($resize_path);

		$resize_path = $image_path.'/imagemap_'.$id.'_240.'.$file->getClientOriginalExtension();
		$original_image = Image::make($image_path.'/'.$image_name);
		$original_image->resize(240, null, function ($constraint) {
			$constraint->aspectRatio();
		});
		$original_image->save($resize_path);

		//画像を管理画面上のプレビュー用にコピー
		system('ln -s '.config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$channel_id.'/img/'.'imagemap_'.$id.'.'.$file->getClientOriginalExtension().' /data/www/line/public/images/preview/');

		//更新データを取得
		$db_data = Line_imagemap_log::where('id',$id)->first();

		if( $reserve_date != "" ){
			$sort_reserve_date = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $reserve_date).'00';

		}else{
			//現在時刻をyyyymmddhhmmにフォーマット
			$sort_reserve_date = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00';
		}

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
//		$this->log_obj->addLog(config('const.admin_display_list')['lp_img_upload'].",{$user['login_id']}");

		//失敗
		if( is_null($id) ){
			return false;

		//画像アップロード成功
		}else{
			return $id;
		}

	}

	/*
	 * 画像のアップロード処理
	 */
	public function deleteImageMapImgUpload(Request $request, $channel_id)
	{
		//landing_pagesテーブルに登録されているidを取得
		$id = $request->input('edit_id');
//error_log("$channel_id, $id::name\n",3,"/data/www/line/storage/logs/nishi_log.txt");

		//更新データを取得
		$db_data = Line_imagemap_log::where('id',$id)->first();

		try{
			DB::transaction(function() use($id){
				//画像名をid名にするためupdateを行う
				Line_imagemap_log::where('id', $id)->update([
					'img' => null,
				]);
			});
		}catch(\Exception $e){
			return response()->json(['error' => [__("messages.dialog_update_failed")]],400);
		}

		//プレビュー画像のシンボリックリンクを削除
		system('unlink /data/www/line/public/images/preview/'.$db_data->img);

		list($image_id, $extension) = explode(".", $db_data->img);

		$image_path = config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$channel_id.'/img/';
		$image_name = 'imagemap_'.$id.'.'.$extension;

		//画像削除
		system('rm -rf /data/www/line/public/images/preview/'.$image_path.$db_data->img);
		system('rm -rf '.$image_path.$db_data->img);

		system('rm -rf '.$image_path.'imagemap_'.$id.'_240.'.$extension);
		system('rm -rf '.$image_path.'imagemap_'.$id.'_300.'.$extension);
		system('rm -rf '.$image_path.'imagemap_'.$id.'_460.'.$extension);
		system('rm -rf '.$image_path.'imagemap_'.$id.'_700.'.$extension);
		system('rm -rf '.$image_path.'imagemap_'.$id.'_1040.'.$extension);

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
//		$this->log_obj->addLog(config('const.admin_display_list')['lp_img_upload'].",{$user['login_id']}");

		return config('const.base_admin_url').'/member/line/setting/imagemap/'.$channel_id."/".$id;
	}

}
