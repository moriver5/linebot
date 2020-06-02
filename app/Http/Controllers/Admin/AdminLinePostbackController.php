<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libs\SysLog;
use App\Model\LineOfficialAccount;
use App\Model\LineContentType;
use App\Model\LineContents;
use App\Model\Line_postback_template;
use DB;
use File;
use Utility;
use Carbon\Carbon;

class AdminLinePostbackController extends Controller
{
//	private $log_obj;

	public function __construct()
	{
		//ログファイルのインスタンス生成
		//引数：ログの操作項目、ログファイルまでのフルパス
//		$this->log_obj = new SysLog(config('const.operation_export_log_name'), config('const.system_log_dir_path').config('const.operation_history_file_name'));
	}

	/*
	 * LINEチャンネルのリスト
	 */
	public function index($channel_id)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		$db_data = LineOfficialAccount::query()
			->whereIn('line_official_accounts.line_basic_id',function($query) use($user){
				$query->select('line_basic_id')->from('admin_channel_allow_lists')->where('admin_id', $user['id']);
			})
			->where('line_official_accounts.line_basic_id', $channel_id)
			->first();

		$postback_data = Line_Postback_template::where('line_basic_id', $channel_id)->get();

		$disp_data = [
			'channel_id'		=> $channel_id,
			'db_data'			=> $db_data,
			'postback_data'		=> $postback_data,
			'ver' => time(),
		];
		
