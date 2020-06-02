<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libs\SysLog;
use App\Model\LineOfficialAccount;
use App\Model\LineContentType;
use App\Model\LineContents;
use DB;
use File;
use Utility;
use Carbon\Carbon;
use Image;

class AdminMasterLineContentController extends Controller
{
	private $log_obj;

	public function __construct()
	{
		//ログファイルのインスタンス生成
		//引数：ログの操作項目、ログファイルまでのフルパス
		$this->log_obj = new SysLog(config('const.operation_export_log_name'), config('const.system_log_dir_path').config('const.operation_history_file_name'));
	}

	/*
	 *  自動メール文設定画面表示
	 */
	public function index($channel_id, $id = '')
	{
		$list_msg = [];

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		$db_data = LineContents::select('line_contents.id', 'line_contents.line_basic_id', 'line_contents.type', 'line_contents.msg1', 'line_contents.msg2', 'line_contents.msg3', 'line_contents.msg4', 'line_contents.msg5', 'line_content_types.name', 'line_contents.image')->join('line_content_types', 'line_contents.type', '=', 'line_content_types.id')->where('line_basic_id', $channel_id)->get();
		if( !empty($db_data) ){
			foreach($db_data as $lines){
//				if( !empty($lines->msg1) ){
					if( preg_match("/msg\d+_\d+_\d+\.png/", $lines->msg1) > 0 ){
						$full_path_image = config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$channel_id.'/img/'.$lines->msg1;
						if( file_exists($full_path_image) ){
							//画像を管理画面上のプレビュー用にコピー
							File::copy($full_path_image, '/data/www/line/public/images/preview/'.$lines->msg1);
						}
					}
					$list_msg[$lines->id.'_'.$lines->type]['msg1'] = $lines->msg1;
//				}
				if( !empty($lines->msg2) ){
					if( preg_match("/msg\d+_\d+_\d+\.png/", $lines->msg2) > 0 ){
						$full_path_image = config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$channel_id.'/img/'.$lines->msg2;
						if( file_exists($full_path_image) ){
							//画像を管理画面上のプレビュー用にコピー
							File::copy($full_path_image, '/data/www/line/public/images/preview/'.$lines->msg2);
						}
					}
					$list_msg[$lines->id.'_'.$lines->type]['msg2'] = $lines->msg2;
				}
				if( !empty($lines->msg3) ){
					if( preg_match("/msg\d+_\d+_\d+\.png/", $lines->msg3) > 0 ){
						$full_path_image = config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$channel_id.'/img/'.$lines->msg3;
						if( file_exists($full_path_image) ){
							//画像を管理画面上のプレビュー用にコピー
							File::copy($full_path_image, '/data/www/line/public/images/preview/'.$lines->msg3);
						}
					}
					$list_msg[$lines->id.'_'.$lines->type]['msg3'] = $lines->msg3;
				}
				if( !empty($lines->msg4) ){
					if( preg_match("/msg\d+_\d+_\d+\.png/", $lines->msg4) > 0 ){
						$full_path_image = config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$channel_id.'/img/'.$lines->msg4;
						if( file_exists($full_path_image) ){
							//画像を管理画面上のプレビュー用にコピー
							File::copy($full_path_image, '/data/www/line/public/images/preview/'.$lines->msg4);
						}
					}
					$list_msg[$lines->id.'_'.$lines->type]['msg4'] = $lines->msg4;
				}
				if( !empty($lines->msg5) ){
					if( preg_match("/msg\d+_\d+_\d+\.png/", $lines->msg5) > 0 ){
						$full_path_image = config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$channel_id.'/img/'.$lines->msg5;
						if( file_exists($full_path_image) ){
							//画像を管理画面上のプレビュー用にコピー
							File::copy($full_path_image, '/data/www/line/public/images/preview/'.$lines->msg5);
						}
					}
					$list_msg[$lines->id.'_'.$lines->type]['msg5'] = $lines->msg5;
				}
			}
		}
//error_log(print_r($list_msg,true)."\n",3,"/data/www/line/storage/logs/nishi_log.txt");

		$channel_data = LineOfficialAccount::where('line_basic_id', $channel_id)->get();

		//メール文カテゴリリスト取得
		$line_type_data = LineContentType::get();

		$disp_data = [
			'redirect_url'	=> config('const.base_admin_url').'/member/line/setting/replay/'.$channel_id,
			'edit_id'		=> $id,
			'channel_id'	=> $channel_id,
			'channel_name'	=> $channel_data[0]->name,
			'list_mail_type'=> $line_type_data,
			'db_data'		=> $db_data,
			'list_msg'		=> $list_msg,
			'ver'			=> time(),
		];

		//設定情報がないとき
		if( count($db_data) > 0 ){
			return view('admin.master.line_contents', $disp_data);
		}else{
			return view('admin.master.line_none_contents', $disp_data);
		}
	}

