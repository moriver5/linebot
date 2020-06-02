<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\User;
use Session;

class DumpInsert extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dump:insert {split_num} {dump_dir} {dump_file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ダンプデータをusersテーブルへインサートする';

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
		$split_num = $this->argument('split_num');
		$dump_dir = $this->argument('dump_dir');
		$dump_file = $this->argument('dump_file');

		require_once($dump_dir.'/'.$dump_file);

		$data_count = 0;
		foreach($user as $lines){
			$listInsertData = [];

			$data_count++;

			$listInsertData['remember_token'] = session_create_id();

			if( isset($lines['login_id']) ){
				$listInsertData['login_id'] = $lines['login_id'];
			}

			if( isset($lines['password']) ){
				$listInsertData['password'] = bcrypt($lines['password']);				
				$listInsertData['password_raw'] = $lines['password'];				
			}

			if( isset($lines['mb_mail_address']) ){
//				if( $lines['mb_mail_address'] == '' ){
//					if( $lines['pc_mail_address'] != '' ){
//						$listInsertData['mobile_mail_address'] = $lines['pc_mail_address'];						
//					}
//				}else{
					$listInsertData['mobile_mail_address'] = $lines['mb_mail_address'];				
//				}
			}

			if( isset($lines['pc_mail_address']) ){
//				if( $lines['pc_mail_address'] == '' ){
//					if( $lines['mb_mail_address'] != '' ){
//						$listInsertData['mail_address'] = $lines['mb_mail_address'];						
//					}	
//				}else{
					$listInsertData['mail_address'] = $lines['pc_mail_address'];
//				}
			}

			if( isset($lines['point']) ){
				$listInsertData['point'] = $lines['point'];
			}

			if( isset($lines['regist_status']) ){
				$listInsertData['status'] = $lines['regist_status'];
			}

			if( isset($lines['mb_mail_status']) ){
				$listInsertData['mail_status'] = $lines['mb_mail_status'];
			}

			if( isset($lines['pc_mail_status']) ){
				$listInsertData['mail_status'] = $lines['pc_mail_status'];
			}

			if( isset($lines['ad_cd']) ){
				$listInsertData['ad_cd'] = $lines['ad_cd'];
			}

			if( isset($lines['group_id']) ){
				$listInsertData['group_id'] = $lines['group_id'];
			}

			if( isset($lines['credit_certify_phone_no']) ){
				$listInsertData['credit_certify_phone_no'] = $lines['credit_certify_phone_no'];
			}

			if( isset($lines['regist_datetime']) && $lines['regist_datetime'] != '0000-00-00 00:00:00' ){
				$listInsertData['regist_date'] = preg_replace("/(\d{4})\-(\d{2})\-(\d{2})\s(\d{2}):(\d{2}):(\d{2})/", "$1$2$3$4$5$6", $lines['regist_datetime']);
			}

			if( isset($lines['pre_regist_datetime']) && $lines['pre_regist_datetime'] != '0000-00-00 00:00:00' ){
				$listInsertData['temporary_datetime'] = $lines['pre_regist_datetime'];
				$listInsertData['sort_temporary_datetime'] = preg_replace("/(\d{4})\-(\d{2})\-(\d{2})\s(\d{2}):(\d{2}):(\d{2})/", "$1$2$3$4$5$6", $lines['pre_regist_datetime']);
			}

			if( isset($lines['update_datetime']) && $lines['update_datetime'] != '0000-00-00 00:00:00' ){
				$listInsertData['updated_at'] = $lines['update_datetime'];
			}

			if( isset($lines['last_access_datetime']) && $lines['last_access_datetime'] != '0000-00-00 00:00:00' ){
				$listInsertData['last_access_datetime'] = $lines['last_access_datetime'];
				$listInsertData['sort_last_access_datetime'] = preg_replace("/(\d{4})\-(\d{2})\-(\d{2})\s(\d{2}):(\d{2}):(\d{2})/", "$1$2$3$4$5$6", $lines['last_access_datetime']);
			}
/*
			if( isset($lines['is_quit']) ){
				$listInsertData['login_id'] = $lines['is_quit'];
			}
*/
			if( isset($lines['quit_datetime']) && $lines['quit_datetime'] != '0000-00-00 00:00:00'  ){
				$listInsertData['quit_datetime'] = $lines['quit_datetime'];
				$listInsertData['sort_quit_datetime'] = preg_replace("/(\d{4})\-(\d{2})\-(\d{2})\s(\d{2}):(\d{2}):(\d{2})/", "$1$2$3$4$5$6", $lines['quit_datetime']);
			}

			if( isset($lines['description']) ){
				$listInsertData['description'] = $lines['description'];
			}

			if( isset($lines['disable']) ){
				$listInsertData['disable'] = $lines['disable'];
			}

			$user = new User($listInsertData);
			$user->save();

			if( ($data_count % 100) == 0 ){
				print $data_count."\n";
				exit;
			}
		}
		
	}
}
