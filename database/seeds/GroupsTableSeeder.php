<?php

use Illuminate\Database\Seeder;

class GroupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		DB::table('groups')->insert([
			'name' => '【新規登録2か月以内】',
			'memo' => '新規',
			'created_at' => date("Y/m/d h:m:s"),
			'updated_at' => date("Y/m/d h:m:s"),
		]);
		DB::table('groups')->insert([
			'name' => '【■［配信］テスト】',
			'memo' => '【［配信］テスト】',
			'created_at' => date("Y/m/d h:m:s"),
			'updated_at' => date("Y/m/d h:m:s"),
		]);
		DB::table('groups')->insert([
			'name' => '【★［注意］ブラック会員】',
			'memo' => '【［注意］ブラック会員】',
			'created_at' => date("Y/m/d h:m:s"),
			'updated_at' => date("Y/m/d h:m:s"),
		]);
		DB::table('groups')->insert([
			'name' => '【ベーシック　キャンペーン】',
			'memo' => '【ベーシック　キャンペーン】',
			'created_at' => date("Y/m/d h:m:s"),
			'updated_at' => date("Y/m/d h:m:s"),
		]);
		DB::table('groups')->insert([
			'name' => '【(火)地方ノーマル+(水)地方ノーマル+(木)地方　重賞１鞍キャンペーン】',
			'memo' => '【(火)地方ノーマル+(水)地方ノーマル+(木)地方　重賞１鞍キャンペーン】',
			'created_at' => date("Y/m/d h:m:s"),
			'updated_at' => date("Y/m/d h:m:s"),
		]);
		DB::table('groups')->insert([
			'name' => '【■［配信］入有】',
			'memo' => '【■［配信］入有】',
			'created_at' => date("Y/m/d h:m:s"),
			'updated_at' => date("Y/m/d h:m:s"),
		]);
		DB::table('groups')->insert([
			'name' => '【先週キャンペーン参加者】',
			'memo' => '補填',
			'created_at' => date("Y/m/d h:m:s"),
			'updated_at' => date("Y/m/d h:m:s"),
		]);
		DB::table('groups')->insert([
			'name' => '【★補填情報提供者】',
			'memo' => '補填',
			'created_at' => date("Y/m/d h:m:s"),
			'updated_at' => date("Y/m/d h:m:s"),
		]);
		DB::table('groups')->insert([
			'name' => '【ハイグレード　キャンペーン】',
			'memo' => '【ハイグレード　キャンペーン】',
			'created_at' => date("Y/m/d h:m:s"),
			'updated_at' => date("Y/m/d h:m:s"),
		]);
		DB::table('groups')->insert([
			'name' => '【配信不要/補填情報提供者】',
			'memo' => '補填',
			'created_at' => date("Y/m/d h:m:s"),
			'updated_at' => date("Y/m/d h:m:s"),
		]);
		DB::table('groups')->insert([
			'name' => '【アクション回数0】　',
			'memo' => '新規',
			'created_at' => date("Y/m/d h:m:s"),
			'updated_at' => date("Y/m/d h:m:s"),
		]);
		DB::table('groups')->insert([
			'name' => '【土日スタンダード　キャンペーン】',
			'memo' => '【土日スタンダード　キャンペーン】',
			'created_at' => date("Y/m/d h:m:s"),
			'updated_at' => date("Y/m/d h:m:s"),
		]);
		DB::table('groups')->insert([
			'name' => '【(水)地方　ノーマル　キャンペーン】',
			'memo' => '【(水)地方　ノーマル　キャンペーン】',
			'created_at' => date("Y/m/d h:m:s"),
			'updated_at' => date("Y/m/d h:m:s"),
		]);
		DB::table('groups')->insert([
			'name' => '【ポイント利用者　補填配信不要】',
			'memo' => '新規',
			'created_at' => date("Y/m/d h:m:s"),
			'updated_at' => date("Y/m/d h:m:s"),
		]);
		DB::table('groups')->insert([
			'name' => '【(日)単勝コロガシ　キャンペーン】',
			'memo' => '【(日)単勝コロガシ　キャンペーン】',
			'created_at' => date("Y/m/d h:m:s"),
			'updated_at' => date("Y/m/d h:m:s"),
		]);
		DB::table('groups')->insert([
			'name' => '【補填1回入金】',
			'memo' => '補填',
			'created_at' => date("Y/m/d h:m:s"),
			'updated_at' => date("Y/m/d h:m:s"),
		]);
		DB::table('groups')->insert([
			'name' => '【入有1回入金】',
			'memo' => '入有',
			'created_at' => date("Y/m/d h:m:s"),
			'updated_at' => date("Y/m/d h:m:s"),
		]);
		DB::table('groups')->insert([
			'name' => '【新規2か月より古い】',
			'memo' => '新規',
			'created_at' => date("Y/m/d h:m:s"),
			'updated_at' => date("Y/m/d h:m:s"),
		]);
		DB::table('groups')->insert([
			'name' => '【(土）単勝コロガシ　キャンペーン】',
			'memo' => '【(土）単勝コロガシ　キャンペーン】',
			'created_at' => date("Y/m/d h:m:s"),
			'updated_at' => date("Y/m/d h:m:s"),
		]);
		DB::table('groups')->insert([
			'name' => '【(木)地方ノーマル　キャンペーン】',
			'memo' => '【(木)地方ノーマル　キャンペーン】',
			'created_at' => date("Y/m/d h:m:s"),
			'updated_at' => date("Y/m/d h:m:s"),
		]);
		DB::table('groups')->insert([
			'name' => '【マスター　キャンペーン】',
			'memo' => '【マスター　キャンペーン】',
			'created_at' => date("Y/m/d h:m:s"),
			'updated_at' => date("Y/m/d h:m:s"),
		]);
		DB::table('groups')->insert([
			'name' => '【(水)地方ノーマル２+SS　キャンペーン】',
			'memo' => '【(水)地方ノーマル２+SS　キャンペーン】',
			'created_at' => date("Y/m/d h:m:s"),
			'updated_at' => date("Y/m/d h:m:s"),
		]);
		DB::table('groups')->insert([
			'name' => '未使用',
			'memo' => '未使用',
			'created_at' => date("Y/m/d h:m:s"),
			'updated_at' => date("Y/m/d h:m:s"),
		]);
		DB::table('groups')->insert([
			'name' => '未使用',
			'memo' => '未使用',
			'created_at' => date("Y/m/d h:m:s"),
			'updated_at' => date("Y/m/d h:m:s"),
		]);
		DB::table('groups')->insert([
			'name' => '【10/22(日)まで900万契約　キャンペーン】',
			'memo' => '【10/22(日)まで900万契約　キャンペーン】',
			'created_at' => date("Y/m/d h:m:s"),
			'updated_at' => date("Y/m/d h:m:s"),
		]);
		DB::table('groups')->insert([
			'name' => '未使用',
			'memo' => '未使用',
			'created_at' => date("Y/m/d h:m:s"),
			'updated_at' => date("Y/m/d h:m:s"),
		]);
		DB::table('groups')->insert([
			'name' => '未使用',
			'memo' => '未使用',
			'created_at' => date("Y/m/d h:m:s"),
			'updated_at' => date("Y/m/d h:m:s"),
		]);
		DB::table('groups')->insert([
			'name' => '【アクセス1年以内新規※無料配信メールあり】',
			'memo' => '新規',
			'created_at' => date("Y/m/d h:m:s"),
			'updated_at' => date("Y/m/d h:m:s"),
		]);
		DB::table('groups')->insert([
			'name' => '【隔離入有会員】',
			'memo' => '友達も登録してるやつ',
			'created_at' => date("Y/m/d h:m:s"),
			'updated_at' => date("Y/m/d h:m:s"),
		]);
		DB::table('groups')->insert([
			'name' => '【しばらく補填情報配信不要】',
			'memo' => '【しばらく補填情報配信不要】',
			'created_at' => date("Y/m/d h:m:s"),
			'updated_at' => date("Y/m/d h:m:s"),
		]);
		DB::table('groups')->insert([
			'name' => '【隔離新規会員】',
			'memo' => '友達も登録してるやつ',
			'created_at' => date("Y/m/d h:m:s"),
			'updated_at' => date("Y/m/d h:m:s"),
		]);
		DB::table('groups')->insert([
			'name' => '未使用',
			'memo' => '未使用',
			'created_at' => date("Y/m/d h:m:s"),
			'updated_at' => date("Y/m/d h:m:s"),
		]);
		DB::table('groups')->insert([
			'name' => '未使用',
			'memo' => '未使用',
			'created_at' => date("Y/m/d h:m:s"),
			'updated_at' => date("Y/m/d h:m:s"),
		]);
		DB::table('groups')->insert([
			'name' => '未使用',
			'memo' => '未使用',
			'created_at' => date("Y/m/d h:m:s"),
			'updated_at' => date("Y/m/d h:m:s"),
		]);
		DB::table('groups')->insert([
			'name' => '未使用',
			'memo' => '未使用',
			'created_at' => date("Y/m/d h:m:s"),
			'updated_at' => date("Y/m/d h:m:s"),
		]);
		DB::table('groups')->insert([
			'name' => '未使用',
			'memo' => '未使用',
			'created_at' => date("Y/m/d h:m:s"),
			'updated_at' => date("Y/m/d h:m:s"),
		]);
		DB::table('groups')->insert([
			'name' => '未使用',
			'memo' => '未使用',
			'created_at' => date("Y/m/d h:m:s"),
			'updated_at' => date("Y/m/d h:m:s"),
		]);
		DB::table('groups')->insert([
			'name' => '【日曜のみ補填】',
			'memo' => '補填',
			'created_at' => date("Y/m/d h:m:s"),
			'updated_at' => date("Y/m/d h:m:s"),
		]);
		DB::table('groups')->insert([
			'name' => '【土曜のみ補填】',
			'memo' => '補填',
			'created_at' => date("Y/m/d h:m:s"),
			'updated_at' => date("Y/m/d h:m:s"),
		]);
		DB::table('groups')->insert([
			'name' => '未使用( ﾟДﾟ)',
			'memo' => '未使用( ﾟДﾟ)',
			'created_at' => date("Y/m/d h:m:s"),
			'updated_at' => date("Y/m/d h:m:s"),
		]);
		DB::table('groups')->insert([
			'name' => '未使用( ﾟДﾟ)',
			'memo' => '未使用( ﾟДﾟ)',
			'created_at' => date("Y/m/d h:m:s"),
			'updated_at' => date("Y/m/d h:m:s"),
		]);
		DB::table('groups')->insert([
			'name' => '未使用（(i)）',
			'memo' => '未使用（(i)）',
			'created_at' => date("Y/m/d h:m:s"),
			'updated_at' => date("Y/m/d h:m:s"),
		]);
		DB::table('groups')->insert([
			'name' => '【しばらく不要　入有】',
			'memo' => '【しばらく不要　入有】',
			'created_at' => date("Y/m/d h:m:s"),
			'updated_at' => date("Y/m/d h:m:s"),
		]);
		DB::table('groups')->insert([
			'name' => '未使用（(i)）',
			'memo' => '未使用（(i)）',
			'created_at' => date("Y/m/d h:m:s"),
			'updated_at' => date("Y/m/d h:m:s"),
		]);
		DB::table('groups')->insert([
			'name' => '【((水))地方　SSS　キャンペーン】',
			'memo' => '【((水))地方　SSS　キャンペーン】',
			'created_at' => date("Y/m/d h:m:s"),
			'updated_at' => date("Y/m/d h:m:s"),
		]);
		DB::table('groups')->insert([
			'name' => 'にがし（(i)）',
			'memo' => 'にがし（(i)）',
			'created_at' => date("Y/m/d h:m:s"),
			'updated_at' => date("Y/m/d h:m:s"),
		]);
		DB::table('groups')->insert([
			'name' => '【隔離　補填あり】',
			'memo' => '補填',
			'created_at' => date("Y/m/d h:m:s"),
			'updated_at' => date("Y/m/d h:m:s"),
		]);
		DB::table('groups')->insert([
			'name' => '【((水))地方　SS　キャンペーン】',
			'memo' => '【((水))地方　SS　キャンペーン】',
			'created_at' => date("Y/m/d h:m:s"),
			'updated_at' => date("Y/m/d h:m:s"),
		]);
		DB::table('groups')->insert([
			'name' => '未使用',
			'memo' => '未使用',
			'created_at' => date("Y/m/d h:m:s"),
			'updated_at' => date("Y/m/d h:m:s"),
		]);
		DB::table('groups')->insert([
			'name' => '未使用( ﾟДﾟ)',
			'memo' => '未使用( ﾟДﾟ)',
			'created_at' => date("Y/m/d h:m:s"),
			'updated_at' => date("Y/m/d h:m:s"),
		]);
		DB::table('groups')->insert([
			'name' => '【先週20万以上的中】',
			'memo' => '入有',
			'created_at' => date("Y/m/d h:m:s"),
			'updated_at' => date("Y/m/d h:m:s"),
		]);
		DB::table('groups')->insert([
			'name' => '【今週不参加　新規】',
			'memo' => '【今週不参加　新規】',
			'created_at' => date("Y/m/d h:m:s"),
			'updated_at' => date("Y/m/d h:m:s"),
		]);
		DB::table('groups')->insert([
			'name' => '【見てない入有】',
			'memo' => '入有',
			'created_at' => date("Y/m/d h:m:s"),
			'updated_at' => date("Y/m/d h:m:s"),
		]);
    }
}
