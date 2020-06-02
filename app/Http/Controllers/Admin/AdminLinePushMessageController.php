<?php

namespace App\Http\Controllers\Admin;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\LineUser;
use App\Model\LineUserProfile;
use App\Model\LinePushLog;
use App\Model\LineTempImmediateMsg;
use App\Model\Line_messages_history_log;
use App\Model\Line_carousel_template;
use App\Model\Line_imagemap_log;
use App\Libs\SysLog;
use Carbon\Carbon;
use Session;
use Utility;
use DB;
use File;
use Image;

class AdminLinePushMessageController extends Controller
{
	private $log_obj;

	public function __construct()
	{
		//ログファイルのインスタンス生成
		//引数：ログの操作項目、ログファイルまでのフルパス
		$this->log_obj	 = new SysLog(config('const.operation_export_log_name'), config('const.system_log_dir_path').config('const.operation_history_file_name'));
	}

	/*
	 * LINEメッセージの即時配信トップ画面表示
	 */
	public function index($channel_id, $id = "")
	{
		$db_data = [];
		$list_msg = [];

		//バナーがtop_contentsテーブルに登録されていたら取得
		if( !empty($id) ){
			$db_data = DB::table("line_push_logs")
				->join("line_official_accounts", "line_push_logs.line_basic_id", "=", "line_official_accounts.line_basic_id")
				->where('line_push_logs.id', $id)
				->first();
			if( !empty($db_data->msg1) ){
				$list_msg['msg1'] = [$db_data->msg1, $db_data->send_date];
			}
			if( !empty($db_data->msg2) ){
				$list_msg['msg2'] = [$db_data->msg2, $db_data->send_date];
			}
			if( !empty($db_data->msg3) ){
				$list_msg['msg3'] = [$db_data->msg3, $db_data->send_date];
			}
			if( !empty($db_data->msg4) ){
				$list_msg['msg4'] = [$db_data->msg4, $db_data->send_date];
			}
			if( !empty($db_data->msg5) ){
				$list_msg['msg5'] = [$db_data->msg5, $db_data->send_date];
			}
		}

		//画面表示用配列
		$disp_data = [
			'edit_id'		=> $id,
			'db_data'		=> $db_data,
			'list_msg'		=> $list_msg,
			'channel_id'	=> $channel_id,
			'redirect_url'	=> config('const.base_admin_url').'/member/line/push/message/'.$channel_id,
			'ver'			=> time()
		];

		return view('admin.push.index', $disp_data);
	}

	/*
	 * 
	 */
	public function saveLinePushMessage(Request $request){
		$page				 = $request->input('page');
		$line_basic_id		 = $request->input('channel_id');
		$line_push_id		 = $request->input('edit_id');
		$send_type			 = $request->input('send_type');
		$reserve_date		 = $request->input('tmp_reserve_date');
		$tmp_regular_time	 = $request->input('tmp_regular_time');
		$tmp_reserve_week	 = $request->input('tmp_reserve_week');
		$tmp_after_minute		 = $request->input('tmp_send_after_minute');
		$msg				 = $request->input('msg');
//error_log(print_r($request->all(),true)."\n",3,"/data/www/line/storage/logs/nishi_log.txt");			
//error_log("{$line_basic_id} {$line_push_id} {$msg} {$send_type} {$reserve_date} {$tmp_regular_time} {$tmp_after_minute}::dddd\n",3,"/data/www/line/storage/logs/nishi_log.txt");

		$db_value = [];

		//現在時刻を取得
		$now_date = Carbon::now();

		//予約配信日時
		if( $reserve_date != "" ){
			$db_value['reserve_send_date'] = $reserve_date;
			$db_value['sort_reserve_send_date'] = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $reserve_date).'00';

		}else{
			if( $send_type == 1 ){
				$db_value['reserve_send_date'] = $now_date;
				//現在時刻をyyyymmddhhmmにフォーマット
				$db_value['sort_reserve_send_date'] = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00';
			}
		}

