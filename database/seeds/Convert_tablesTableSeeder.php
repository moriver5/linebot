<?php

use Illuminate\Database\Seeder;

class Convert_tablesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
			DB::table('convert_tables')->insert([
				'key' => '-%regist_url-',
				'value' => 'https://jra-yosou.net/registend',
				'memo' => '登録用URL',
				'created_at' => date("Y/m/d h:m:s"),
				'updated_at' => date("Y/m/d h:m:s"),
			]);

			DB::table('convert_tables')->insert([
				'key' => '-%simple_login-',
				'value' => 'https://jra-yosou.net/member/home',
				'memo' => '簡単ログインURL',
				'created_at' => date("Y/m/d h:m:s"),
				'updated_at' => date("Y/m/d h:m:s"),
			]);
			
			DB::table('convert_tables')->insert([
				'key' => '-%site_name-',
				'value' => '【JRA 競馬予想Web】',
				'memo' => 'サイト名',
				'created_at' => date("Y/m/d h:m:s"),
				'updated_at' => date("Y/m/d h:m:s"),
			]);

			DB::table('convert_tables')->insert([
				'key' => '-%info_mail-',
				'value' => 'info@jra-yosou.net',
				'memo' => 'インフォメールアドレス',
				'created_at' => date("Y/m/d h:m:s"),
				'updated_at' => date("Y/m/d h:m:s"),
			]);
			DB::table('convert_tables')->insert([
				'key' => '-%top_url-',
				'value' => 'https://jra-yosou.net/',
				'memo' => 'TOPページ',
				'created_at' => date("Y/m/d h:m:s"),
				'updated_at' => date("Y/m/d h:m:s"),
			]);
			DB::table('convert_tables')->insert([
				'key' => '-%company_name-',
				'value' => 'プレミアム運営事務局',
				'memo' => '会社名',
				'created_at' => date("Y/m/d h:m:s"),
				'updated_at' => date("Y/m/d h:m:s"),
			]);
			DB::table('convert_tables')->insert([
				'key' => '-%company_address-',
				'value' => '東京都渋谷区松濤2-15-5　秀和松濤レジデンス405号',
				'memo' => '会社住所',
				'created_at' => date("Y/m/d h:m:s"),
				'updated_at' => date("Y/m/d h:m:s"),
			]);
			DB::table('convert_tables')->insert([
				'key' => '-%sekininsya-',
				'value' => '桑原 洋一',
				'memo' => '運営責任者',
				'created_at' => date("Y/m/d h:m:s"),
				'updated_at' => date("Y/m/d h:m:s"),
			]);
			DB::table('convert_tables')->insert([
				'key' => '-%company_navidial-',
				'value' => '0570-002036',
				'memo' => 'ナビダイヤル',
				'created_at' => date("Y/m/d h:m:s"),
				'updated_at' => date("Y/m/d h:m:s"),
			]);
			DB::table('convert_tables')->insert([
				'key' => '-%mail_domain-',
				'value' => 'jra-yosou.net',
				'memo' => 'メール本文用ドメイン',
				'created_at' => date("Y/m/d h:m:s"),
				'updated_at' => date("Y/m/d h:m:s"),
			]);
			DB::table('convert_tables')->insert([
				'key' => '-%keiba_race01-',
				'value' => '産経賞オールカマー（G2） <br> 神戸新聞杯（G2）',
				'memo' => '今週の注目レース',
				'created_at' => date("Y/m/d h:m:s"),
				'updated_at' => date("Y/m/d h:m:s"),
			]);
    }
}
