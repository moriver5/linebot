<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Api\LineCarouselPushMessageController;
use App\Model\LineOfficialAccount;
use App\Model\Line_2choices_template;
use App\Model\Line_2choices_log;
use App\Model\LineUser;
use App\Http\Controllers\Api\Line2ChoicesPushMessageController;
use LINE\LINEBot;
use Carbon\Carbon;
use DB;

class LineReserve2ChoicesBroadcastDelivery extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'line:reserve_2choices_broadcast_delivery {line_push_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '友達登録しているユーザーのLINEへ2択(確認)テンプレートの配信処理を行う';

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
		$list_db_choices = Line_2choices_template::join('line_2choices_logs', 'line_2choices_logs.master_id', '=', 'line_2choices_templates.id')->where('line_2choices_templates.id', $this->argument('line_push_id'))->first();

		//現在時刻
		$now_date = Carbon::now();

		//現在時刻をyyyymmddhhmmにフォーマット
		$sort_date = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00';

		//メルマガ配信先リスト取得
		$db_user_data = LineUser::select(['line_users.id','line_users.user_line_id'])
			->where('line_basic_id', $list_db_choices->line_basic_id)
			->where('follow_flg', 1)
			->where('disable', 0)
			->get();

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

		if( !empty($db_user_data) ){
			//現在時刻
			$now_date = Carbon::now();

			//履歴を残す以外(send_status:4以外)
			//メルマガ配信日時 配信状況：1(送信中)
			$update = Line_2choices_template::where('id', $this->argument('line_push_id'))
				->update([
					'send_date' => $now_date,
					'send_status' => 1]);

			$line_account = LineOfficialAccount::where('line_basic_id', $list_db_choices->line_basic_id)->first();

			//DBに登録チャンネルがないとき
			if( !isset($line_account->line_basic_id) ){
				return;
			}

			$bot = new LINEBot(
					new LINEBot\HTTPClient\CurlHTTPClient($line_account->line_token),
					['channelSecret' => $line_account->line_channel_secret]
				);

			$choices_obj = new Line2ChoicesPushMessageController();

			//LINEメッセージ配信
			$choices_obj->sendLine2ChoicesTemplate($bot, $list_db_choices->line_basic_id, $this->argument('line_push_id'), $list_line_id, $list_db_choices);

			//履歴を残す以外(send_status:4以外)
			//メルマガ配信状況の更新→配信済:send_status→2
			$update = Line_2choices_template::where('id', $this->argument('line_push_id'))->update(['send_status' => 2, 'send_count' => count($list_line_id)]);
		}
    }
}