		if( isset($tmp_after_minute) ){
			$db_value['send_after_minute'] = $tmp_after_minute;
		}

		if( !empty($tmp_reserve_week) ){
			$db_value['send_week'] = $tmp_reserve_week;
		}

		//配信指定日時
		if( $tmp_regular_time != "" ){
			$db_value['send_regular_time'] = $tmp_regular_time;
		}

		if( empty($line_push_id) ){
			$db_value = array_merge($db_value, [
				'line_basic_id'			=> $line_basic_id,
				'send_type'				=> $send_type,								//配信タイプ:
				'send_status'			=> 99,										//送信状況:99(送信前の保存)
				'send_count'			=> 0,										//送信数
				$msg					=> $request->input($msg),					//HTML内容
				'created_at'			=> $now_date,
				'updated_at'			=> $now_date
			]);

			$line_push_id = LinePushLog::insertGetId($db_value);
		}else{
			$db_value = array_merge($db_value, [
				'line_basic_id'			=> $line_basic_id,
				'send_status'			=> 99,										//送信状況:99(送信前の保存)
				$msg					=> $request->input($msg),					//HTML内容
			]);

			LinePushLog::where('id', $line_push_id)->update($db_value);
		}

		//予約配信
		if( $send_type ){
			return config('const.base_url').'/admin/member/line/reserve/status/edit/'.$page.'/'.$send_type.'/'.$line_basic_id.'/'.$line_push_id;

		//即時配信
		}else{
			return config('const.base_url').'/admin/member/line/push/message/'.$line_basic_id.'/'.$line_push_id;
		}
	}

	/*
	 * LINEメッセージの即時配信
	 */
	public function sendImmediateLinePushMessage(Request $request){
		$this->validate($request, [
			'msg1'		=> 'bail|surrogate_pair_check',
		]);

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['melmaga_send_immediate'].",{$user['login_id']}");

		$line_basic_id	 = $request->input('channel_id');
		$line_push_id	 = $request->input('edit_id');
		$send_type		 = $request->input('send_type');

		//友達登録者でdisable=0のデータ取得
		$list_user = LineUser::select(['line_users.id','line_users.user_line_id'])
			->where('line_basic_id', $line_basic_id)
			->where('follow_flg', 1)
			->where('disable', 0)
			->get();

		//現在時刻を取得
		$now_date = Carbon::now();

		//現在時刻をyyyymmddhhmmにフォーマット
		$sort_reserve_date = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00';

		if( empty($line_push_id) ){
			//メルマガログに送信情報を登録
			$line_push_id = LinePushLog::insertGetId([
				'line_basic_id'			=> $line_basic_id,
				'send_type'				=> $send_type,								//配信タイプ:0(即時配信)
				'send_status'			=> 0,							//送信状況:0(配信待ち)
				'send_count'			=> 0,										//送信数
				'msg1'					=> $request->input('msg1'),					//HTML内容
				'msg2'					=> $request->input('msg2'),					//HTML内容
				'msg3'					=> $request->input('msg3'),					//HTML内容
				'msg4'					=> $request->input('msg4'),					//HTML内容
				'msg5'					=> $request->input('msg5'),					//HTML内容
				'sort_reserve_send_date'=> $sort_reserve_date,
				'created_at'			=> $now_date,
				'updated_at'			=> $now_date
			]);
		}else{
			$update_value = [
				'line_basic_id'			=> $line_basic_id,
				'send_type'				=> $send_type,								//配信タイプ:0(即時配信)
				'send_status'			=> 0,							//送信状況:0(配信待ち)
				'send_count'			=> 0,										//送信数
				'sort_reserve_send_date'=> $sort_reserve_date,
				'created_at'			=> $now_date,
				'updated_at'			=> $now_date
			];

			if( !empty($request->input('msg1')) ){
				$update_value['msg1'] = $request->input('msg1');
			}
			if( !empty($request->input('msg2')) ){
				$update_value['msg2'] = $request->input('msg2');
			}
			if( !empty($request->input('msg3')) ){
				$update_value['msg3'] = $request->input('msg3');
			}
			if( !empty($request->input('msg4')) ){
				$update_value['msg4'] = $request->input('msg4');
			}
			if( !empty($request->input('msg5')) ){
				$update_value['msg5'] = $request->input('msg5');
			}

			LinePushLog::where('id', $line_push_id)->update($update_value);
		}
