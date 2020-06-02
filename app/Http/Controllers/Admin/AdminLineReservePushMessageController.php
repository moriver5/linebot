<?php

namespace App\Http\Controllers\Admin;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\LineUser;
use App\Model\LineUserProfile;
use App\Model\LinePushLog;
use App\Model\Line_click_user;
use App\Model\LineTempImmediateMsg;
use App\Model\Line_carousel_template;
use App\Model\Line_2choices_template;
use App\Model\Line_2choices_log;
use App\Model\Line_4choices_template;
use App\Model\Line_4choices_log;
use App\Model\Line_postback_template;
use App\Model\Line_imagemap_log;
use App\Libs\SysLog;
use Carbon\Carbon;
use Session;
use Utility;
use FormParts;
use DB;

class AdminLineReservePushMessageController extends Controller
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
	public function index($send_type, $channel_id, $id = "")
	{
		$db_data		 = [];
		$list_msg		 = [];
		$reserve_date	 = '';
		$send_date		 = '';

		$disp_data = [
			'segment'		 => [
				'line_id'		 => '',
				'start_reg_date' => '',
				'end_reg_date'	 => '',
				'start_click_date' => '',
				'end_click_date'	 => '',
				'ad_code'		 => '',
				'opt'			 => ''
			],
			'checked0'		 => '',
			'checked1'		 => '',
			'checked2'		 => '',
			'checked3'		 => '',
			'checked4'		 => '',
			'checked5'		 => '',
			'checked6'		 => '',
			'after_minute'	 => '',
		];

		//バナーがtop_contentsテーブルに登録されていたら取得
		if( !empty($id) ){
			$db_data = DB::table("line_push_logs")
				->join("line_official_accounts", "line_push_logs.line_basic_id", "=", "line_official_accounts.line_basic_id")
				->where('line_push_logs.id', $id)
				->first();
			if( !empty($db_data->msg1) ){
				$list_msg['msg1'] = [$db_data->msg1, $db_data->reserve_send_date];
			}
			if( !empty($db_data->msg2) ){
				$list_msg['msg2'] = [$db_data->msg2, $db_data->reserve_send_date];
			}
			if( !empty($db_data->msg3) ){
				$list_msg['msg3'] = [$db_data->msg3, $db_data->reserve_send_date];
			}
			if( !empty($db_data->msg4) ){
				$list_msg['msg4'] = [$db_data->msg4, $db_data->reserve_send_date];
			}
			if( !empty($db_data->msg5) ){
				$list_msg['msg5'] = [$db_data->msg5, $db_data->reserve_send_date];
			}
			if( !empty($db_data->send_regular_time) ){
				$reserve_date = $db_data->send_regular_time;
			}
			if( !empty($db_data->reserve_send_date) ){
				$reserve_date = $db_data->reserve_send_date;				
				$send_date = Utility::getDayOfWeek($reserve_date);
			}

			if( !empty($db_data->send_week) ){
				$listWeeks = explode(",", $db_data->send_week);
				foreach($listWeeks as $week){
					$disp_data['checked'.$week] = 'checked';
				}
			}

			if( isset($db_data->send_after_minute) ){
				$disp_data['after_minute'] = $db_data->send_after_minute;
			}

			if( isset($db_data->segment) ){
				$list_segment = json_decode($db_data->segment, true);
				if( isset($list_segment['segment']['line_id']) ){
					$disp_data['segment']['line_id'] = $list_segment['segment']['line_id'];
				}
				if( isset($list_segment['segment']['start_reg_date']) ){
					$disp_data['segment']['start_reg_date'] = $list_segment['segment']['start_reg_date'];
				}
				if( isset($list_segment['segment']['end_reg_date']) ){
					$disp_data['segment']['end_reg_date'] = $list_segment['segment']['end_reg_date'];
				}
				if( isset($list_segment['segment']['start_click_date']) ){
					$disp_data['segment']['start_click_date'] = $list_segment['segment']['start_click_date'];
				}
				if( isset($list_segment['segment']['end_click_date']) ){
					$disp_data['segment']['end_click_date'] = $list_segment['segment']['end_click_date'];
				}
				if( isset($list_segment['segment']['ad_code']) ){
					$disp_data['segment']['ad_code'] = $list_segment['segment']['ad_code'];
				}
				if( isset($list_segment['segment']['opt']) ){
					$disp_data['segment']['opt'] = $list_segment['segment']['opt'];
				}
			}
		}
		//画面表示用配列
		$disp_data = array_merge($disp_data, [
			'search_like_type' => config('const.search_like_type'),
			'edit_id'		=> $id,
			'send_date'		=> $send_date,
			'reserve_date'	=> $reserve_date,
			'db_data'		=> $db_data,
			'list_msg'		=> $list_msg,
			'channel_id'	=> $channel_id,
			'redirect_url'	=> config('const.base_admin_url').'/member/line/reserve/push/message/'.$send_type.'/'.$channel_id,
			'ver'			=> time()
		]);

		//予約配信
		if( $send_type == 1 ){
			return view('admin.push.index_reserve', $disp_data);

		//毎日配信
		}elseif( $send_type == 2 ){
			return view('admin.push.index_reserve_everyday', $disp_data);

		//毎週配信
		}elseif( $send_type == 3 ){
			return view('admin.push.index_reserve_everyweek', $disp_data);			

		//登録後配信
		}elseif( $send_type == 4 ){
			return view('admin.push.index_reserve_afterregist', $disp_data);			
		}
	}

	/*
	 * LINEメッセージの予約配信
	 */
	public function sendReserveLinePushMessage(Request $request){
		$db_data = ['send_status' => 0];

		if( !empty($request->input('msg1')) ){
			$db_data['msg1'] = $request->input('msg1');
			$validate = ['msg1' => 'bail|required|surrogate_pair_check'];
		}
		if( !empty($request->input('msg2')) ){
			$db_data['msg2'] = $request->input('msg2');
			$validate = ['msg2' => 'bail|required|surrogate_pair_check'];
		}
		if( !empty($request->input('msg3')) ){
			$db_data['msg3'] = $request->input('msg3');
			$validate = ['msg3' => 'bail|required|surrogate_pair_check'];
		}
		if( !empty($request->input('msg4')) ){
			$db_data['msg4'] = $request->input('msg4');
			$validate = ['msg4' => 'bail|required|surrogate_pair_check'];
		}
		if( !empty($request->input('msg5')) ){
			$db_data['msg5'] = $request->input('msg5');
			$validate = ['msg5' => 'bail|required|surrogate_pair_check'];
		}

		$list_segment = [];
		if( !is_null($request->input('line_id')) ){
			$list_segment['segment']['line_id'] = $request->input('line_id');
		}
		if( !is_null($request->input('start_reg_date')) ){
			$list_segment['segment']['start_reg_date'] = $request->input('start_reg_date');
		}
		if( !is_null($request->input('end_reg_date')) ){
			$list_segment['segment']['end_reg_date'] = $request->input('end_reg_date');
		}
		if( !is_null($request->input('start_click_date')) ){
			$list_segment['segment']['start_click_date'] = $request->input('start_click_date');
		}
		if( !is_null($request->input('end_click_date')) ){
			$list_segment['segment']['end_click_date'] = $request->input('end_click_date');
		}
		if( !is_null($request->input('ad_code')) ){
			$list_segment['segment']['ad_code'] = $request->input('ad_code');
		}
		if( !is_null($request->input('opt')) ){
			$list_segment['segment']['opt'] = $request->input('opt');
		}
		if( !is_null($list_segment) ){
			$db_data['segment'] = json_encode($list_segment);
		}

//error_log(print_r($request->all(),true)."\n",3,"/data/www/line/storage/logs/nishi_log.txt");
		//予約配信
		if( $request->input('send_type') == 1 ){
			$db_data = array_merge($db_data, [
				'reserve_send_date'			=> $request->input('reserve_date'),
				'sort_reserve_send_date'	=> preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $request->input('reserve_date')).'00'
			]);
			$validate['reserve_date'] = 'bail|required|date_format_check|surrogate_pair_check|emoji_check';
		}

		//毎週配信
		if( $request->input('send_type') == 3 ){
			$db_data = array_merge($db_data, [
				'send_week' => $request->input('reserve_week')
			]);
			$validate['reserve_week'] = 'bail|required|surrogate_pair_check|emoji_check';
		}

		//毎日・毎週配信
		if( $request->input('send_type') == 2 || 
			$request->input('send_type') == 3 ){
			$db_data = array_merge($db_data, [
				'send_regular_time' => $request->input('regular_time')
			]);
			$validate['regular_time'] = 'bail|required|surrogate_pair_check|emoji_check';
		}

		//登録後配信
		if( $request->input('send_type') == 4 ){
			$db_data = array_merge($db_data, [
				'send_after_minute' => $request->input('send_after_minute')
			]);

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
			$validate['send_after_minute'] = 'bail|required|surrogate_pair_check|emoji_check';
		}
	
		if( !empty($request->input('send_status')) ){
			$db_data['send_status'] = $request->input('send_status');
		}

		$this->validate($request, $validate);

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['melmaga_reserve_send'].",{$user['login_id']}");

		//現在時刻
		$now_date = Carbon::now();

		$line_basic_id = $request->input('channel_id');
		$line_push_id = $request->input('edit_id');
		$send_type = $request->input('send_type');

		if( empty($line_push_id) ){
			$db_data = array_merge($db_data, [
				'line_basic_id'			=> $line_basic_id,
				'send_type'				=> $send_type,	
//				'send_status'			=> 0,										//送信状況:0(配信待ち)
				'send_count'			=> 0,										//送信数
				'created_at'			=> $now_date,
				'updated_at'			=> $now_date
			]);

			//メルマガログに送信情報を登録
			$line_push_id = LinePushLog::insertGetId($db_data);
		}else{
			$db_data = array_merge($db_data, [
				'line_basic_id'			=> $line_basic_id,
				'send_type'				=> $send_type,								//予約配信:1
//				'send_status'			=> 0,										//送信状況:0(配信待ち)
				'send_count'			=> 0,										//送信数
				'created_at'			=> $now_date,
				'updated_at'			=> $now_date
			]);

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
			LinePushLog::where('id', $line_push_id)->update($db_data);
		}

		return $line_push_id;
	}

	/*
	 * LINE予約配信状況
	 */
	public function statusReserveLinePushMessages($send_type, $channel_id){
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['melmaga_reserve_status'].",{$user['login_id']}");

		if( $send_type == 1 ){
			$db_data = ['links' => '', 'currentPage' => '', 'db_data' => []];
			$query = LinePushLog::query();
//			$db_data = $query->select('line_push_logs.id', DB::raw('sum(line_click_users.click) as click'), 'line_push_logs.send_type', 'line_push_logs.send_status', 'line_push_logs.send_count', 'line_push_logs.send_date', 'line_push_logs.reserve_send_date', 'line_push_logs.created_at', 'line_push_logs.line_basic_id')
			$db_tmp_data = $query->select('line_push_logs.id', 'line_push_logs.send_type', 'line_push_logs.send_status', 'line_push_logs.send_count', 'line_push_logs.send_date', 'line_push_logs.reserve_send_date', 'line_push_logs.updated_at', 'line_push_logs.line_basic_id')
//						->leftJoin('line_click_users', 'line_click_users.line_push_id', '=', 'line_push_logs.id')
						->where('line_push_logs.line_basic_id', $channel_id)
						->where('line_push_logs.send_type', $send_type)
//						->groupBy('line_click_users.line_push_id')
						->orderBy('line_push_logs.sort_reserve_send_date', 'desc')
						->paginate(config('const.admin_client_list_limit'));
			if( count($db_tmp_data) > 0 ){
				$db_data['links'] = $db_tmp_data->links();
				$db_data['currentPage'] = $db_tmp_data->currentPage();				
				foreach($db_tmp_data as $lines){
					$db_click = Line_click_user::select(DB::raw('sum(click) as click'))->where('line_push_id', $lines->id)->groupBy('line_push_id')->first();
					$click = 0;
					if( !empty($db_click) ){
						$click = $db_click->click;
					}
					$db_data['db_data'][] = [
						'line_basic_id'		=> $lines->line_basic_id,
						'id'				=> $lines->id,
						'send_type'			=> $lines->send_type,
						'send_status'		=> $lines->send_status,
						'send_date'			=> $lines->send_date,
						'click'				=> $click,
						'send_count'		=> $lines->send_count,
						'reserve_send_date'	=> $lines->reserve_send_date,
						'send_regular_time'	=> $lines->send_regular_time,
						'send_week'			=> $lines->send_week,
						'send_regular_time'	=> $lines->send_regular_time,
						'send_after_minute'	=> $lines->send_after_minute,
						'updated_at'		=> $lines->updated_at,
					];
				}
			}

		}elseif( $send_type == 4 ){
			$db_data = ['links' => '', 'currentPage' => '', 'db_data' => []];
			$query = LinePushLog::query();
//			$db_data = $query->select('line_push_logs.id', DB::raw('sum(line_click_users.click) as click'), 'line_push_logs.send_type', 'line_push_logs.send_status', 'line_push_logs.send_count', 'line_push_logs.send_date', 'line_push_logs.reserve_send_date', 'line_push_logs.created_at', 'line_push_logs.line_basic_id')
			$db_tmp_data = $query->select('line_push_logs.id', 'line_push_logs.send_type', 'line_push_logs.send_status', 'line_push_logs.send_count', 'line_push_logs.send_date', 'line_push_logs.reserve_send_date', 'line_push_logs.updated_at', 'line_push_logs.line_basic_id', 'line_push_logs.send_after_minute')
//						->leftJoin('line_click_users', 'line_click_users.line_push_id', '=', 'line_push_logs.id')
						->where('line_push_logs.line_basic_id', $channel_id)
						->where('line_push_logs.send_type', $send_type)
//						->groupBy('line_click_users.line_push_id')
						->orderBy('line_push_logs.send_after_minute', 'asc')
						->paginate(config('const.admin_client_list_limit'));
			if( count($db_tmp_data) > 0 ){
				$db_data['links'] = $db_tmp_data->links();
				$db_data['currentPage'] = $db_tmp_data->currentPage();				
				foreach($db_tmp_data as $lines){
					$db_click = Line_click_user::select(DB::raw('sum(click) as click'))->where('line_push_id', $lines->id)->groupBy('line_push_id')->first();
					$click = 0;
					if( !empty($db_click) ){
						$click = $db_click->click;
					}
					$db_data['db_data'][] = [
						'line_basic_id'		=> $lines->line_basic_id,
						'id'				=> $lines->id,
						'send_type'			=> $lines->send_type,
						'send_status'		=> $lines->send_status,
						'send_date'			=> $lines->send_date,
						'click'				=> $click,
						'send_count'		=> $lines->send_count,
						'reserve_send_date'	=> $lines->reserve_send_date,
						'send_regular_time'	=> $lines->send_regular_time,
						'send_week'			=> $lines->send_week,
						'send_regular_time'	=> $lines->send_regular_time,
						'send_after_minute'	=> $lines->send_after_minute,
						'updated_at'		=> $lines->updated_at,
					];
				}
			}

		//send_typeが5のときカルーセルデータ
		}elseif( $send_type == 5 ){
			$db_data = Line_carousel_template::where('line_basic_id', $channel_id)
				->where('send_type', 1)
				->orderBy('sort_reserve_send_date', 'desc')
				->paginate(config('const.admin_client_list_limit'));

		//send_typeが6のとき２択データ
		}elseif( $send_type == 6 ){
			$db_data = Line_2choices_template::where('line_basic_id', $channel_id)
				->where('send_type', 1)
				->orderBy('sort_reserve_send_date', 'desc')
				->paginate(config('const.admin_client_list_limit'));
			
		//send_typeが7のとき２択データ
		}elseif( $send_type == 7 ){
			$db_data = Line_4choices_template::where('line_basic_id', $channel_id)
				->where('send_type', 1)
				->orderBy('sort_reserve_send_date', 'desc')
				->paginate(config('const.admin_client_list_limit'));

		//send_typeが8のときイメージマップ
		}elseif( $send_type == 8 ){
			$db_data = Line_imagemap_log::where('line_basic_id', $channel_id)
				->where('send_type', 1)
				->orderBy('sort_reserve_send_date', 'desc')
				->paginate(config('const.admin_client_list_limit'));

		}else{
			//配信ログ取得
			$db_data = LinePushLog::where('line_basic_id', $channel_id)
	//			->whereNotNull('reserve_send_date')
				->where('send_type', $send_type)
				->orderBy('sort_reserve_send_date', 'desc')
				->paginate(config('const.admin_client_list_limit'));
		}

		$disp_data = [
			'send_type'				=> $send_type,
			'db_data'				=> $db_data,
			'cancel_redirect_url'	=> config('const.base_admin_url').'/'.config('const.reserve_status_url_path').'/'.$send_type.'/'.$channel_id,
			'ver'					=> time()
		];

		if( $send_type == 4 ){
			return view('admin.push.push_reserve_afterregist_status', $disp_data);			
		}elseif( $send_type == 1 ){
			return view('admin.push.push_reserve_reserve_status', $disp_data);			
		}else{
			return view('admin.push.push_reserve_status', $disp_data);
		}
	}

	/*
	 * 予約状況から選択したメルマガの編集画面を表示
	 */
	public function editReserveLinePushMessages($page, $send_type, $channel_id, $id){
		$db_data		 = [];
		$list_msg		 = [];
		$reserve_date	 = '';
		$send_date		 = '';
		$disp_data		 = [];
		$list_scenario	 = [];
		$options		 = '';

		//バナーがtop_contentsテーブルに登録されていたら取得
		if( !empty($id) ){
			if( $send_type == 5 ){
				$db_data = DB::table("line_carousel_templates")
					->join("line_official_accounts", "line_carousel_templates.line_basic_id", "=", "line_official_accounts.line_basic_id")
					->where('line_carousel_templates.id', $id)
					->first();

				$reserve_date = $db_data->reserve_send_date;
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
				$disp_data['send_status'] = $db_data->send_status;
				$disp_data['push_title'] = $db_data->push_title;
				$disp_data['save_redirect_url'] = config('const.base_admin_url').'/member/line/setting/carousel/'.$channel_id;

				//ポストバックデータ取得
				$postback_data = Line_postback_template::get();
				if( !empty($postback_data) ){
					foreach($postback_data as $lines){
						$list_postback[] = [$lines->postback, $lines->name];
					}
					$disp_data['list_postback'] = $list_postback;
					$disp_data['postback_options'] = FormParts::getMakeSelectOptions($list_postback, '');
				}

			}elseif( $send_type == 6 ){
				$db_data = DB::table("line_2choices_templates")
					->select('line_2choices_templates.push_title', 'line_2choices_templates.send_status', 'line_2choices_templates.reserve_send_date', 'line_2choices_templates.reserve_send_date', 'line_2choices_logs.*')
					->join("line_official_accounts", "line_2choices_templates.line_basic_id", "=", "line_official_accounts.line_basic_id")
					->join('line_2choices_logs','line_2choices_templates.id', '=', 'line_2choices_logs.master_id')
					->where('line_2choices_templates.id', $id)
					->orderBy('line_2choices_logs.id')
					->get();

				foreach($db_data as $lines){
					$disp_data['list_scenario'][] = ['master_id='.$id.'&'.config('const.scenario_val_prefix').$lines->id, $lines->id];
					$disp_data['send_status']	 = $lines->send_status;
					$disp_data['push_title']	 = $lines->push_title;
					$reserve_date = $lines->reserve_send_date;
				}

				$disp_data['scenario_options'] = FormParts::getMakeSelectOptions($disp_data['list_scenario'], "シナリオID：", $id);
				$disp_data['save_redirect_url'] = config('const.base_admin_url').'/member/line/reserve/status/edit/'.$page.'/'.$send_type.'/'.$channel_id;
//error_log(print_r($disp_data['list_scenario'],true)."\n",3,"/data/www/line/storage/logs/nishi_log.txt");			

				$list_postback = [];

				//ポストバックデータ取得
				$postback_data = Line_postback_template::get();
				if( !empty($postback_data) ){
					foreach($postback_data as $lines){
						$list_postback[] = [$lines->postback, $lines->name];
					}
					$disp_data['list_postback'] = $list_postback;
					$disp_data['postback_options'] = FormParts::getMakeSelectOptions($list_postback, '');
				}

			}elseif( $send_type == 7 ){
				$db_data = DB::table("line_4choices_templates")
					->select('line_4choices_templates.push_title', 'line_4choices_templates.send_status', 'line_4choices_templates.reserve_send_date', 'line_4choices_templates.reserve_send_date', 'line_4choices_templates.img_ratio', 'line_4choices_templates.img_size', 'line_4choices_templates.img', 'line_4choices_logs.*')
					->leftJoin("line_official_accounts", "line_4choices_templates.line_basic_id", "=", "line_official_accounts.line_basic_id")
					->leftJoin('line_4choices_logs','line_4choices_templates.id', '=', 'line_4choices_logs.master_id')
					->where('line_4choices_templates.id', $id)
					->orderBy('line_4choices_logs.id')
					->get();

				foreach($db_data as $lines){
					$disp_data['list_scenario'][] = ['act=4ch&master_id='.$id.'&'.config('const.scenario_val_prefix').$lines->id, $lines->id];
					$disp_data['send_status']	 = $lines->send_status;
					$disp_data['push_title']	 = $lines->push_title;
					$disp_data['img_ratio']		 = $lines->img_ratio;
					$disp_data['img_size']		 = $lines->img_size;
					$disp_data['img']			 = $lines->img;
					$reserve_date = $lines->reserve_send_date;
				}

				$disp_data['scenario_options'] = FormParts::getMakeSelectOptions($disp_data['list_scenario'], "シナリオID：", $id);
				$disp_data['save_redirect_url'] = config('const.base_admin_url').'/member/line/reserve/status/edit/'.$page.'/'.$send_type.'/'.$channel_id;
//error_log(print_r($disp_data['list_scenario'],true)."\n",3,"/data/www/line/storage/logs/nishi_log.txt");			

				$list_postback = [];

				//ポストバックデータ取得
				$postback_data = Line_postback_template::where('line_basic_id', $channel_id)->get();
				if( !empty($postback_data) ){
					foreach($postback_data as $lines){
						$list_postback[] = [$lines->postback, $lines->name];
					}
					$disp_data['list_postback'] = $list_postback;
					$disp_data['postback_options'] = FormParts::getMakeSelectOptions($list_postback, '');
				}

			}elseif( $send_type == 8 ){
				$db_data = Line_imagemap_log::where('id', $id)->orderBy('id')->first();

				$disp_data['send_status']		 = $db_data->send_status;
				$disp_data['alttext']			 = $db_data->alttext;
				$reserve_date					 = $db_data->reserve_send_date;
				$disp_data['img']				 = $db_data->img;

				$disp_data['list_area'] = [];
				if( !empty($db_data->area_json) ){
					$listTmpArea = json_decode($db_data->area_json);
					foreach($listTmpArea as $lines){
						$listArea[] = explode(",", $lines);
					}
					$disp_data['list_area'] = $listArea;
				}
				$disp_data['save_redirect_url'] = config('const.base_admin_url').'/member/line/setting/imagemap/'.$channel_id;

			}else{
				$db_data = DB::table("line_push_logs")
					->join("line_official_accounts", "line_push_logs.line_basic_id", "=", "line_official_accounts.line_basic_id")
					->where('line_push_logs.id', $id)
					->first();

				$reserve_date = $db_data->reserve_send_date;
				if( !empty($db_data->msg1) ){
					$list_msg['msg1'] = [$db_data->msg1, $db_data->reserve_send_date];
				}
				if( !empty($db_data->msg2) ){
					$list_msg['msg2'] = [$db_data->msg2, $db_data->reserve_send_date];
				}
				if( !empty($db_data->msg3) ){
					$list_msg['msg3'] = [$db_data->msg3, $db_data->reserve_send_date];
				}
				if( !empty($db_data->msg4) ){
					$list_msg['msg4'] = [$db_data->msg4, $db_data->reserve_send_date];
				}
				if( !empty($db_data->msg5) ){
					$list_msg['msg5'] = [$db_data->msg5, $db_data->reserve_send_date];
				}
				if( !empty($db_data->send_regular_time) ){
					$reserve_date = $db_data->send_regular_time;
				}
				if( !empty($db_data->reserve_send_date) ){
					$reserve_date = $db_data->reserve_send_date;				
					$send_date = Utility::getDayOfWeek($reserve_date);
				}

				$disp_data = [
					'segment'		 => [
						'line_id'		 => '',
						'start_reg_date' => '',
						'end_reg_date'	 => '',
						'start_click_date' => '',
						'end_click_date'	 => '',
						'ad_code'		 => '',
						'opt'			 => ''
					],
					'checked0'		 => '',
					'checked1'		 => '',
					'checked2'		 => '',
					'checked3'		 => '',
					'checked4'		 => '',
					'checked5'		 => '',
					'checked6'		 => '',
					'after_minute'	 => '',
				];

				if( !empty($db_data->segment) ){
					$list_segment = json_decode($db_data->segment, true);
					if( isset($list_segment['segment']['line_id']) ){
						$disp_data['segment']['line_id'] = $list_segment['segment']['line_id'];
					}
					if( isset($list_segment['segment']['start_reg_date']) ){
						$disp_data['segment']['start_reg_date'] = $list_segment['segment']['start_reg_date'];
					}
					if( isset($list_segment['segment']['end_reg_date']) ){
						$disp_data['segment']['end_reg_date'] = $list_segment['segment']['end_reg_date'];
					}
					if( isset($list_segment['segment']['start_click_date']) ){
						$disp_data['segment']['start_click_date'] = $list_segment['segment']['start_click_date'];
					}
					if( isset($list_segment['segment']['end_click_date']) ){
						$disp_data['segment']['end_click_date'] = $list_segment['segment']['end_click_date'];
					}
					if( isset($list_segment['segment']['ad_code']) ){
						$disp_data['segment']['ad_code'] = $list_segment['segment']['ad_code'];
					}
					if( isset($list_segment['segment']['opt']) ){
						$disp_data['segment']['opt'] = $list_segment['segment']['opt'];
					}
				}

				if( !empty($db_data->send_week) ){
					$listWeeks = explode(",", $db_data->send_week);
					foreach($listWeeks as $week){
						$disp_data['checked'.$week] = 'checked';
					}
				}

				if( isset($db_data->send_after_minute) ){
					$disp_data['after_minute'] = $db_data->send_after_minute;
				}
				$send_date = Utility::getDayOfWeek($db_data->reserve_send_date);
				$disp_data['send_status'] = $db_data->send_status;
			}
		}

		//画面表示用配列
		$disp_data = array_merge($disp_data, [
			'search_like_type' => config('const.search_like_type'),
			'page'			=> $page,
			'edit_id'		=> $id,
			'send_date'		=> $send_date,
			'reserve_date'	=> $reserve_date,
			'db_data'		=> $db_data,
			'list_msg'		=> $list_msg,
			'channel_id'	=> $channel_id,
			'redirect_url'	=> config('const.base_admin_url').'/member/line/reserve/status/edit/'.$page.'/'.$send_type.'/'.$channel_id,
			'ver'			=> time()
		]);

		//即時配信
		if( $send_type == 0 ){
			return view('admin.push.push_edit', $disp_data);

		//予約配信
		}elseif( $send_type == 1 ){
			return view('admin.push.push_reserve_edit', $disp_data);

		//毎日配信
		}elseif( $send_type == 2 ){
			return view('admin.push.push_reserve_everyday_edit', $disp_data);

		//毎週配信
		}elseif( $send_type == 3 ){
			return view('admin.push.push_reserve_everyweek_edit', $disp_data);			

		//登録後配信
		}elseif( $send_type == 4 ){
			return view('admin.push.push_reserve_afterregist_edit', $disp_data);			

		//カルーセル
		}elseif( $send_type == 5 ){
			return view('admin.push.carousel', $disp_data);

		//２択
		}elseif( $send_type == 6 ){
			return view('admin.push.confirm', $disp_data);

		//４択
		}elseif( $send_type == 7 ){
			return view('admin.push.confirm_4choices', $disp_data);			

		//イメージマップ
		}elseif( $send_type == 8 ){
			return view('admin.push.imagemap', $disp_data);			
		}
	}

	/*
	 * メルマガ予約状況-キャンセル
	 */
	public function sendCancel($page, $send_type, $channel_id, $id){
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['melmaga_reserve_cancel'].",{$user['login_id']}");

		$now_date = Carbon::now();

		if( $send_type == 5 ){
			Line_carousel_template::where('id', $id)
					->update([
				'send_status'			=> 3,			//送信状況:3(キャンセル)
				'updated_at'			=> $now_date
			]);

		}elseif( $send_type == 6 ){
			Line_2choices_template::where('id', $id)
					->update([
				'send_status'			=> 3,			//送信状況:3(キャンセル)
				'updated_at'			=> $now_date
			]);

		}elseif( $send_type == 7 ){
			Line_4choices_template::where('id', $id)
					->update([
				'send_status'			=> 3,			//送信状況:3(キャンセル)
				'updated_at'			=> $now_date
			]);

		}elseif( $send_type == 8 ){
			Line_imagemap_log::where('id', $id)
					->update([
				'send_status'			=> 3,			//送信状況:3(キャンセル)
				'updated_at'			=> $now_date
			]);

		}else{
			LinePushLog::where('id', $id)
					->update([
				'send_status'			=> 3,			//送信状況:3(キャンセル)
				'updated_at'			=> $now_date
			]);
		}

		return null;
	}

	/*
	 * メルマガ予約状況-キャンセル
	 */
	public function sendDelete($page, $send_type, $channel_id, $id){

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['melmaga_reserve_cancel'].",{$user['login_id']}");

		$now_date = Carbon::now();

		//カルーセル
		if( $send_type == 5 ){
			Line_carousel_template::where('line_basic_id', $channel_id)->where('id', $id)->delete();

		//2択
		}elseif( $send_type == 6 ){
			Line_2choices_template::where('line_basic_id', $channel_id)->where('id', $id)->delete();
			Line_2choices_log::where('master_id', $id)->delete();

		//4択
		}elseif( $send_type == 7 ){
			Line_4choices_template::where('line_basic_id', $channel_id)->where('id', $id)->delete();
			Line_4choices_log::where('master_id', $id)->delete();

		//イメージマップ
		}elseif( $send_type == 8 ){
			Line_imagemap_log::where('line_basic_id', $channel_id)->where('id', $id)->delete();

		//
		}else{
			LinePushLog::where('line_basic_id', $channel_id)->where('id', $id)->delete();
		}

		return null;
	}

	public function convertEmoji($id = null)
	{

		$disp_data = [
			'id'		=> $id,
			'ver'		=> time(),
		];

		return view('admin.push.convert_emoji', $disp_data);
	}

	/*
	 * セグメント条件を元に配信数を取得
	 */
	public function getSegmentCount(Request $request, $channel_id)
	{
//error_log(print_r($request->all(),true)."\n",3,"/data/www/line/storage/logs/nishi_log.txt");

		$query = LineUser::query();
//		$query->select('line_click_users.user_line_id');

		//チャネルID
		$query->where("line_users.line_basic_id", $channel_id);

		//フォロー済
		$query->where("line_users.follow_flg", 1);

		//LINE ID
		if( !empty($request->input('line_id')) ){
			$query->whereIn('line_users.user_line_id', explode(",",$request->input('line_id')) );
		}

		//友だち登録開始日時
		if( !empty($request->input('start_reg_date')) ){
			$query->where('line_users.created_at', '>=', $request->input('start_reg_date'));
		}

		//友だち登録終了日時
		if( !empty($request->input('end_reg_date')) ){
			$query->where('line_users.created_at', '<=', $request->input('end_reg_date'));			
		}

		if( !empty($request->input('start_click_date')) || !empty($request->input('end_click_date')) ){
			$query->join('line_click_users', 'line_click_users.user_line_id', '=', 'line_users.user_line_id');

			//クリック開始日時
			if( !empty($request->input('start_click_date')) ){
				$query->where('line_click_users.sort_date', '>=', preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $request->input('start_click_date')).'00');
			}
			//クリック終了日時
			if( !empty($request->input('end_click_date')) ){
				$query->where('line_click_users.sort_date', '<=', preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $request->input('end_click_date')).'00');			
			}
			$query->where('line_click_users.click', '>', 0);
		}

		//広告コード
		if( !empty($request->input('ad_code')) ){
			$listSearchLikeType = config('const.search_like_type');
			$query->where('line_users.ad_cd', $listSearchLikeType[$request->input('opt')][0], sprintf($listSearchLikeType[$request->input('opt')][1], $request->input('ad_code')) );
		}

//		$db_data = LinePushLog::where();
error_log($query->distinct('line_click_users.user_line_id')->toSql()."\n",3,"/data/www/line/storage/logs/nishi_log.txt");

		return $query->distinct('line_users.user_line_id')->count('line_users.user_line_id');
	}
}