		return view('admin.master.line_postback', $disp_data);
	}

	/*
	 *  自動メール文設定画面表示
	 */
	public function index2($channel_id, $id = '')
	{
		$list_msg = [];

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		$db_data = LineContents::select('line_contents.id', 'line_contents.line_basic_id', 'line_contents.type', 'line_contents.msg1', 'line_contents.msg2', 'line_contents.msg3', 'line_contents.msg4', 'line_contents.msg5', 'line_content_types.name', 'line_contents.image')->join('line_content_types', 'line_contents.type', '=', 'line_content_types.id')->where('line_basic_id', $channel_id)->get();
		if( !empty($db_data) ){
			foreach($db_data as $lines){
				if( !empty($lines->msg1) ){
					if( preg_match("/msg\d+_\d+_\d+\.png/", $lines->msg1) > 0 ){
						$full_path_image = config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$channel_id.'/img/'.$lines->msg1;
						if( file_exists($full_path_image) ){
							//画像を管理画面上のプレビュー用にコピー
							File::copy($full_path_image, '/data/www/line/public/images/preview/'.$lines->msg1);
						}
					}
					$list_msg[$lines->id.'_'.$lines->type]['msg1'] = $lines->msg1;
				}
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
	 *  
	 */
	public function create($channel_id, $id = null)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		$db_data = [];
		$list_msg = [];
		$title = "";

//		$postback_data = Line_Postback_template::where('line_basic_id', $channel_id)->get();

		//バナーがtop_contentsテーブルに登録されていたら取得
		if( !empty($id) ){
			$db_data = DB::table("line_postback_templates")
				->select('line_postback_templates.*')
				->join("line_official_accounts", "line_postback_templates.line_basic_id", "=", "line_official_accounts.line_basic_id")
				->where('line_postback_templates.id', $id)
				->first();

			if( !empty($db_data->msg1) ){
				$list_msg['msg1'] = $db_data->msg1;
			}
			if( !empty($db_data->msg2) ){
				$list_msg['msg2'] = $db_data->msg2;
			}
			if( !empty($db_data->msg3) ){
				$list_msg['msg3'] = $db_data->msg3;
			}
			if( !empty($db_data->msg4) ){
				$list_msg['msg4'] = $db_data->msg4;
			}
			if( !empty($db_data->msg5) ){
				$list_msg['msg5'] = $db_data->msg5;
			}

			$title = $db_data->name;
		}

		$disp_data = [
			'edit_id'		=> $id,
			'channel_id'	=> $channel_id,
			'list_msg'		=> $list_msg,
			'db_data'		=> $db_data,
			'title'			=> $title,
//			'postback_data'	=> $postback_data,
			'redirect_url'	=> config('const.base_admin_url').'/member/line/setting/postback/create/'.$channel_id,
			'ver' => time(),
		];

		return view('admin.master.postback_create', $disp_data);
	}

	/*
	 * 画像のアップロード処理
	 */
	public function uploadPostbackImgUpload(Request $request, $channel_id)
	{
		//アップロード画像情報取得
		$file = $request->file('import_file');

		//landing_pagesテーブルに登録されているidを取得
		$id					 = $request->input('edit_id');
		$msg				 = $request->input('msg');
		$img_name			 = $file->getClientOriginalName();
		$now_date			 = Carbon::now();
//error_log("$channel_id, $file, $id, $msg, $send_type, $now_date, $reserve_date::name\n",3,"/data/www/line/storage/logs/nishi_log.txt");

		try{
			$id = DB::transaction(function() use($channel_id, $file, $id, $msg, $now_date){
				//top_contentsテーブルにまだ登録されていないとき
				if( is_null($id) ){
					//最初の１回目の画像アップロードはinsert
					$id = Line_postback_template::insertGetId([
						'line_basic_id'	=> $channel_id,
						$msg			=> $msg,
						'created_at'	=> $now_date,
						'updated_at'	=> $now_date
					]);
				}

				//画像名をid名にするためupdateを行う
				Line_postback_template::where('id', $id)->update([
					'postback'		=> config('const.postback_val_prefix').$id,
					$msg			=> config('const.postback_img_prefix').$msg.'_'.$id.'.'.$file->getClientOriginalExtension(),
				]);
//				throw new \Exception("テスト例外エラー");
				return $id;
			});
		}catch(\Exception $e){
			return response()->json(['error' => [__("messages.dialog_update_failed")]],400);
		}

		//画像の保存先を移動
		$file->move(config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$channel_id.'/img', config('const.postback_img_prefix').$msg.'_'.$id.'.'.$file->getClientOriginalExtension());

		//画像を管理画面上のプレビュー用にコピー
		system('ln -s '.config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$channel_id.'/img/'.config('const.postback_img_prefix').$msg.'_'.$id.'.'.$file->getClientOriginalExtension().' /data/www/line/public/images/preview/');

		//更新データを取得
		$db_data = Line_postback_template::where('id',$id)->first();

		//メッセージをすべて空で更新
		Line_postback_template::where('id', $id)->update([
			'msg1'	=> "",
			'msg2'	=> "",
			'msg3'	=> "",
			'msg4'	=> "",
			'msg5'	=> "",
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
			$img_prefix = config('const.postback_img_prefix');
			foreach($list_msg as $index => $msg){
				//画像データなら
				if( preg_match("/{$img_prefix}msg\d+_\d+\.(png|jpg|jpeg)$/", $msg, $extension) > 0 ){
					$new_msg = $img_prefix.'msg'.($index+1).'_'.$id.'.'.$extension[1];
					//詰める画像名にするため移動する
					File::move(config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$channel_id.'/img/'.$msg, 
								config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$channel_id.'/img/'.$new_msg);
					//プレビュー画像も同様に移動する
//					File::move('/data/www/line/public/images/preview/'.$msg, '/data/www/line/public/images/preview/'.$new_msg);
					system('ln -s '.config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$channel_id.'/img/'.$new_msg.' /data/www/line/public/images/preview/');
					$msg = $new_msg;
				}
				$list_update_val['msg'.($index+1)] = $msg;
			}

			//詰めたmsg1～msg5のデータを更新
			Line_postback_template::where('id', $id)->update($list_update_val);
		}

		//ログイン管理者情報取得
//		$user = Utility::getAdminDefaultDispParam();

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
	 * 
	 */
	public function saveLinePostbackData(Request $request){
		$page				 = $request->input('page');
		$line_basic_id		 = $request->input('channel_id');
		$postback_id		 = $request->input('edit_id');
		$type				 = $request->input('type');
		$msg				 = $request->input('msg');
//error_log(print_r($request->all(),true)."\n",3,"/data/www/line/storage/logs/nishi_log.txt");			
//error_log("{$line_basic_id} {$postback_id} {$msg} {$type}::dddd\n",3,"/data/www/line/storage/logs/nishi_log.txt");

		$db_value = [];

		//現在時刻を取得
		$now_date = Carbon::now();

		if( !empty($request->input('push_title')) ){
			$db_value['name'] = $request->input('push_title');
		}

		//下書き保存
		if( !empty($msg) ){
			$db_value = array_merge($db_value, [
				'line_basic_id'			=> $line_basic_id,
				$msg					=> $request->input($msg),					//HTML内容
				'created_at'			=> $now_date,
				'updated_at'			=> $now_date
			]);

		//編集データ
		}else{
			//個別メッセージ保存時(下書き保存ボタン押下)
			if( !empty($request->input('msg')) ){
				$db_value[$msg] = $request->input($msg);

			//ポストバック設定保存ボタン押下
			}else{
				if( !empty($request->input('msg1')) ){
					$db_value['msg1'] = $request->input('msg1');
				}
				if( !empty($request->input('msg2')) ){
					$db_value['msg2'] = $request->input('msg2');
				}
				if( !empty($request->input('msg3')) ){
					$db_value['msg3'] = $request->input('msg3');
				}
				if( !empty($request->input('msg4')) ){
					$db_value['msg4'] = $request->input('msg4');
				}
				if( !empty($request->input('msg5')) ){
					$db_value['msg5'] = $request->input('msg5');
				}
			}

			$db_value = array_merge($db_value, [
				'postback'				=> config('const.postback_val_prefix').$postback_id,
				'line_basic_id'			=> $line_basic_id,
				'name'					=> $request->input('push_title'),
			]);
		}

		if( empty($postback_id) ){
			$postback_id = Line_postback_template::insertGetId($db_value);
			Line_postback_template::where('id', $postback_id)->update(['postback' => config('const.postback_val_prefix').$postback_id]);
		}else{
			Line_postback_template::where('id', $postback_id)->update($db_value);
		}
//error_log("{$postback_id}::postback_id\n",3,"/data/www/line/storage/logs/nishi_log.txt");

		//登録データID
		return $postback_id;
	}

	public function deletePostback(Request $request, $channel_id)
	{
		$line_basic_id = $request->input('channel_id');
		$line_push_id = $request->input('edit_id');
		$msg = $request->input('msg');

//error_log(print_r($request->all(),true)."\n",3,"/data/www/line/storage/logs/nishi_log.txt");			
//error_log("{$line_basic_id} {$line_push_id} {$msg}::dddd\n",3,"/data/www/line/storage/logs/nishi_log.txt");

		Line_postback_template::where('id', $line_push_id)->update([$msg => ""]);

		$db_data = Line_postback_template::where('id',$line_push_id)->first();

		Line_postback_template::where('id', $line_push_id)->update([
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
			$img_prefix = config('const.postback_img_prefix');
			foreach($list_msg as $index => $msg){
				if( preg_match("/{$img_prefix}_msg\d+_\d+\.(png|jpg|jpeg)$/", $msg) > 0 ){
					$new_msg = $img_prefix.'_msg'.($index+1).'_'.$line_push_id.'.png';
					File::move(config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$channel_id.'/img/'.$msg, 
								config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$channel_id.'/img/'.$new_msg);
					File::move('/data/www/line/public/images/preview/'.$msg, '/data/www/line/public/images/preview/'.$new_msg);
					$msg = $new_msg;
				}
				$list_update_val['msg'.($index+1)] = $msg;
			}

			Line_postback_template::where('id', $line_push_id)->update($list_update_val);
		}
//		return $channel_id.'/'.$line_push_id;
		return $line_push_id;
	}

}