//error_log($request->input('relay_server_flg')."::dddd\n",3,"/data/www/melmaga/storage/logs/nishi_log.txt");
//exit;

		//即時メルマガ配信先のクライアントIDを登録
		foreach($list_user as $lines){
			DB::insert('insert ignore into line_temp_immediate_msgs(line_push_id, user_line_id, created_at, updated_at) values('
			.$line_push_id.',"'
			.$lines->user_line_id.'","'
			.$now_date.'","'
			.$now_date.'")');

			DB::insert('insert ignore into line_messages_history_logs(line_push_id, user_line_id, sort_date, created_at, updated_at) values('
			.$line_push_id.',"'
			.$lines->user_line_id.'",'
			.$sort_reserve_date.',"'
			.$now_date.'","'
			.$now_date.'")');
		}
//error_log("{$line_basic_id} {$line_push_id}:history\n",3,"/data/www/line/storage/logs/nishi_log.txt");

		//バックグラウンドでアクセス一覧のユーザーにメール配信
		$process = new Process(config('const.artisan_command_path')." line:broadcast {$line_basic_id} {$line_push_id} > /dev/null");

		//非同期実行
		$process->start();

		//非同期実行の場合は別プロセスが実行する前に終了するのでsleepを入れる
		//1秒待機
		usleep(1000000);

		return null;
	}

	/*
	 * LINEメッセージ配信履歴
	 */
	public function historySendLinePushMessages($channel_id){
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['melmaga_history'].",{$user['login_id']}");

		//配信ログ取得(send_status:4は履歴を残さない)
		$db_data = LinePushLog::where('line_basic_id', $channel_id)->whereNotIn('send_status', [4, 5])->orderBy('sort_reserve_send_date' , 'desc')->paginate(config('const.admin_client_list_limit'));

		$disp_data = [
			'db_data'			=> $db_data,
			'ver'				=> time()
		];

		return view('admin.push.push_message_history', $disp_data);
	}

	/*
	 * 画像のアップロード処理
	 */
	public function uploadPushMessageImgUpload(Request $request, $channel_id)
	{
		//アップロード画像情報取得
		$file = $request->file('import_file');

		//landing_pagesテーブルに登録されているidを取得
		$id					 = $request->input('edit_id');
		$msg				 = $request->input('msg');
		$send_type			 = $request->input('send_type');
		$reserve_date		 = $request->input('tmp_reserve_date');
		$tmp_regular_time	 = $request->input('tmp_regular_time');
		$img_name			 = $file->getClientOriginalName();
		$now_date			 = Carbon::now();
//error_log("$channel_id, $file, $id, $msg, $send_type, $now_date, $reserve_date::name\n",3,"/data/www/line/storage/logs/nishi_log.txt");
//error_log(print_r($request->all(),true)."\n",3,"/data/www/line/storage/logs/nishi_log.txt");
		try{
			$id = DB::transaction(function() use($channel_id, $file, $id, $msg, $send_type, $now_date){
				//top_contentsテーブルにまだ登録されていないとき
				if( is_null($id) ){
					//最初の１回目の画像アップロードはinsert
					$id = LinePushLog::insertGetId([
						'line_basic_id'	=> $channel_id,
						'send_type'		=> $send_type,		//配信タイプ:
						'send_status'	=> 99,				//配信状況:99(送信前の保存)
						'send_count'	=> 0,
						$msg			=> $msg,
						'created_at'	=> $now_date,
						'updated_at'	=> $now_date
					]);
				}

				//画像名をid名にするためupdateを行う
				LinePushLog::where('id', $id)->update([
					'send_status'	=> 99,														//配信状況:99(送信前の保存)
					$msg			=> $msg.'_'.$id.'.'.$file->getClientOriginalExtension(),
				]);
//				throw new \Exception("テスト例外エラー");
				return $id;
			});
		}catch(\Exception $e){
			return response()->json(['error' => [__("messages.dialog_update_failed")]],400);
		}

		//画像の保存先を移動
		$file->move(config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$channel_id.'/img', $msg.'_'.$id.'.'.$file->getClientOriginalExtension());

		//画像を管理画面上のプレビュー用にコピー
/*
		File::copy(config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$channel_id.'/img/'.$msg.'_'.$id.'.'.$file->getClientOriginalExtension(), 
			'/data/www/line/public/images/preview/'.$msg.'_'.$id.'.'.$file->getClientOriginalExtension());
*/
		system('ln -s '.config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$channel_id.'/img/'.$msg.'_'.$id.'.'.$file->getClientOriginalExtension().' /data/www/line/public/images/preview/');

		//更新データを取得
		$db_data = LinePushLog::where('id',$id)->first();

		if( $reserve_date != "" ){
			$sort_reserve_date = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $reserve_date).'00';

		}else{
			//現在時刻をyyyymmddhhmmにフォーマット
			$sort_reserve_date = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00';
		}

		//メッセージをすべて空で更新
		LinePushLog::where('id', $id)->update([
			'reserve_send_date'		=> $reserve_date,							//予約日時
			'sort_reserve_send_date'=> $sort_reserve_date,
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
				if( preg_match("/msg\d+_\d+\.png$/", $msg) > 0 ){
					$new_msg = 'msg'.($index+1).'_'.$id.'.png';
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
			LinePushLog::where('id', $id)->update($list_update_val);
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
	 * イメージマップ画像のアップロード処理
	 */
	public function uploadPushMessageImgMapUpload(Request $request, $channel_id)
	{
		$this->validate($request, [
			'import_file' => 'bail|required|check_upload_image_width'
		]);

		//アップロード画像情報取得
		$file = $request->file('import_file');

		//landing_pagesテーブルに登録されているidを取得
		$id					 = $request->input('edit_id');
		$msg				 = $request->input('msg');
		$send_type			 = $request->input('send_type');
		$reserve_date		 = $request->input('tmp_reserve_date');
		$tmp_regular_time	 = $request->input('tmp_regular_time');
		$img_name			 = $file->getClientOriginalName();
		$now_date			 = Carbon::now();
//error_log("$channel_id, $file, $id, $msg, $send_type, $now_date, $reserve_date::name\n",3,"/data/www/line/storage/logs/nishi_log.txt");
//error_log(print_r($request->all(),true)."\n",3,"/data/www/line/storage/logs/nishi_log.txt");
		try{
			$id = DB::transaction(function() use($channel_id, $file, $id, $msg, $send_type, $now_date){
				//top_contentsテーブルにまだ登録されていないとき
				if( is_null($id) ){
					//最初の１回目の画像アップロードはinsert
					$id = LinePushLog::insertGetId([
						'line_basic_id'	=> $channel_id,
						'send_type'		=> $send_type,		//配信タイプ:
						'send_status'	=> 99,				//配信状況:99(送信前の保存)
						'send_count'	=> 0,
						$msg			=> $msg,
						'created_at'	=> $now_date,
						'updated_at'	=> $now_date
					]);
				}

				//画像名をid名にするためupdateを行う
				LinePushLog::where('id', $id)->update([
					'send_status'	=> 99,																//配信状況:99(送信前の保存)
					$msg			=> 'imglink'.$msg.'_'.$id.'.'.$file->getClientOriginalExtension(),
				]);
//				throw new \Exception("テスト例外エラー");
				return $id;
			});
		}catch(\Exception $e){
			return response()->json(['error' => [__("messages.dialog_update_failed")]],400);
		}

		$image_path = config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$channel_id.'/img';
		$image_name = 'imglink'.$msg.'_'.$id.'.'.$file->getClientOriginalExtension();

		//画像の保存先を移動
		$file->move($image_path, $image_name);

		$resize_path = $image_path.'/imglink'.$msg.'_'.$id.'_1040.'.$file->getClientOriginalExtension();
		$original_image = Image::make($image_path.'/'.$image_name);
		$original_image->resize(1040, null, function ($constraint) {
			$constraint->aspectRatio();
		});
		$original_image->save($resize_path);

		$resize_path = $image_path.'/imglink'.$msg.'_'.$id.'_700.'.$file->getClientOriginalExtension();
		$original_image = Image::make($image_path.'/'.$image_name);
		$original_image->resize(700, null, function ($constraint) {
			$constraint->aspectRatio();
		});
		$original_image->save($resize_path);

		$resize_path = $image_path.'/imglink'.$msg.'_'.$id.'_460.'.$file->getClientOriginalExtension();
		$original_image = Image::make($image_path.'/'.$image_name);
		$original_image->resize(460, null, function ($constraint) {
			$constraint->aspectRatio();
		});
		$original_image->save($resize_path);

		$resize_path = $image_path.'/imglink'.$msg.'_'.$id.'_300.'.$file->getClientOriginalExtension();
		$original_image = Image::make($image_path.'/'.$image_name);
		$original_image->resize(300, null, function ($constraint) {
			$constraint->aspectRatio();
		});
		$original_image->save($resize_path);

		$resize_path = $image_path.'/imglink'.$msg.'_'.$id.'_240.'.$file->getClientOriginalExtension();
		$original_image = Image::make($image_path.'/'.$image_name);
		$original_image->resize(240, null, function ($constraint) {
			$constraint->aspectRatio();
		});
		$original_image->save($resize_path);

		//画像を管理画面上のプレビュー用にコピー
		system('ln -s '.$image_path.'/'.$image_name.' /data/www/line/public/images/preview/');

		//更新データを取得
		$db_data = LinePushLog::where('id',$id)->first();

		if( $reserve_date != "" ){
			$sort_reserve_date = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $reserve_date).'00';

		}else{
			//現在時刻をyyyymmddhhmmにフォーマット
			$sort_reserve_date = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00';
		}

		//メッセージをすべて空で更新
		LinePushLog::where('id', $id)->update([
			'reserve_send_date'		=> $reserve_date,							//予約日時
			'sort_reserve_send_date'=> $sort_reserve_date,
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
				if( preg_match("/imglinkmsg\d+_\d+(\.png|\.jpg|\.jpeg)$/", $msg, $match) > 0 ){
					$new_msg = 'imglinkmsg'.($index+1).'_'.$id.$match[1];

					//詰める画像名にするため移動する
					File::move($image_path.'/'.$msg, $image_path.'/'.$new_msg);

					//プレビュー画像も同様に移動する
					system('ln -s '.$image_path.'/'.$new_msg.' /data/www/line/public/images/preview/');

					$msg = $new_msg;
				}
				$list_update_val['msg'.($index+1)] = $msg;
			}

			//詰めたmsg1～msg5のデータを更新
			LinePushLog::where('id', $id)->update($list_update_val);
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

	public function deletePushMessage(Request $request, $channel_id)
	{
		$line_basic_id = $request->input('channel_id');
		$line_push_id = $request->input('edit_id');
		$msg = $request->input('msg');

//error_log(print_r($request->all(),true)."\n",3,"/data/www/line/storage/logs/nishi_log.txt");			
//error_log("{$line_basic_id} {$line_push_id} {$msg}::dddd\n",3,"/data/www/line/storage/logs/nishi_log.txt");

		LinePushLog::where('id', $line_push_id)->update([$msg => ""]);

		$db_data = LinePushLog::where('id',$line_push_id)->first();

		LinePushLog::where('id', $line_push_id)->update([
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
				if( preg_match("/msg\d+_\d+\.png$/", $msg) > 0 ){
					$new_msg = 'msg'.($index+1).'_'.$line_push_id.'.png';
					File::move(config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$channel_id.'/img/'.$msg, 
								config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$channel_id.'/img/'.$new_msg);
//					File::move('/data/www/line/public/images/preview/'.$msg, '/data/www/line/public/images/preview/'.$new_msg);
					system('ln -s '.config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$channel_id.'/img/'.$new_msg.' /data/www/line/public/images/preview/');
					$msg = $new_msg;
				}
				$list_update_val['msg'.($index+1)] = $msg;
			}

			LinePushLog::where('id', $line_push_id)->update($list_update_val);
		}
//		return $channel_id.'/'.$line_push_id;
		return $line_push_id;
	}

	/*
	 * LINEメッセージ配信履歴
	 */
	public function viewHistorySendPushMessage($page, $send_type, $channel_id, $push_id){
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['melmaga_history'].",{$user['login_id']}");

		$disp_data = [];

		//配信ログ取得(send_status:4は履歴を残さない)
		//カルーセル
		if( $send_type == 5 ){
			$db_data = DB::table("line_carousel_templates")
				->join("line_official_accounts", "line_carousel_templates.line_basic_id", "=", "line_official_accounts.line_basic_id")
				->where('line_carousel_templates.id', $push_id)
				->first();			

		//2択
		}elseif( $send_type == 6 ){
			$db_data = DB::table("line_2choices_templates")
				->select('line_2choices_templates.push_title', 'line_2choices_templates.send_status', 'line_2choices_templates.reserve_send_date', 'line_2choices_templates.reserve_send_date', 'line_2choices_templates.send_date', 'line_2choices_logs.*')
				->join("line_official_accounts", "line_2choices_templates.line_basic_id", "=", "line_official_accounts.line_basic_id")
				->join('line_2choices_logs','line_2choices_templates.id', '=', 'line_2choices_logs.master_id')
				->where('line_2choices_templates.id', $push_id)
				->orderBy('line_2choices_logs.id')
				->first();

		//4択
		}elseif( $send_type == 7 ){

		//イメージマップ
		}elseif( $send_type == 8 ){
				$db_data = Line_imagemap_log::where('id', $push_id)->first();

		}else{
			$db_data = DB::table("line_push_logs")
//				->select("line_official_accounts.line_basic_id", "line_push_logs.line_basic_id")
				->join("line_official_accounts", "line_push_logs.line_basic_id", "=", "line_official_accounts.line_basic_id")
				->where('line_push_logs.id', $push_id)
				->first();
		}

		$list_msg = [];
		if( !empty($db_data) ){
			//カルーセル
			if( $send_type == 5 ){
				foreach($db_data as $lines){
					if( !empty($db_data->img1) ){
						$list_msg['img1'] = [$db_data->img1, $db_data->reserve_send_date, $db_data->title1, $db_data->text1, $db_data->act1, $db_data->label1, $db_data->value1];
					}
					if( !empty($db_data->img2) ){
						$list_msg['img2'] = [$db_data->img2, $db_data->reserve_send_date, $db_data->title2, $db_data->text2, $db_data->act2, $db_data->label2, $db_data->value2];
					}
					if( !empty($db_data->img3) ){
						$list_msg['img3'] = [$db_data->img3, $db_data->reserve_send_date, $db_data->title3, $db_data->text3, $db_data->act3, $db_data->label3, $db_data->value3];
					}
					if( !empty($db_data->img4) ){
						$list_msg['img4'] = [$db_data->img4, $db_data->reserve_send_date, $db_data->title4, $db_data->text4, $db_data->act4, $db_data->label4, $db_data->value4];
					}
					if( !empty($db_data->img5) ){
						$list_msg['img5'] = [$db_data->img5, $db_data->reserve_send_date, $db_data->title5, $db_data->text5, $db_data->act5, $db_data->label5, $db_data->value5];
					}
					if( !empty($db_data->img6) ){
						$list_msg['img6'] = [$db_data->img5, $db_data->reserve_send_date, $db_data->title6, $db_data->text6, $db_data->act6, $db_data->label6, $db_data->value6];
					}
					if( !empty($db_data->img7) ){
						$list_msg['img7'] = [$db_data->img7, $db_data->reserve_send_date, $db_data->title7, $db_data->text7, $db_data->act7, $db_data->label7, $db_data->value7];
					}
					if( !empty($db_data->img8) ){
						$list_msg['img8'] = [$db_data->img8, $db_data->reserve_send_date, $db_data->title8, $db_data->text8, $db_data->act8, $db_data->label8, $db_data->value8];
					}
					if( !empty($db_data->img9) ){
						$list_msg['img9'] = [$db_data->img9, $db_data->reserve_send_date, $db_data->title9, $db_data->text9, $db_data->act9, $db_data->label9, $db_data->value9];
					}
					if( !empty($db_data->img10) ){
						$list_msg['img10'] = [$db_data->img10, $db_data->reserve_send_date, $db_data->title10, $db_data->text10, $db_data->act10, $db_data->label10, $db_data->value10];
					}
				}
				$disp_data['reserve_date'] = $db_data->reserve_send_date;

			}elseif( $send_type == 6 ){

			}elseif( $send_type == 8 ){
				$disp_data['reserve_date']	 = $db_data->reserve_send_date;
				$disp_data['img']				 = $db_data->img;
				$disp_data['channel_id']		 = $channel_id;

				$disp_data['list_area'] = [];
				if( !empty($db_data->area_json) ){
					$listTmpArea = json_decode($db_data->area_json);
					foreach($listTmpArea as $lines){
						$listArea[] = explode(",", $lines);
					}
					$disp_data['list_area'] = $listArea;
				}

			}else{
				if( !empty($db_data->msg1) ){
					$list_msg[] = [$db_data->msg1, $db_data->send_date];
				}
				if( !empty($db_data->msg2) ){
					$list_msg[] = [$db_data->msg2, $db_data->send_date];
				}
				if( !empty($db_data->msg3) ){
					$list_msg[] = [$db_data->msg3, $db_data->send_date];
				}
				if( !empty($db_data->msg4) ){
					$list_msg[] = [$db_data->msg4, $db_data->send_date];
				}
				if( !empty($db_data->msg5) ){
					$list_msg[] = [$db_data->msg5, $db_data->send_date];
				}
			}
		}

		$send_date = Utility::getDayOfWeek($db_data->send_date);

		$disp_data = array_merge($disp_data, [
			'send_date'			=> $send_date,
			'db_data'			=> $db_data,
			'list_msg'			=> $list_msg,
			'ver'				=> time()
		]);

		//カルーセル
		if( $send_type == 5 ){
			return view('admin.push.view_carousel_line_history', $disp_data);

		//イメージマップ
		}elseif( $send_type == 8 ){
			return view('admin.push.view_imagemap_line_history', $disp_data);

		}else{
			return view('admin.push.view_line_history', $disp_data);
		}
	}

	/*
	 * メルマガ配信履歴-配信リスト
	 */
	public function listHistoryUsers($channel_id, $line_push_id)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['melmaga_history_list'].",{$user['login_id']}");

		$access_data = DB::table("line_messages_history_logs")
			->select("line_messages_history_logs.user_line_id", "line_messages_history_logs.read_flg")
			->where('line_messages_history_logs.line_push_id', $line_push_id)
			->orderBy("line_messages_history_logs.sort_date")
			->paginate(config('const.admin_client_list_limit'));

		$disp_data = [
			'total'			=> $access_data->total(),
			'currentPage'	=> $access_data->currentPage(),
			'lastPage'		=> $access_data->lastPage(),
			'links'			=> $access_data->links(),
			'db_data'		=> $access_data,
			'line_push_id'	=> $line_push_id,
			'ver'			=> time(),
		];
		
		return view('admin.push.push_history_list', $disp_data);
	}

}