	/*
	 *  自動メール文設定-更新処理
	 */
	public function store(Request $request)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['auto_mail_update'].",{$user['login_id']}");

		$tab_id = preg_replace("/(\d+)_\d+/", "$1", $request->input('tab'));
		$channel_id = $request->input('channel_id');

		//削除
		if( $request->input('del_flg') == 1 ){
			$update = LineContents::where('id', $tab_id)->where('line_basic_id', $channel_id)->delete();
			return null;
		}

		$db_data = [
			'msg1'			=> $request->input('msg1'),
			'msg2'			=> $request->input('msg2'),
			'msg3'			=> $request->input('msg3'),
			'msg4'			=> $request->input('msg4'),
			'msg5'			=> $request->input('msg5'),
		];

		//新規追加
		if( empty($tab_id) ){
			//エラーチェック
			$this->validate($request, [
				'channel_id'	=> 'required',
				'msg1'			=> 'bail|required|surrogate_pair_check|emoji_check'
			]);

			$db_data = array_merge($db_data,[
				'line_basic_id'	=> $channel_id,
				'type'			=> $request->input('setting_name')
			]);

			//groupテーブルにグループ名を追加
			$db_obj = new LineContents($db_data);

			//DB保存
			$db_obj->save();

		//更新
		}else{
//error_log(print_r($request->all(),true)." {$tab_id}\n",3,"/data/www/line/storage/logs/nishi_log.txt");			
			//エラーチェック
			$this->validate($request, [
				'channel_id'	=> 'required',
				'msg1'			=> 'bail|required|surrogate_pair_check|emoji_check',
			]);
//error_log(print_r($request->all(),true)." {$tab_id}\n",3,"/data/www/line/storage/logs/nishi_log.txt");			

			if( !empty($request->input('urlmsg1')) ){
				$db_data['msg1'] = $request->input('urlmsg1');
			}
			if( !empty($request->input('urlmsg2')) ){
				$db_data['msg2'] = $request->input('urlmsg2');
			}
			if( !empty($request->input('urlmsg3')) ){
				$db_data['msg3'] = $request->input('urlmsg3');
			}
			if( !empty($request->input('urlmsg4')) ){
				$db_data['msg4'] = $request->input('urlmsg4');
			}
			if( !empty($request->input('urlmsg5')) ){
				$db_data['msg5'] = $request->input('urlmsg5');
			}

			//自動メール文更新処理
			$update = LineContents::where('id', $tab_id)->where('line_basic_id', $channel_id)
				->update($db_data);
		}
		
		return null;
	}

	/*
	 *  自動メール文設定画面追加表示
	 */
	public function addSetting($line_basic_id, $id = '')
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//メール文カテゴリリスト取得
		$line_type_data = LineContentType::get();

		//グループリスト取得
		$channel_data = LineOfficialAccount::where('line_basic_id', $line_basic_id)->get();

		$disp_data = [
			'redirect_url'	=> config('const.base_admin_url').'/member/line/setting/replay/'.$line_basic_id,
			'id'			=> $id,
			'channel_id'	=> $line_basic_id,
			'channel_name'	=> $channel_data[0]->name,
			'edit_id'		=> $channel_data[0]->id,
			'list_mail_type'=> $line_type_data,
			'ver'			=> time(),
		];

		return view('admin.master.line_none_contents', $disp_data);
	}

	/*
	 *  自動メール文設定-更新処理
	 */
	public function saveFollowMsg(Request $request, $channel_id)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['auto_mail_update'].",{$user['login_id']}");

		$send_type	 = preg_replace("/\d+_(\d+)/", "$1", $request->input('send_type'));
		$msg		 = $request->input('msg');
		$edit_id	 = preg_replace("/(\d+)_\d+/", "$1", $request->input('edit_id'));
		$msg_value	 = $request->input($msg.'_'.$edit_id.'_'.$send_type);
		if( !isset($msg_value) ){
			$msg_value = $request->input($msg);
		}
