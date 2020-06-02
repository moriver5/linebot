<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Api\LinePushMessageController;
use App\Model\LineOfficialAccount;
use App\Model\LinePushLog;
use App\Model\LineUser;
use LINE\LINEBot;
use Carbon\Carbon;
use DB;

class LineReserveBroadcastDelivery extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'line:reserve_broadcast_delivery {line_push_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '友達登録しているユーザーのLINEへメッセージ配信';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
		$db_push_data = LinePushLog::where('id', $this->argument('line_push_id'))->first();

		//現在時刻
		$now_date = Carbon::now();

		//現在時刻をyyyymmddhhmmにフォーマット
		$sort_date = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00';

		//メルマガ配信先リスト取得
		$query = LineUser::query();
		$query->select(['line_users.id','line_users.user_line_id']);
		$query->where('line_basic_id', $db_push_data->line_basic_id);
		$query->where('follow_flg', 1);
		$query->where('disable', 0);

		//セグメント条件取得
		$list_segment = json_decode($db_push_data->segment,true);
//error_log(print_r($list_segment,true)."\n",3,"/data/www/line/storage/logs/nishi_log.txt");
		//LINE ID
		if( isset($list_segment['segment']['line_id']) ){
			$query->whereIn('user_line_id', explode(",", $list_segment['segment']['line_id']));
		}

		//友だち登録開始日時
		if( isset($list_segment['segment']['start_reg_date']) ){
			$query->where('created_at', '>=', $list_segment['segment']['start_reg_date']);
		}

		//友だち登録終了日時
		if( isset($list_segment['segment']['end_reg_date']) ){
			$query->where('created_at', '<=', $list_segment['segment']['end_reg_date']);
		}

		//広告コード
		if( isset($list_segment['segment']['ad_code']) ){
			$listSearchLikeType = config('const.search_like_type');
			$query->where('ad_cd', $listSearchLikeType[$list_segment['segment']['opt']][0], sprintf($listSearchLikeType[$list_segment['segment']['opt']][1], $list_segment['segment']['ad_code']) );

		}

		$db_user_data = $query->get();
/*
		$db_user_data = LineUser::select(['line_users.id','line_users.user_line_id'])
			->where('line_basic_id', $db_push_data->line_basic_id)
			->where('follow_flg', 1)
			->where('disable', 0)
			->get();
*/
		$list_line_id = [];
		foreach($db_user_data as $lines){
			$list_line_id[] = $lines->user_line_id;

			DB::insert('insert ignore into line_messages_history_logs(line_push_id, user_line_id, sort_date, created_at, updated_at) values('
			.$this->argument('line_push_id').',"'
			.$lines->user_line_id.'",'
			.$sort_date.',"'
			.$now_date.'","'
			.$now_date.'")');
		}
/*
		if( !empty($list_user) ){
			foreach($list_user as $client_id){
				//メール配信先テーブル(melmaga_temp_immediate_mails)にメルマガ予約配信先のクライアントIDを登録
				$melmaga_mails = new Melmaga_temp_immediate_mail([
					'melmaga_id'	=> $this->argument('melmaga_id'),
					'client_id'		=> $client_id,
					'created_at'	=> $now_date,
					'updated_at'	=> $now_date
				]);

				//DB保存
				$melmaga_mails->save();

				$melmaga_history = new Melmaga_history_log([
					'melmaga_id'	=> $this->argument('melmaga_id'),
					'client_id'		=> $client_id,
					'sort_date'		=> $sort_date,
					'created_at'	=> $now_date,
					'updated_at'	=> $now_date
				]);

				//DB保存
				$melmaga_history->save();
			}
		}
*/

		if( !empty($db_user_data) ){
			//現在時刻
			$now_date = Carbon::now();

			//履歴を残す以外(send_status:4以外)
			if( $db_push_data->send_status != 4 ){
				//メルマガ配信日時 配信状況：1(送信中)
				$update = LinePushLog::where('id', $this->argument('line_push_id'))
					->update([
						'send_date' => $now_date,
						'send_status' => 1]);
			}else{
				//メルマガ配信日時
				$update = LinePushLog::where('id', $this->argument('line_push_id'))
					->update(['send_date' => $now_date]);
			}

			$line_account = LineOfficialAccount::where('line_basic_id', $db_push_data->line_basic_id)->first();

			//DBに登録チャンネルがないとき
			if( !isset($line_account->line_basic_id) ){
				return;
			}

			$bot = new LINEBot(
					new LINEBot\HTTPClient\CurlHTTPClient($line_account->line_token),
					['channelSecret' => $line_account->line_channel_secret]
				);

			$push_msg_obj = new LinePushMessageController();

			$push_image = "";

			//画像があれば
			if( !empty($db_push_data->image) ){
				$push_image = $db_push_data->image;
			}

			$list_push_msg = [];
			$list_push_msg['msg1'] = $db_push_data->msg1;

			if( !empty($db_push_data->msg2) ){
				$list_push_msg['msg2'] = $db_push_data->msg2;
			}

			if( !empty($db_push_data->msg3) ){
				$list_push_msg['msg3'] = $db_push_data->msg3;
			}

			if( !empty($db_push_data->msg4) ){
				$list_push_msg['msg4'] = $db_push_data->msg4;
			}

			if( !empty($db_push_data->msg5) ){
				$list_push_msg['msg5'] = $db_push_data->msg5;
			}

			//LINEメッセージ配信
			$push_msg_obj->sendLineOneUserPushMessage($bot, $db_push_data->line_basic_id, $this->argument('line_push_id'), $list_line_id, $list_push_msg, $push_image);

			//履歴を残す以外(send_status:4以外)
			if( $db_push_data->send_status != 4 ){
				//メルマガ配信状況の更新→配信済:send_status→2
				$update = LinePushLog::where('id', $this->argument('line_push_id'))->update(['send_status' => 2, 'send_count' => count($list_line_id)]);

			//履歴を残さない場合(send_status:4)
			}else{
				//メルマガ配信状況の更新→履歴を残さない場合の配信済:send_status→5
				$update = LinePushLog::where('id', $this->argument('line_push_id'))->update(['send_status' => 5, 'send_count' => count($list_line_id)]);				
			}
		}

    }
}
