<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//国内IP判別のテスト
Route::get('/checkip', 'CheckIpController@index');

Route::get('/line/{push_id}/{short_url}/{type?}', 'RedirectController@index');

/*
 *	LINE Bot
 */
//友だち登録URLのPV
Route::post('php/line/track/adcode/pv', 'AdAggregateController@index');

//ユーザーのLINEからイベント発生時の受け入れWeb hook
Route::post('/php/line/hook/{basic_id}', 'Api\LineHookController@callback');

//QRコードなどからLINEの友達登録するタイミングでアクセス
Route::post('/php/line/track/{basic_id}/{ad_cd?}', 'Api\LineTrackAccessController@insertTrackAccess');

Route::get('/php/line/imagemap/{basic_id}/{image_id}/{img_width}', 'Api\LineImageController@showImageMapImage');
Route::post('/php/line/imagemap/{basic_id}/{image_id}/{img_width}', 'Api\LineImageController@showImageMapImage');

Route::get('/php/line/imglink/{basic_id}/{image_id}/{img_width}', 'Api\LineImageController@showLinkImage');
Route::post('/php/line/imglink/{basic_id}/{image_id}/{img_width}', 'Api\LineImageController@showLinkImage');

//ユーザーのLINEへ友達登録したタイミングで送信する画像のURL
Route::get('/php/line/img/original/{basic_id}/{image_id}/{line_id}', 'Api\LineImageController@showFollowImage');
Route::get('/php/line/img/preview/{basic_id}/{image_id}/{line_id}', 'Api\LineImageController@showFollowImage');

Route::get('/php/line/push/img/original/{basic_id}/{msg}/{line_push_id}', 'Api\LineImageController@showPushImage');
Route::get('/php/line/push/img/preview/{basic_id}/{msg}/{line_push_id}', 'Api\LineImageController@showPushImage');
Route::get('/php/line/push/img/carousel/{basic_id}/{msg}/{line_push_id}', 'Api\LineImageController@showCarouselPushImage');
Route::get('/php/line/push/img/button/{basic_id}/{msg}/{line_push_id}', 'Api\LineImageController@showButtonPushImage');
Route::get('/php/line/push/img/postback/original/{basic_id}/{msg}/{postback_id}', 'Api\LineImageController@showPostbackPushImage');
Route::get('/php/line/push/img/postback/preview/{basic_id}/{msg}/{postback_id}', 'Api\LineImageController@showPostbackPushImage');

//ユーザーのLINEへメッセージ送信
//Route::get('/php/line/push/send/{basic_id}/{line_id}', 'Api\LinePushMessageController@sendPushMessage');


/*******************
 * 
	代理店管理画面
 * 
 *******************/

//代理店管理画面-ログイン前(http://ドメイン/agency 以降でアクセスがあったら)
Route::group(['prefix' => 'agency'], function() {
	//代理店管理ログイン前-ログイン画面
	Route::get('/', 'Agency\Auth\LoginController@showLoginForm');
	Route::get('login/{agency_login_id?}', 'Agency\Auth\LoginController@showLoginForm');
	Route::post('login', 'Agency\Auth\LoginController@login');

	//代理店管理画面-ログアウト
	Route::post('logout', 'Agency\Auth\LoginController@logout');
	Route::get('logout', 'Agency\Auth\LoginController@logout');

	//代理店管理画面-ログイン後の画面(http://ドメイン/agency/member 以降でアクセスがあったら)
	Route::group(['middleware' => 'auth.agency.token'], function() {
		//管理トップ
		Route::get('member', 'Agency\AgencyController@index');
		Route::get('member/home/{sid?}', 'Agency\AgencyController@index');

		//集計-期間
		Route::post('member/aggregate', 'Agency\AgencyController@searchPost');
		Route::get('member/aggregate', 'Agency\AgencyController@search');

		//集計-月別
		Route::post('member/aggregate/month/{channel_id}/{ad_cd}', 'Agency\AgencyController@aggregateMonth');
		Route::get('member/aggregate/month/{channel_id}/{ad_cd}', 'Agency\AgencyController@aggregateMonth');

	});

});

/*******************
 * 
	管理画面
 * 
 *******************/