//error_log(print_r($request->all(),true)."\n",3,"/data/www/line/storage/logs/nishi_log.txt");
//error_log($request->input($msg.'_'.$edit_id.'_'.$send_type)."<>".$msg.'_'.$edit_id.'_'.$send_type." {$send_type} {$msg} {$edit_id}::eee\n",3,"/data/www/line/storage/logs/nishi_log.txt");
		//新規追加
		if( empty($edit_id) ){
			//エラーチェック
			$this->validate($request, [
//				$msg			=> 'bail|required|surrogate_pair_check|emoji_check'
			]);

			//groupテーブルにグループ名を追加
			$db_obj = new LineContents([
				'line_basic_id'	=> $channel_id,
				'type'			=> $send_type,
				$msg			=> $msg_value
			]);

			//DB保存
			$db_obj->save();

		//更新
		}else{
			//エラーチェック
			$this->validate($request, [
//				$msg			=> 'bail|required|surrogate_pair_check|emoji_check',
			]);

			//自動メール文更新処理
			$update = LineContents::where('id', $edit_id)->where('type', $send_type)
				->update([
					$msg	=> $msg_value
				]);
		}
		
		return null;
	}

	/*
	 * 画像のアップロード処理
	 */
	public function uploadFollowImgUpload(Request $request, $channel_id)
	{
		//アップロード画像情報取得
		$file = $request->file('import_file');

		//landing_pagesテーブルに登録されているidを取得
		$id			 = preg_replace("/(\d+)_\d+/", "$1", $request->input('edit_id'));
		$msg		 = $request->input('msg');
		$send_type	 = preg_replace("/\d+_(\d+)/", "$1", $request->input('send_type'));
//error_log("{$id} {$send_type}　{$msg}::dddd\n",3,"/data/www/line/storage/logs/nishi_log.txt");

		$img_name = $file->getClientOriginalName();

		try{
			$id = DB::transaction(function() use($channel_id, $file, $id, $msg, $send_type){
				if( empty($id) ){
					$now_date = Carbon::now();

					//最初の１回目の画像アップロードはinsert
					$id = LineContents::insertGetId([
						'line_basic_id'	=> $channel_id,
						'type'			=> $send_type,
						$msg			=> $msg,
						'created_at'	=> $now_date,
						'updated_at'	=> $now_date
					]);
				}

				//画像名をid名にするためupdateを行う
				LineContents::where('id', $id)->where('type', $send_type)->update([
					$msg			=> $msg.'_'.$id.'_'.$send_type.'.'.$file->getClientOriginalExtension(),
				]);
//				throw new \Exception("テスト例外エラー");
				return $id;
			});
		}catch(\Exception $e){
			return response()->json(['error' => [__("messages.dialog_update_failed")]],400);
		}

		//画像の保存先を移動
		$file->move('/data/www/line/public/images/preview/', $msg.'_'.$id.'_'.$send_type.'.'.$file->getClientOriginalExtension());

		//画像を管理画面上のプレビュー用にコピー
		File::copy('/data/www/line/public/images/preview/'.$msg.'_'.$id.'_'.$send_type.'.'.$file->getClientOriginalExtension(), 
			config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$channel_id.'/img/'.$msg.'_'.$id.'_'.$send_type.'.'.$file->getClientOriginalExtension());

		//更新データを取得
		$db_data = LineContents::where('id',$id)->first();

		//メッセージをすべて空で更新
		LineContents::where('id', $id)->update([
			'msg1'					=> "",
			'msg2'					=> "",
			'msg3'					=> "",
			'msg4'					=> "",
			'msg5'					=> "",
		]);

		//ここからmsg1～msg5の間に空データがあれば詰める処理
		$list_msg = [];
		if( !empty($db_data) ){
			if( !empty($db_data->msg1) ){
				$list_msg[] = $db_data->msg1;
			}
			if( !empty($db_data->msg2) ){
				$list_msg[] = $db_data->msg2;
			}
			if( !empty($db_data->msg3) ){
				$list_msg[] = $db_data->msg3;
			}
			if( !empty($db_data->msg4) ){
				$list_msg[] = $db_data->msg4;
			}
			if( !empty($db_data->msg5) ){
				$list_msg[] = $db_data->msg5;
			}

			$list_update_val = [];
			foreach($list_msg as $index => $msg){
				//画像データなら
				if( preg_match("/msg\d+_\d+_\d+(\.png|\.jpeg|\.jpg|\.gif)$/", $msg, $extension) > 0 ){
					$new_msg = 'msg'.($index+1).'_'.$id.'_'.$send_type.$extension[1];

					//プレビュー画像も同様に移動する
					File::move('/data/www/line/public/images/preview/'.$msg, '/data/www/line/public/images/preview/'.$new_msg);

					$msg = $new_msg;
				}
				$list_update_val['msg'.($index+1)] = $msg;
			}

			//詰めたmsg1～msg5のデータを更新
			LineContents::where('id', $id)->update($list_update_val);
		}

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['lp_img_upload'].",{$user['login_id']}");

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
	public function uploadFollowImgMapUpload(Request $request, $channel_id)
	{
		//アップロード画像情報取得
		$file = $request->file('import_file');
//error_log(print_r($request->all(),true)."\n",3,"/data/www/line/storage/logs/nishi_log.txt");

		//landing_pagesテーブルに登録されているidを取得
		$id			 = preg_replace("/(\d+)_\d+/", "$1", $request->input('edit_id'));
		$msg		 = $request->input('msg');
		$send_type	 = preg_replace("/\d+_(\d+)/", "$1", $request->input('send_type'));
		$title		 = $request->input('tmp_title'.$msg);
		$url		 = $request->input('tmp_url'.$msg);		

		$this->validate($request, [
			'import_file'		 => 'bail|required|check_upload_image_width',
//			'tmp_title'.$msg	 => 'required',
//			'tmp_url'.$msg		 => 'required',
		]);

		$img_name = $file->getClientOriginalName();
//error_log("{$title} {$url} {$id} {$send_type}　{$msg} {$img_name}::dddd\n",3,"/data/www/line/storage/logs/nishi_log.txt");

		try{
			$id = DB::transaction(function() use($channel_id, $file, $id, $msg, $send_type, $title, $url){
				if( empty($id) ){
					$now_date = Carbon::now();

					//最初の１回目の画像アップロードはinsert
					$id = LineContents::insertGetId([
						'line_basic_id'	=> $channel_id,
						'type'			=> $send_type,
						$msg			=> $msg,
						'created_at'	=> $now_date,
						'updated_at'	=> $now_date
					]);
				}

				//画像名をid名にするためupdateを行う
				LineContents::where('id', $id)->where('type', $send_type)->update([
					$msg			=> 'imglink'.$msg.'_'.$id.'_'.$send_type.'.'.$file->getClientOriginalExtension().'|'.$url.'|'.$title,
				]);
//				throw new \Exception("テスト例外エラー");
				return $id;
			});
		}catch(\Exception $e){
			return response()->json(['error' => [__("messages.dialog_update_failed")]],400);
		}

		//画像の保存先を移動
		$file->move('/data/www/line/public/images/preview/', 'imglink'.$msg.'_'.$id.'_'.$send_type.'.'.$file->getClientOriginalExtension());

		$image_path = config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$channel_id.'/img';
		$image_name = 'imglink'.$msg.'_'.$id.'_'.$send_type.'.'.$file->getClientOriginalExtension();

		//画像を管理画面上のプレビュー用にコピー
		File::copy('/data/www/line/public/images/preview/'.'imglink'.$msg.'_'.$id.'_'.$send_type.'.'.$file->getClientOriginalExtension(), 
			$image_path.'/'.$image_name);

		$resize_path = $image_path.'/imglink'.$msg.'_'.$id.'_'.$send_type.'_1040.'.$file->getClientOriginalExtension();
		$original_image = Image::make($image_path.'/'.$image_name);
		$original_image->resize(1040, null, function ($constraint) {
			$constraint->aspectRatio();
		});
		$original_image->save($resize_path);

		$resize_path = $image_path.'/imglink'.$msg.'_'.$id.'_'.$send_type.'_700.'.$file->getClientOriginalExtension();
		$original_image = Image::make($image_path.'/'.$image_name);
		$original_image->resize(700, null, function ($constraint) {
			$constraint->aspectRatio();
		});
		$original_image->save($resize_path);

		$resize_path = $image_path.'/imglink'.$msg.'_'.$id.'_'.$send_type.'_460.'.$file->getClientOriginalExtension();
		$original_image = Image::make($image_path.'/'.$image_name);
		$original_image->resize(460, null, function ($constraint) {
			$constraint->aspectRatio();
		});
		$original_image->save($resize_path);

		$resize_path = $image_path.'/imglink'.$msg.'_'.$id.'_'.$send_type.'_300.'.$file->getClientOriginalExtension();
		$original_image = Image::make($image_path.'/'.$image_name);
		$original_image->resize(300, null, function ($constraint) {
			$constraint->aspectRatio();
		});
		$original_image->save($resize_path);

		$resize_path = $image_path.'/imglink'.$msg.'_'.$id.'_'.$send_type.'_240.'.$file->getClientOriginalExtension();
		$original_image = Image::make($image_path.'/'.$image_name);
		$original_image->resize(240, null, function ($constraint) {
			$constraint->aspectRatio();
		});
		$original_image->save($resize_path);

		//更新データを取得
		$db_data = LineContents::where('id',$id)->first();

		//メッセージをすべて空で更新
		LineContents::where('id', $id)->update([
			'msg1'					=> "",
			'msg2'					=> "",
			'msg3'					=> "",
			'msg4'					=> "",
			'msg5'					=> "",
		]);

		//ここからmsg1～msg5の間に空データがあれば詰める処理
		$list_msg = [];
		if( !empty($db_data) ){
			if( !empty($db_data->msg1) ){
				$list_msg[] = $db_data->msg1;
			}
			if( !empty($db_data->msg2) ){
				$list_msg[] = $db_data->msg2;
			}
			if( !empty($db_data->msg3) ){
				$list_msg[] = $db_data->msg3;
			}
			if( !empty($db_data->msg4) ){
				$list_msg[] = $db_data->msg4;
			}
			if( !empty($db_data->msg5) ){
				$list_msg[] = $db_data->msg5;
			}

			$list_update_val = [];
			foreach($list_msg as $index => $msg){

				//画像データなら
				if( preg_match("/^imglinkmsg\d+_\d+_\d+(\.png|\.jpeg|\.jpg|\.gif)/", $msg, $extension) > 0 ){
					list($msg, $url, $title) = explode("|", $msg);
					$new_msg = 'imglinkmsg'.($index+1).'_'.$id.'_'.$send_type.$extension[1];

					//プレビュー画像も同様に移動する
					File::move('/data/www/line/public/images/preview/'.$msg, '/data/www/line/public/images/preview/'.$new_msg);

					$msg = $new_msg.'|'.$url.'|'.$title;
				}
				$list_update_val['msg'.($index+1)] = $msg;
			}

			//詰めたmsg1～msg5のデータを更新
			LineContents::where('id', $id)->update($list_update_val);
		}

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['lp_img_upload'].",{$user['login_id']}");

		//失敗
		if( is_null($id) ){
			return false;

		//画像アップロード成功
		}else{
			return $id;
		}
	}

	/*
	 * 画像の削除処理
	 */
	public function deleteFollowImg(Request $request, $channel_id, $id, $type)
	{
		$msg	= $request->input('msg');
//error_log("{$channel_id} {$id} {$type} {$msg}::ffff\n",3,"/data/www/line/storage/logs/nishi_log.txt");

		//DBの画像を削除
		LineContents::where('line_basic_id', $channel_id)->where('id', $id)->where('type', $type)->update([$msg => '']);

		//画像ファイル削除
		File::delete('/data/www/line/public/images/preview/'.$id.'.png');

		//更新後のデータ取得
		$db_data = 	LineContents::where('line_basic_id', $channel_id)->where('id', $id)->where('type', $type)->first();

		LineContents::where('line_basic_id', $channel_id)
			->where('id', $id)
			->where('type', $type)
			->update([
				'msg1'			=> "",
				'msg2'			=> "",
				'msg3'			=> "",
				'msg4'			=> "",
				'msg5'			=> "",
			]);

		$list_msg = [];
		if( !empty($db_data) ){
			if( !empty($db_data->msg1) ){
				$list_msg[] = $db_data->msg1;
			}
			if( !empty($db_data->msg2) ){
				$list_msg[] = $db_data->msg2;
			}
			if( !empty($db_data->msg3) ){
				$list_msg[] = $db_data->msg3;
			}
			if( !empty($db_data->msg4) ){
				$list_msg[] = $db_data->msg4;
			}
			if( !empty($db_data->msg5) ){
				$list_msg[] = $db_data->msg5;
			}

			$list_update_val = [];
			foreach($list_msg as $index => $msg){
				if( preg_match("/(imglink)?msg\d+_\d+_\d+(\.png|\.jpeg|\.jpg|\.gif)$/", $msg, $extensions) > 0 ){
//error_log(print_r($extensions,true)."::ffff\n",3,"/data/www/line/storage/logs/nishi_log.txt");
					$new_msg = $extensions[1].'msg'.($index+1).'_'.$id.'_'.$type.$extensions[2];
					File::move(config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$channel_id.'/img/'.$msg, 
								config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$channel_id.'/img/'.$new_msg);
					$msg = $new_msg;
				}
				$list_update_val['msg'.($index+1)] = $msg;
			}
			LineContents::where('line_basic_id', $channel_id)->where('id', $id)->where('type', $type)->update($list_update_val);
		}

		//リダイレクト
		return config('const.base_admin_url').'/member/line/setting/replay/'.$channel_id;
	}

	/*
	 *	テキストメッセージの削除
	 */
	public function deleteFollowMsg(Request $request, $channel_id, $id, $type)
	{
		$msg	= $request->input('msg');
//error_log("{$channel_id} {$id} {$type} {$msg}::ffff\n",3,"/data/www/line/storage/logs/nishi_log.txt");

		//更新
		LineContents::where('line_basic_id', $channel_id)->where('id', $id)->where('type', $type)->update([$msg => '']);

		//更新後のデータ取得
		$db_data = 	LineContents::where('line_basic_id', $channel_id)->where('id', $id)->where('type', $type)->first();

		LineContents::where('line_basic_id', $channel_id)
			->where('id', $id)
			->where('type', $type)
			->update([
				'msg1'			=> "",
				'msg2'			=> "",
				'msg3'			=> "",
				'msg4'			=> "",
				'msg5'			=> "",
			]);

		$list_msg = [];
		if( !empty($db_data) ){
			if( !empty($db_data->msg1) ){
				$list_msg[] = $db_data->msg1;
			}
			if( !empty($db_data->msg2) ){
				$list_msg[] = $db_data->msg2;
			}
			if( !empty($db_data->msg3) ){
				$list_msg[] = $db_data->msg3;
			}
			if( !empty($db_data->msg4) ){
				$list_msg[] = $db_data->msg4;
			}
			if( !empty($db_data->msg5) ){
				$list_msg[] = $db_data->msg5;
			}

			$list_update_val = [];
			foreach($list_msg as $index => $msg){
				if( preg_match("/(imglink)?msg\d+_\d+_\d+(\.png|\.jpeg|\.jpg|\.gif)$/", $msg, $extensions) > 0 ){
//error_log(print_r($extensions,true)."::ffff\n",3,"/data/www/line/storage/logs/nishi_log.txt");
					$new_msg = $extensions[1].'msg'.($index+1).'_'.$id.'_'.$type.$extensions[2];
					File::move(config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$channel_id.'/img/'.$msg, 
								config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$channel_id.'/img/'.$new_msg);
					$msg = $new_msg;
				}
				$list_update_val['msg'.($index+1)] = $msg;
			}
			LineContents::where('line_basic_id', $channel_id)->where('id', $id)->where('type', $type)->update($list_update_val);
		}

		//リダイレクト
		return config('const.base_admin_url').'/member/line/setting/replay/'.$channel_id;
	}
}