//管理画面-ログイン前(http://ドメイン/admin 以降でアクセスがあったら)
//Route::group(['prefix' => 'admin', 'middleware' => ['check.allow.ip']], function() {
Route::group(['prefix' => 'admin'], function() {
	//管理ログイン前-アカウント新規作成
	Route::get('regist', 'Admin\Auth\LoginController@register');
	Route::post('regist/send', 'Admin\Auth\RegisterController@create');

	//管理ログイン前-パスワード設定(アカウント未登録用)
	Route::get('password/setting/{sid}', 'Admin\Auth\RegisterController@passwordSetting');
	Route::post('password/setting/send', 'Admin\Auth\RegisterController@passwordSettingSend');

	//管理ログイン前-パスワード再設定(アカウント登録済用)
	Route::get('password/resetting/{sid}', 'Admin\Auth\RegisterController@passwordReSetting');
	Route::post('password/resetting/send', 'Admin\Auth\RegisterController@passwordReSettingSend');

	//管理ログイン前-ログインID・パスワード忘れ
	Route::get('forget', 'Admin\Auth\LoginController@forget');
	Route::post('forget', 'Admin\Auth\LoginController@forgetSend');

	//管理ログイン前-ログイン画面
//	Route::get('/', 'Admin\Auth\LoginController@showLoginForm');
	Route::get('/', function(){
		return redirect('/admin/login');
	});
	Route::get('login', 'Admin\Auth\LoginController@showLoginForm');
	Route::post('login', 'Admin\Auth\LoginController@login');

	//管理画面-ログアウト
	Route::post('logout', 'Admin\Auth\LoginController@logout');
	Route::get('logout', 'Admin\Auth\LoginController@logout');
 	
	//管理画面-ログイン後の画面(http://ドメイン/admin/member 以降でアクセスがあったら)
	Route::group(['middleware' => 'auth.admin.token'], function() {
		//管理トップ
		Route::get('member', 'Admin\AdminMemberController@index');
		Route::get('member/home/{sid?}', 'Admin\AdminMemberController@index');

		//アカウント新規作成関連
		Route::get('member/create/{page?}', 'Admin\AdminMemberController@create')->middleware('check.line.lole');
		Route::post('member/create/send', 'Admin\AdminMemberController@createSend')->middleware('check.line.lole');

		//アカウント編集関連
		Route::get('member/edit/{page}/{id}', 'Admin\AdminMemberController@edit');
		Route::post('member/edit/send', 'Admin\AdminMemberController@store');

		//クライアント一覧
//		Route::get('member/client', 'Admin\AdminClientController@index');
		Route::get('member/client', 'Admin\AdminLineClientController@index');

		//クライアント-一括削除
		Route::post('member/client/del/send', 'Admin\AdminClientController@bulkDeleteSend');

		//クライアント検索
		Route::get('member/client/search', 'Admin\AdminClientController@search');
		Route::post('member/client/search', 'Admin\AdminClientController@searchPost');

		//クライアントインポート
		Route::get('member/client/import', 'Admin\AdminClientController@importClientData');
		Route::post('member/client/import/upload', 'Admin\AdminClientController@importClientUpload');

		//クライアントインポート-不正メールアドレスリストのダウンロード
		Route::get('member/client/import/dl/bad_email', 'Admin\AdminClientController@downLoadBadEmail');

		//クライアントインポート-不明ドメインリストのダウンロード
		Route::get('member/client/import/dl/unknown_mx_domain', 'Admin\AdminClientController@downLoadUnknownMxDomain');

		//クライアントインポート-重複メールアドレスリストのダウンロード
		Route::get('member/client/import/dl/duplicate_email', 'Admin\AdminClientController@downLoadDuplicateEmail');

		//クライアントインポート-不正メールアドレスリストファイルの削除
		Route::get('member/client/import/del/bad_email', 'Admin\AdminClientController@deleteBadEmail');

		//クライアントインポート-不明ドメインリストファイルの削除
		Route::get('member/client/import/del/unknown_mx_domain', 'Admin\AdminClientController@deleteUnknownMxDomain');

		//クライアントインポート-重複メールアドレスリストファイルの削除
		Route::get('member/client/import/del/duplicate_email', 'Admin\AdminClientController@deleteDuplicateEmail');

		//クライアント検索エクスポート
		Route::post('member/client/search/export', 'Admin\AdminClientController@clientExport');

		//クライアントエクスポートの操作ログ
		Route::get('member/client/export/opeartion/log', 'Admin\AdminClientController@clientExportOperationLog');
		Route::post('member/client/export/opeartion/log', 'Admin\AdminClientController@clientExportOperationLog');

		//クライアント編集画面-メルマガ履歴画面表示
		Route::get('member/client/edit/{id}/melmaga/history', 'Admin\AdminClientController@historyMelmaga');

		//クライアント編集画面-アクセス履歴
		Route::get('member/client/edit/{id}/access/history', 'Admin\AdminClientController@accessHistory');

		//クライアント個別リスト画面
		Route::get('member/client/list/{page}/{channel_id}/{line_id}', 'Admin\AdminLineClientController@edit');

		//クライアント個別リスト画面-更新
		Route::post('member/client/list/{page}/{client_id}/send', 'Admin\AdminClientController@updateUserSend');

		//クライアント編集画面
		Route::get('member/client/edit/{page}/{client_id}/{group_id}', 'Admin\AdminClientController@edit');

		//クライアント編集画面-更新処理
		Route::post('member/client/edit/send', 'Admin\AdminLineClientController@store');

		//クライアント検索設定
		Route::get('member/client/search/setting', 'Admin\AdminClientController@searchSetting');

		//グループ管理
		Route::get('member/line/channel/setting/{line_basic_id?}', 'Admin\AdminMasterLineChannelController@index');

		//グループ管理-更新
		Route::post('member/line/channel/setting/list/update/send', 'Admin\AdminMasterLineChannelController@bulkUpdate');

		//
		Route::get('member/line/channel/detail/{channel_id}', 'Admin\AdminMasterLineChannelController@channelDetail')->middleware('check.allow.line_channel');

		//チャンネル設定-チャンネル一覧
		Route::get('member/line/channel/list/{type?}', 'Admin\AdminMasterLineChannelController@channelList');

		//チャンネル設定-チャンネル一覧-チャンネル設定orメッセージ配信
		Route::get('member/line/channel/detail/{channel_id}/{type}', 'Admin\AdminMasterLineChannelController@channelDetail')->middleware('check.allow.line_channel');

		//LINEチャンネル-Step1追加画面表示
		Route::get('member/line/channel/add/step1', 'Admin\AdminMasterLineChannelController@createStep1')->middleware('check.line.lole');

		//LINEチャンネル--Step1追加処理
		Route::post('member/line/channel/add/send', 'Admin\AdminMasterLineChannelController@createSend')->middleware('check.line.lole');

		//LINEチャンネル-Step2追加画面表示
		Route::get('member/line/channel/add/step2/{channel_id}', 'Admin\AdminMasterLineChannelController@createStep2')->middleware('check.line.lole');

		//LINEチャンネル-Step3追加画面表示
		Route::get('member/line/channel/add/step3/{channel_id}', 'Admin\AdminMasterLineChannelController@createStep3')->middleware('check.line.lole');

		//LINEチャンネル-Step4追加画面表示
		Route::get('member/line/channel/add/step4/{channel_id}', 'Admin\AdminMasterLineChannelController@createStep4')->middleware('check.line.lole');

		Route::get('member/line/channel/get', 'Admin\AdminLineDveloperScrapingController@getLineChannel');

		//チャンネル編集
		Route::get('member/line/channel/edit/{channel_id}', 'Admin\AdminMasterLineChannelController@edit')->middleware('check.allow.line_channel');
	
		//チャンネル編集処理
		Route::post('member/line/channel/edit/{channel_id}/send', 'Admin\AdminMasterLineChannelController@store')->middleware('check.allow.line_channel');

		//グループ管理-一括移行
		Route::get('member/group/move/bulk', 'Admin\AdminMasterGroupController@bulkMoveGroup');

		//グループ管理-一括移行-更新
		Route::post('member/group/move/bulk/send', 'Admin\AdminMasterGroupController@bulkMoveGroupSend');

		//グループ管理-自動メール文設定
		Route::get('member/line/setting/add/{channel_id}/{id?}', 'Admin\AdminMasterLineContentController@addSetting')->middleware('check.allow.line_channel');
		Route::get('member/line/setting/redirect/{channel_id}/{id?}', 'Admin\AdminMasterLineContentController@index')->middleware('check.allow.line_channel');
		Route::get('member/line/setting/replay/{channel_id}/{id?}', 'Admin\AdminMasterLineContentController@index')->middleware('check.allow.line_channel');

		//ポストバック設定
		Route::get('member/line/setting/postback/create/{channel_id}/{id?}', 'Admin\AdminLinePostbackController@create')->middleware('check.allow.line_channel');
		Route::post('member/line/setting/postback/create/{channel_id}/save/send', 'Admin\AdminLinePostbackController@saveLinePostbackData')->middleware('check.allow.line_channel');
		Route::post('member/line/setting/postback/{channel_id}/delete', 'Admin\AdminLinePostbackController@deletePostback')->middleware('check.allow.line_channel');
		Route::post('member/line/setting/postback/create/img/{channel_id}/upload', 'Admin\AdminLinePostbackController@uploadPostbackImgUpload')->middleware('check.allow.line_channel');
		Route::get('member/line/setting/postback/{channel_id}/{id?}', 'Admin\AdminLinePostbackController@index')->middleware('check.allow.line_channel');

		//グループ管理-自動メール文更新
		Route::post('member/line/setting/msg/save/{channel_id}/send', 'Admin\AdminMasterLineContentController@store')->middleware('check.allow.line_channel');
		Route::post('member/line/setting/msg/save/{channel_id}', 'Admin\AdminMasterLineContentController@saveFollowMsg')->middleware('check.allow.line_channel');

		//自動応答設定-画像アップロード
		Route::post('member/line/setting/img/upload/{channel_id}', 'Admin\AdminMasterLineContentController@uploadFollowImgUpload')->middleware('check.allow.line_channel');
		Route::post('member/line/setting/imgmap/upload/{channel_id}', 'Admin\AdminMasterLineContentController@uploadFollowImgMapUpload')->middleware('check.allow.line_channel');
		Route::post('member/line/setting/img/delete/{channel_id}/{id}/{type}', 'Admin\AdminMasterLineContentController@deleteFollowImg')->middleware('check.allow.line_channel');
		Route::post('member/line/setting/msg/delete/{channel_id}/{id}/{type}', 'Admin\AdminMasterLineContentController@deleteFollowMsg')->middleware('check.allow.line_channel');

		//２択-トップ
		Route::get('member/line/setting/2choices/{channel_id}/{id?}', 'Admin\AdminLineConfirmController@index')->middleware('check.allow.line_channel');

		//２択-設定更新
		Route::post('member/line/setting/2choices/save/{channel_id}/send', 'Admin\AdminLineConfirmController@save2ConfirmSetting')->middleware('check.allow.line_channel');

		//４択-トップ
		Route::get('member/line/setting/4choices/{channel_id}/{id?}', 'Admin\AdminLineConfirmController@index_4choices')->middleware('check.allow.line_channel');

		//４択-画像アップロード
		Route::post('member/line/setting/4choices/img/{channel_id}/upload', 'Admin\AdminLineConfirmController@uploadButtonImgUpload')->middleware('check.allow.line_channel');

		//４択-画像削除
		Route::post('member/line/setting/4choices/img/{channel_id}/delete', 'Admin\AdminLineConfirmController@deleteButtonImgUpload')->middleware('check.allow.line_channel');

		//４択-設定更新
		Route::post('member/line/setting/4choices/save/{channel_id}/send', 'Admin\AdminLineConfirmController@save4ConfirmSetting')->middleware('check.allow.line_channel');

		//カルーセル-トップ
		Route::get('member/line/setting/carousel/{channel_id}/{id?}', 'Admin\AdminLineCarouselController@index')->middleware('check.allow.line_channel');

		//カルーセル-設定更新
		Route::post('member/line/setting/carousel/save/{channel_id}/send', 'Admin\AdminLineCarouselController@saveCarouselSetting')->middleware('check.allow.line_channel');

		//カルーセル-画像アップロード
		Route::post('member/line/setting/carousel/img/{channel_id}/upload', 'Admin\AdminLineCarouselController@uploadCarouselImgUpload')->middleware('check.allow.line_channel');

		//イメージマップ-トップ
		Route::get('member/line/setting/imagemap/{channel_id}/{id?}', 'Admin\AdminLineImageMapController@index')->middleware('check.allow.line_channel');

		//イメージマップ-画像アップロード		
		Route::post('member/line/setting/imagemap/img/{channel_id}/upload', 'Admin\AdminLineImageMapController@uploadImageMapImgUpload')->middleware('check.allow.line_channel');

		//イメージマップ-画像削除	
		Route::post('member/line/setting/imagemap/img/{channel_id}/delete', 'Admin\AdminLineImageMapController@deleteImageMapImgUpload')->middleware('check.allow.line_channel');

		//イメージマップ-設定更新
		Route::post('member/line/setting/imagemap/save/{channel_id}/send', 'Admin\AdminLineImageMapController@saveImageMapSetting')->middleware('check.allow.line_channel');

		//グループ管理-%変換設定
		Route::get('member/group/convert/setting/{group_id}', 'Admin\AdminMasterConvertController@index');

		//グループ管理-%変換設定-更新処理
		Route::post('member/group/convert/setting/send', 'Admin\AdminMasterConvertController@store');

		//グループ管理-%変換設定-キー追加画面表示
		Route::get('member/group/convert/setting/add/{group_id}', 'Admin\AdminMasterConvertController@create');

		//グループ管理-%変換設定-キー追加処理
		Route::post('member/group/convert/setting/add/send', 'Admin\AdminMasterConvertController@createSend');

		//LINEチャンネル詳細-IDごとの登録者表示
		Route::get('member/line/channel/friends/{channel_id}', 'Admin\AdminLineClientController@channelUserList')->middleware('check.allow.line_channel');

		//グループ管理-グループ内検索-IDごとの登録者表示-カテゴリ一括移行
		Route::post('member/group/search/{group_id}/category/bulk/move/send', 'Admin\AdminMasterGroupController@moveBulkCategorySend');

		//グループ管理-グループ内検索-IDごとの登録者表示-ユーザーごとのカテゴリ移行
		Route::post('member/group/search/{group_id}/category/move/send', 'Admin\AdminMasterGroupController@moveCategorySend');

		//グループ管理-グループ内検索-カテゴリ追加画面表示
		Route::get('member/group/search/category/add/{id}', 'Admin\AdminMasterGroupController@createCategory');

		//グループ管理-グループ内検索-カテゴリ追加画面表示-追加処理
		Route::post('member/group/search/category/add/{id}/send', 'Admin\AdminMasterGroupController@createCategorySend');

		//ランディングページ-ドメイン一覧
		Route::get('member/lp', 'Admin\AdminLandingPageController@index');

		//ランディングページ-LP編集(デフォルトのLP一覧)
		Route::get('member/lp/list/{lpid}', 'Admin\AdminLandingPageController@listLandingPage');

		//ランディングページ-LP一覧(ページ一覧)
		Route::get('member/lp/list/{lpid}/subpage', 'Admin\AdminLandingPageController@listSubLandingPage');

		//ランディングページ-LP一覧(ページ一覧)-更新処理
		Route::post('member/lp/list/{lpid}/subpage/update/send', 'Admin\AdminLandingPageController@updatelistSubLandingPage');

		//ランディングページ-LP一覧(ページ一覧)-ページ追加
		Route::post('member/lp/list/{lpid}/subpage/add/send', 'Admin\AdminLandingPageController@addSubLandingPageSend');

		//ランディングページ-LP一覧(ページ編集)-ページ追加
		Route::post('member/lp/list/{lpid}/subpage/{page_name}/add/send', 'Admin\AdminLandingPageController@addFileSubLandingPageSend');

		//ランディングページ-LP一覧(ページ一覧)-参照-画像
		Route::get('member/lp/list/subpage/content/img/{lpid}/{page_name}', 'Admin\AdminLandingPageController@uploadSubLandingPageImg');

		//ランディングページ-LP一覧(ページ一覧)-参照-画像アップロード処理
		Route::post('member/lp/list/subpage/content/img/{lpid}/{page_name}/upload', 'Admin\AdminLandingPageController@uploadSubLandingPageImgUpload');

		//ランディングページ-LP一覧-参照-画像削除
		Route::post('member/lp/list/subpage/content/img/{lpid}/{page_name}/delete', 'Admin\AdminLandingPageController@deleteSubLandingPageImg');

		//ランディングページ-LP一覧(ページ一覧)-参照
		Route::get('member/lp/list/{lpid}/subpage/content/{page_name?}/{name?}', 'Admin\AdminLandingPageController@createSubLandingPage');

		//ランディングページ-LP一覧(ページ一覧)-参照-更新処理
		Route::post('member/lp/list/{lpid}/subpage/content/{page_name}/{name}/send', 'Admin\AdminLandingPageController@updateSubLandingPageSend');

		//ランディングページ編集-プレビュー表示
		Route::post('member/lp/subpage/{lpid}/{page_name}/{name}/preview', 'Admin\AdminLandingPageController@previewSubLandingPageSend');
		Route::get('member/lp/subpage/{lpid}/{page_name}/{name}/preview', 'Admin\AdminLandingPageController@previewSubLandingPage');

		//ランディングページ編集-プレビュー表示
		Route::post('member/lp/create/content/{id}/{name}/preview', 'Admin\AdminLandingPageController@previewLandingPageSend');
		Route::get('member/lp/create/content/{id}/{name}/preview', 'Admin\AdminLandingPageController@previewLandingPage');

		//ランディングページ-LP一覧-参照
		Route::get('member/lp/create/content/{id}/{name?}', 'Admin\AdminLandingPageController@createLandingPage');

		//ランディングページ-LP一覧-参照-更新処理
		Route::post('member/lp/create/content/{id}/{name}/send', 'Admin\AdminLandingPageController@updateLandingPageSend');

		//ランディングページ-LP一覧-参照-画像
		Route::get('member/lp/create/img/{id}', 'Admin\AdminLandingPageController@uploadLandingPageImg');

		//ランディングページ-LP一覧-参照-画像アップロード処理
		Route::post('member/lp/create/img/{id}/upload', 'Admin\AdminLandingPageController@uploadLandingPageImgUpload');

		//ランディングページ-LP一覧-参照-画像削除
		Route::post('member/lp/create/img/{id}/delete', 'Admin\AdminLandingPageController@deleteLandingPageImg');

		//ランディングページ-LP一覧-検索設定画面表示
		Route::get('member/lp/search/setting', 'Admin\AdminLandingPageController@searchSetting');

		//ランディングページ-LP一覧-検索
		Route::get('member/lp/search', 'Admin\AdminLandingPageController@search');
		Route::post('member/lp/search', 'Admin\AdminLandingPageController@searchPost');

		//ランディングページ-LP一覧-新規作成
		Route::get('member/lp/create', 'Admin\AdminLandingPageController@create');
		Route::post('member/lp/create/send', 'Admin\AdminLandingPageController@createSend');

		//ランディングページ-LP一覧-LP編集-個別ページ追加
		Route::post('member/lp/create/content/{id}/add/page/send', 'Admin\AdminLandingPageController@addLandingPageSend');

		//ランディングページ-LP一覧-編集
		Route::get('member/lp/edit/{page}/{id}', 'Admin\AdminLandingPageController@edit');

		//ランディングページ-LP一覧-編集処理
		Route::post('member/lp/edit/send', 'Admin\AdminLandingPageController@store');

		//メルマガ-即時配信メルマガ-トップ
		Route::get('member/line/push/message/{channel_id}/{id?}', 'Admin\AdminLinePushMessageController@index')->middleware('check.allow.line_channel');

		//メッセージ配信用の画像アップロード
		Route::post('member/line/push/message/img/{channel_id}/upload', 'Admin\AdminLinePushMessageController@uploadPushMessageImgUpload')->middleware('check.allow.line_channel');

		//メッセージ配信用のリンク画像アップロード
		Route::post('member/line/push/message/imgmap/{channel_id}/upload', 'Admin\AdminLinePushMessageController@uploadPushMessageImgMapUpload')->middleware('check.allow.line_channel');

		//メッセージ配信用のメッセージ削除
		Route::post('member/line/push/message/{channel_id}/delete', 'Admin\AdminLinePushMessageController@deletePushMessage')->middleware('check.allow.line_channel');

		//メルマガ-検索設定画面表示
		Route::get('member/line/push/message/user/search/setting', 'Admin\AdminLinePushMessageController@searchSetting');

		//メルマガ-検索
		Route::get('member/line/push/message/user/search', 'Admin\AdminLinePushMessageController@search');
		Route::post('member/line/push/message/user/search', 'Admin\AdminLinePushMessageController@searchPost');

		Route::post('member/line/push/message/save/send', 'Admin\AdminLinePushMessageController@saveLinePushMessage');

		//メルマガ-検索-メルマガ即時配信
		Route::post('member/line/push/message/send', 'Admin\AdminLinePushMessageController@sendImmediateLinePushMessage');

		//メルマガ-メルマガ配信履歴
		Route::get('member/line/history/push/message/{channel_id}', 'Admin\AdminLinePushMessageController@historySendLinePushMessages')->middleware('check.allow.line_channel');

		//集計-メルマガ配信履歴-配信リスト
		Route::get('member/line/history/list/{channel_id}/{push_id}', 'Admin\AdminLinePushMessageController@listHistoryUsers')->middleware('check.allow.line_channel');

		//メルマガ-メルマガ配信履歴-配信メルマガ確認
		Route::get('member/line/history/push/message/view/{page}/{send_type}/{channel_id}/{push_id}', 'Admin\AdminLinePushMessageController@viewHistorySendPushMessage')->middleware('check.allow.line_channel');

		//メルマガ-送信失敗一覧
		Route::get('member/melmaga/mail/failed/list', 'Admin\AdminMelmagaController@failedSendMelmaga');

		//メルマガ-送信失敗一覧-再配信
		Route::post('member/melmaga/mail/failed/list/redelivery', 'Admin\AdminMelmagaController@sendFailedMelmaga');

		//メルマガ-送信失敗一覧-メルマガ送信失敗リスト
		Route::get('member/melmaga/mail/failed/list/emails/{page}/{melmaga_id}', 'Admin\AdminMelmagaController@listFailedSendMelmaga');

		//メルマガ-送信失敗一覧-一括削除
		Route::post('member/melmaga/mail/failed/list/del', 'Admin\AdminMelmagaController@bulkDeleteSend');

		//セグメント条件で配信数取得
		Route::post('member/line/{channel_id}/segment/count', 'Admin\AdminLineReservePushMessageController@getSegmentCount')->middleware('check.allow.line_channel');

		//メルマガ-予約配信メルマガ-トップ
		Route::get('member/line/reserve/push/message/{send_type}/{channel_id}/{id?}', 'Admin\AdminLineReservePushMessageController@index')->middleware('check.allow.line_channel');

		//メルマガ-検索設定画面表示
		Route::get('member/melmaga/reserve/search/setting', 'Admin\AdminMelmagaReserveController@searchSetting');

		//メルマガ-予約配信メルマガ-検索
		Route::get('member/melmaga/reserve/search', 'Admin\AdminMelmagaReserveController@search');
		Route::post('member/melmaga/reserve/search', 'Admin\AdminMelmagaReserveController@searchPost');

		//メルマガ-検索-予約配信メルマガ-メルマガ予約配信
		Route::post('member/line/push/message/reserve/send', 'Admin\AdminLineReservePushMessageController@sendReserveLinePushMessage');

		//メルマガ-予約配信メルマガ-予約状況
		Route::get('member/line/reserve/status/{send_type}/{channel_id}', 'Admin\AdminLineReservePushMessageController@statusReserveLinePushMessages')->middleware('check.allow.line_channel');

		//メルマガ-予約配信メルマガ-予約状況-メルマガ編集
		Route::get('member/line/reserve/status/edit/{page}/{send_type}/{channel_id}/{id}', 'Admin\AdminLineReservePushMessageController@editReserveLinePushMessages')->middleware('check.allow.line_channel');

		//メルマガ-予約配信メルマガ-予約状況-キャンセル
		Route::post('member/line/reserve/status/cancel/{page}/{send_type}/{channel_id}/{id}', 'Admin\AdminLineReservePushMessageController@sendCancel')->middleware('check.allow.line_channel');
		Route::post('member/line/reserve/status/delete/{page}/{send_type}/{channel_id}/{id}', 'Admin\AdminLineReservePushMessageController@sendDelete')->middleware('check.allow.line_channel');

		//メルマガ-登録後送信メール
		Route::get('member/melmaga/registered/mail', 'Admin\AdminMelmagaRegisteredMailController@index');

		//メルマガ-登録後送信メール-一括削除
		Route::post('member/melmaga/registered/mail/delete/send', 'Admin\AdminMelmagaRegisteredMailController@bulkUpdate');

		//メルマガ-登録後送信メール-新規作成
		Route::get('member/melmaga/registered/mail/create', 'Admin\AdminMelmagaRegisteredMailController@create');

		//メルマガ-登録後送信メール-新規作成-作成処理
		Route::post('member/melmaga/registered/mail/create/send', 'Admin\AdminMelmagaRegisteredMailController@createSend');

		//メルマガ-登録後送信メール-検索設定
		Route::get('member/melmaga/registered/mail/search/setting', 'Admin\AdminMelmagaRegisteredMailController@searchSetting');

		//メルマガ-登録後送信メール-検索設定-検索処理
		Route::get('member/melmaga/registered/mail/search', 'Admin\AdminMelmagaRegisteredMailController@search');
		Route::post('member/melmaga/registered/mail/search', 'Admin\AdminMelmagaRegisteredMailController@searchPost');

		//メルマガ-登録後送信メール-編集画面表示
		Route::get('member/melmaga/registered/mail/edit/{page}/{id}', 'Admin\AdminMelmagaRegisteredMailController@edit');

		//メルマガ-登録後送信メール-編集画面表示-編集処理
		Route::post('member/melmaga/registered/mail/edit/send', 'Admin\AdminMelmagaRegisteredMailController@store');

		//LINE-絵文字表示(HTML)
		Route::get('member/line/emoji/convert/list', 'Admin\AdminLineReservePushMessageController@convertEmoji');

		//LINE-LINE独自絵文字表示(HTML)
		Route::get('member/line/emoji/convert/list', 'Admin\AdminLineReservePushMessageController@convertEmoji');

		//マスタ管理-絵文字変換表示
		Route::get('member/master/emoji/convert/{id}', 'Admin\AdminEmojiController@convert');

		//マスタ管理-タグ設定
		Route::get('member/master/tags/setting', 'Admin\AdminMasterTagsSettingController@index');

		//マスタ管理-タグ設定-追加
		Route::get('member/master/tags/setting/add', 'Admin\AdminMasterTagsSettingController@create');

		//マスタ管理-タグ設定-追加処理
		Route::post('member/master/tags/setting/add/send', 'Admin\AdminMasterTagsSettingController@createSend');

		//マスタ管理-タグ設定-一括更新処理
		Route::post('member/master/tags/setting/update/send', 'Admin\AdminMasterTagsSettingController@updateSend');

		//マスタ管理-タグ設定-編集画面
		Route::get('member/master/tags/setting/edit/{id}', 'Admin\AdminMasterTagsSettingController@edit');

		//マスタ管理-タグ設定-編集画面-更新処理
		Route::post('member/master/tags/setting/edit/{id}/send', 'Admin\AdminMasterTagsSettingController@store');

		//マスタ管理-メールアドレス禁止ワード設定
		Route::get('member/master/mailaddress_ng_word/setting', 'Admin\AdminMasterMailAddressNgWordController@index');

		//マスタ管理-メールアドレス禁止ワード設定-更新処理
		Route::post('member/master/mailaddress_ng_word/setting/send', 'Admin\AdminMasterMailAddressNgWordController@store');

		//マスタ管理-ドメイン設定
		Route::get('member/master/domain/setting', 'Admin\AdminMasterDomainController@index');

		//マスタ管理-ドメイン設定-更新
		Route::post('member/master/domain/setting/send', 'Admin\AdminMasterDomainController@store');

		//マスタ管理-ドメイン設定-追加
		Route::get('member/master/domain/setting/add', 'Admin\AdminMasterDomainController@create');

		//マスタ管理-ドメイン設定-追加処理
		Route::post('member/master/domain/setting/add/send', 'Admin\AdminMasterDomainController@createSend');

		//マスタ管理-リレーサーバー設定
		Route::get('member/master/relayserver/setting', 'Admin\AdminMasterRelayServerController@index');

		//マスタ管理-リレーサーバー設定-更新処理
		Route::post('member/master/relayserver/setting/send', 'Admin\AdminMasterRelayServerController@store');

		//マスタ管理-自動メール文設定-変換表表示
		Route::get('member/master/mail_sentence/setting/convert/{id}', 'Admin\AdminMasterMailContentController@convert');

		//マスタ管理-確認アドレス設定
		Route::get('member/master/confirm/email/setting', 'Admin\AdminMasterConfirmEmailSettingController@index');

		//マスタ管理-確認アドレス設定-アドレス追加画面
		Route::get('member/master/confirm/email/setting/add', 'Admin\AdminMasterConfirmEmailSettingController@create');

		//マスタ管理-確認アドレス設定-アドレス追加-追加処理
		Route::post('member/master/confirm/email/setting/add/send', 'Admin\AdminMasterConfirmEmailSettingController@createSend');

		//マスタ管理-確認アドレス設定-更新処理
		Route::post('member/master/confirm/email/setting/del/send', 'Admin\AdminMasterConfirmEmailSettingController@updateSend');

		//マスタ管理-メンテナンス設定
		Route::get('member/maintenance/setting', 'Admin\AdminMasterMaintenanceController@index');

		//マスタ管理-メンテナンス設定処理
		Route::post('member/maintenance/setting/send', 'Admin\AdminMasterMaintenanceController@createSend');

		//マスタ管理-メンテナンス設定-メンテナンス画面プレビュー
		Route::get('member/maintenance/setting/preview', 'Admin\AdminMasterMaintenanceController@preview');

		//集計-友だち追加数
		Route::get('member/line/analytics/friends/{channel_id}/{string?}', 'Admin\AdminLineFriendsAnalyticsController@index');
		Route::post('member/line/analytics/friends/{channel_id}/{string?}', 'Admin\AdminLineFriendsAnalyticsController@index');

		//集計-アクセス解析-年
		Route::get('member/analytics/access/{year?}', 'Admin\AdminAnalyticsController@index');

		//集計-アクセス解析-月
		Route::get('member/analytics/access/{year}/{month}', 'Admin\AdminAnalyticsController@monthAnalysis');

		//集計-アクセス解析-日
		Route::get('member/analytics/access/{year}/{month}/{day}', 'Admin\AdminAnalyticsController@dayAnalysis');

		//集計-メルマガ解析-トップ
		Route::get('member/analytics/melmaga/access', 'Admin\AdminMelmagaAnalyticsController@index');

		//集計-メルマガ解析-閲覧済
		Route::get('member/analytics/melmaga/access/visited/{melmaga_id}', 'Admin\AdminMelmagaAnalyticsController@viewVisited');

		//集計-メルマガ解析-閲覧済
		Route::get('member/analytics/melmaga/access/unseen/{melmaga_id}', 'Admin\AdminMelmagaAnalyticsController@viewUnseen');

		//集計-PVログ-年
		Route::get('member/analytics/pv/access/{year?}', 'Admin\AdminPvAnalyticsController@index');

		//集計-PVログ-月
		Route::get('member/analytics/pv/access/{year}/{month}/{pv_name}', 'Admin\AdminPvAnalyticsController@monthAnalysis');

		//集計-利用統計-年
		Route::get('member/analytics/statistics/access/{year?}', 'Admin\AdminUserStatisticsController@index');

		//集計-利用統計-月
		Route::get('member/analytics/statistics/access/{channel_id}/{year}/{month}', 'Admin\AdminUserStatisticsController@monthAnalysis');

		//集計-利用統計-日
		Route::get('member/analytics/statistics/access/{year}/{month}/{day}', 'Admin\AdminUserStatisticsController@dayAnalysis');

		//広告-ASP一覧
		Route::get('member/ad/asp', 'Admin\AdminAdAspController@index');

		//広告-ASP一覧-一括削除
		Route::post('member/ad/asp/send', 'Admin\AdminAdAspController@bulkDeleteSend');

		//広告-ASP作成
		Route::get('member/ad/asp/create', 'Admin\AdminAdAspController@create');

		//広告-ASP作成処理
		Route::post('member/ad/asp/create/send', 'Admin\AdminAdAspController@createSend');

		//広告-ASP編集
		Route::get('member/ad/asp/edit/{page}/{asp_id}', 'Admin\AdminAdAspController@edit');

		//広告-ASP編集処理
		Route::post('member/ad/asp/edit/send', 'Admin\AdminAdAspController@store');

		//広告-広告コード-一覧
		Route::get('member/ad/adcode', 'Admin\AdminAdCodeController@index');

		//広告-広告コード-一覧-一括削除
		Route::post('member/ad/adcode/send', 'Admin\AdminAdCodeController@bulkDeleteSend');

		//広告-広告コード-新規作成
		Route::get('member/ad/adcode/create', 'Admin\AdminAdCodeController@create');

		//広告-広告コード-新規作成処理
		Route::post('member/ad/adcode/create/send', 'Admin\AdminAdCodeController@createSend');

		//広告-広告コード-編集
		Route::get('member/ad/adcode/edit/{page}/{ad_id}', 'Admin\AdminAdCodeController@edit');

		//広告-広告コード-編集処理
		Route::post('member/ad/adcode/edit/send', 'Admin\AdminAdCodeController@store');

		//広告-広告コード-検索設定
		Route::get('member/ad/adcode/search/setting', 'Admin\AdminAdCodeController@searchSetting');

		//広告-広告コード-検索処理
		Route::post('member/ad/adcode/search', 'Admin\AdminAdCodeController@searchPost');
		Route::get('member/ad/adcode/search', 'Admin\AdminAdCodeController@search');

		//広告-代理店-一覧
		Route::get('member/ad/agency', 'Admin\AdminAdAgencyController@index');

		//広告-代理店-一括削除
		Route::post('member/ad/agency/send', 'Admin\AdminAdAgencyController@bulkDeleteSend');

		//広告-代理店-新規作成
		Route::get('member/ad/agency/create', 'Admin\AdminAdAgencyController@create');

		//広告-代理店-新規作成処理
		Route::post('member/ad/agency/create/send', 'Admin\AdminAdAgencyController@createSend');

		//広告-代理店-編集
		Route::get('member/ad/agency/edit/{page}/{ad_id}', 'Admin\AdminAdAgencyController@edit');

		//広告-代理店-編集処理
		Route::post('member/ad/agency/edit/send', 'Admin\AdminAdAgencyController@store');

		//広告-媒体集計-一覧
		Route::get('member/ad/media', 'Admin\AdminAdMediaController@index');

		//広告-媒体集計-検索設定
		Route::get('member/ad/media/search/setting', 'Admin\AdminAdMediaController@searchSetting');

		//広告-媒体集計-検索処理
		Route::post('member/ad/media/search', 'Admin\AdminAdMediaController@searchPost');
		Route::get('member/ad/media/search', 'Admin\AdminAdMediaController@search');

		Route::get('member/confirm/template/send/{channel_id}', 'Api\LineConfirmPushMessageController@sendLineConfirmTemplate');
	});

/*
 * 外部URLへリダイレクト
 */
Route::get('/{push_id}/{short_url}/{type?}', 'RedirectController@index');

});



