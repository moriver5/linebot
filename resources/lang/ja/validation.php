<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */
    'accepted'             => ':attributeを承認してください。',
    'active_url'           => ':attributeには有効なURLを指定してください。',
    'after'                => ':attributeには:date以降の日付を指定してください。',
    'alpha'                => ':attributeには英字のみからなる文字列を指定してください。',
    'alpha_dash'           => ':attributeには英数字・ハイフン・アンダースコアのみからなる文字列を指定してください。',
    'alpha_num'            => ':attributeには英数字のみからなる文字列を指定してください。',
    'array'                => ':attributeには配列を指定してください。',
    'before'               => ':attributeには:date以前の日付を指定してください。',
    'between'              => [
        'numeric' => ':attributeには:min〜:maxまでの数値を指定してください。',
        'file'    => ':attributeには:min〜:max KBのファイルを指定してください。',
        'string'  => ':attributeには:min〜:max文字の文字列を指定してください。',
        'array'   => ':attributeには:min〜:max個の要素を持つ配列を指定してください。',
    ],
    'boolean'              => ':attributeには真偽値を指定してください。',
    'confirmed'            => ':attributeが確認用の値と一致しません。',
    'date'                 => ':attributeには正しい形式の日付を指定してください。',
    'date_format'          => '":format"という形式の日付を指定してください。',
    'different'            => ':attributeには:otherとは異なる値を指定してください。',
    'digits'               => ':attributeには:digits桁の数値を指定してください。',
    'digits_between'       => ':attributeには:min〜:max桁の数値を指定してください。',
    'dimensions'           => ':attributeの画像サイズが不正です。',
    'distinct'             => '指定された:attributeは既に存在しています。',
    'email'                => ':attributeに正しい形式のメールアドレスを入力してください。',
    'exists'               => '指定された:attributeは存在しません。',
    'file'                 => ':attributeにはファイルを指定してください。',
    'filled'               => ':attributeには空でない値を指定してください。',
    'image'                => ':attributeには画像ファイルを指定してください。',
    'in'                   => ':attributeには:valuesのうちいずれかの値を指定してください。',
    'in_array'             => ':attributeが:otherに含まれていません。',
    'integer'              => ':attributeには整数を指定してください。',
    'ip'                   => ':attributeには正しい形式のIPアドレスを指定してください。',
    'json'                 => ':attributeには正しい形式のJSON文字列を指定してください。',
//	'login_id'			   => '正しい:attributeを指定してください。',
    'max'                  => [
        'numeric' => ':attributeには:max以下の数値を指定してください。',
        'file'    => ':attributeには:max KB以下のファイルを指定してください。',
        'string'  => ':attributeには:max文字以下の文字列を指定してください。',
        'array'   => ':attributeには:max個以下の要素を持つ配列を指定してください。',
    ],
    'mimes'                => ':attributeには:valuesのうちいずれかの形式のファイルを指定してください。',
    'mimetypes'            => ':attributeには:valuesのうちいずれかの形式のファイルを指定してください。',
    'min'                  => [
        'numeric' => ':attributeには:min以上の数値を指定してください。',
        'file'    => ':attributeには:min KB以上のファイルを指定してください。',
        'string'  => ':attributeには:min文字以上の文字列を指定してください。',
        'array'   => ':attributeには:min個以上の要素を持つ配列を指定してください。',
    ],
    'not_in'               => ':attributeには:valuesのうちいずれとも異なる値を指定してください。',
    'numeric'              => ':attributeには半角数値を指定してください。',
    'present'              => ':attributeには現在時刻を指定してください。',
//	'password'			   => ':attributeは必須です',
    'regex'                => '正しい形式の:attributeを指定してください。',
    'required'             => ':attributeは必須です。',
    'required_if'          => ':otherが:valueの時:attributeは必須です。',
    'required_unless'      => ':otherが:values以外の時:attributeは必須です。',
    'required_with'        => ':valuesのうちいずれかが指定された時:attributeは必須です。',
    'required_with_all'    => ':valuesのうちすべてが指定された時:attributeは必須です。',
    'required_without'     => ':valuesのうちいずれかがが指定されなかった時:attributeは必須です。',
    'required_without_all' => ':valuesのうちすべてが指定されなかった時:attributeは必須です。',
    'same'                 => ':attributeが:otherと一致しません。',
    'size'                 => [
        'numeric' => ':attributeには:sizeを指定してください。',
        'file'    => ':attributeには:size KBのファイルを指定してください。',
        'string'  => ':attributeには:size文字の文字列を指定してください。',
        'array'   => ':attributeには:size個の要素を持つ配列を指定してください。',
    ],
    'string'               => ':attributeには文字列を指定してください。',
    'timezone'             => ':attributeには正しい形式のタイムゾーンを指定してください。',
    'unique'               => 'その:attributeはすでに登録されています。',
    'uploaded'             => ':attributeのアップロードに失敗しました。',
    'url'                  => ':attributeには正しい形式のURLを指定してください。',
    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */
    'custom' => [
		'page' => [
			'check_file_name'	 => ':attributeに使用できない文字が含まれています。',
			'check_exist_lp_file'=> 'その:attributeは既に使用されています。',
		],
		'melmaga_relayserver' => [
			'check_relayserver'	 => '正しい:attributeを入力してください。',								
		],
		'setting_relayserver' => [
			'check_relayserver'	 => '正しい:attributeを入力してください。',											
		],
		'personal_relayserver' => [
			'check_relayserver'	 => '正しい:attributeを入力してください。',											
		],
		'melmaga_port' => [
			'digital_num_check'	 => '半角数のみで:attributeを入力してください。',								
		],
		'setting_port' => [
			'digital_num_check'	 => '半角数のみで:attributeを入力してください。',								
		],
		'personal_port' => [
			'digital_num_check'	 => '半角数のみで:attributeを入力してください。',								
		],
		'banner' => [
			'required'	 => ':attributeを入力してください。',											
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'banner.*' => [
			'required'	 => ':attributeを入力してください。',											
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'agency' => [
			'required'	 => ':attributeを入力してください。',											
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'ad_cd' => [
			'required'			 => ':attributeを入力してください。',
			'alpha_num_check'	 => ':attributeには半角英数字のみからなる文字列を入力してください',
		],
		'del' => [
			'required'	 => ':attributeにチェックを入れてください。',								
		],
		'user_name' => [
			'required'	 => ':attributeを入力してください。',								
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'user_name.*' => [
			'required'	 => ':attributeを入力してください。',								
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'del_img' => [
			'required'	 => '削除したい:attributeにチェックを入れてください。',								
		],
		'img' => [
			'required'	 => ':attributeを入力してください。',								
		],
		'disp_sdate' => [
			'required'			 => ':attributeを入力してください。',
			'date_format_check'	 => '指定の:attributeで入力してください。',
		],
		'disp_edate' => [
			'required'			 => ':attributeを入力してください。',
			'date_format_check'	 => '指定の:attributeで入力してください。',
		],
		'open_sdate' => [
			'required'			 => ':attributeを入力してください。',								
			'date_format_check'	 => 'attributeを正しいフォーマットで入力してください。',
		],
		'open_edate' => [
			'required'			 => ':attributeを入力してください。',								
			'date_format_check'	 => 'attributeを正しいフォーマットで入力してください。',
		],
		'comment' => [
			'required'				 => ':attributeを入力してください。',
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'detail' => [
			'required'	 => ':attributeを入力してください。',
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'product_id' => [
			'required'	 => ':attributeを選択してください。',								
		],
		'money' => [
			'required'	 => ':attributeを入力してください。',								
		],
		'money.*' => [
			'required'				 => ':attributeを入力してください。',								
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'start_date' => [
			'required'	 => ':attributeを入力してください。',								
		],
		'end_date' => [
			'required'	 => ':attributeを入力してください。',								
		],
		'remarks' => [
			'required'	 => ':attributeを入力してください。',								
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'remarks.*' => [
			'required'				 => ':attributeを入力してください。',					
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'category_name' => [
			'required'				 => ':attributeを入力してください。',					
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'category_name.*' => [
			'required'				 => ':attributeを入力してください。',					
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'subject' => [
			'required'				 => ':attributeを入力してください。',
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'to_mail' => [
			'required'			 => ':attributeを入力してください。',
			'check_mx_domain'	 => ':attributeに存在しないドメインが使用されています。正しいドメインを入力してください。',
		],
		'from_mail' => [
			'required'			 => ':attributeを入力してください。',
			'check_mx_domain'	 => ':attributeに存在しないドメインが使用されています。正しいドメインを入力してください。',
		],
		'from_name'	=> [
			'required'				 => ':attributeを入力してください。',
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',			
		],
		'remember_token' => [
			'required' => ':attributeを入力してください。',
		],
		'body'		 => [
			'required'			=> ':attributeを入力してください。',			
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',			
		],
		'html_body' => [
			'required'				 => ':attributeを入力してください。',			
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',						
		],
		'group_id'		 => [
			'required'				=> '移行先の:attributeを選択してください。',	
			'only_num_check'		=> ':attributeIDには半角数字で複数ある場合は半角カンマで区切って入力してください。',
			'exist_group_id_check'	=> '存在する:attributeIDを入力してください。',
		],
		'groups'		 => [
			'only_num_check'		=> ':attributeには半角数字で複数ある場合は半角カンマで区切って入力してください。',
			'exist_group_id_check'	=> '存在する:attributeを入力してください。',
		],
		'campaigns'		 => [
			'only_num_check'			=> ':attributeには半角数字で複数ある場合は半角カンマで区切って入力してください。',
			'exist_campaign_id_check'	=> '存在する:attributeを入力してください。',
		],
		'move_group_id'		 => [
			'required'			=> '移行前の:attributeを選択してください。',			
		],
		'point'			 => [
			'required'			=> ':attributeを入力してください。',
			'check_add_point'	=> ':attributeをプラス付与する場合は半角数字のみ、マイナス付与する場合は-(半角ハイフン)のあとに半角数字を使用してください。例：プラス付与：100、マイナス付与：-100',
			'integer'			=> ':attributeは半角数値で入力してください。',
		],
		'point.*'			 => [
			'required'			=> ':attributeを入力してください。',
			'check_add_point'	=> ':attributeをプラス付与する場合は半角数字のみ、マイナス付与する場合は-(半角ハイフン)のあとに半角数字を使用してください。例：プラス付与：100、マイナス付与：-100',
			'integer'			=> ':attributeは半角数値で入力してください。',
		],
		'point_id'			 => [
			'required'			=> ':attributeを選択してください。',
		],
		'sort'			 => [
			'required'		=> ':attributeを入力してください。',
		],
		'title'			 => [
			'required'		=> ':attributeを入力してください。',
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',			
		],
		'name'			 => [
			'required'		=> ':attributeを入力してください。',
		],
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
		'email' => [
			'exists'				=> '指定された:attributeは存在しません。',
			'check_email'			=> ':attributeに正しい形式のメールアドレスを入力してください。',
			'check_email_domain'	=> 'SVCの:attributeを入力してください。',
			'check_approved'		=> 'アカウントが未承認です。承認されるまでお待ちください。',
			'check_mx_domain'		=> ':attributeに存在しないドメインが使用されています。正しいドメインを入力してください。',
		],
		'login_id' => [
			'required'			 => ':attributeを入力してください。',
			'max'				 => ':attributeが字数を超えています。',
			'alpha_num_check'	 => ':attributeには半角英数字のみからなる文字列を入力してください。',
			'check_regist_status'=> ':attributeが本登録されていません。本登録を完了させてください。',
		],
		'password' => [
			'required'			=> ':attributeを入力してください。',
			'confirmed'			=> ':attributeが確認用の値と一致しません。',
			'use_char_check'	=> ':attributeに使用できない文字が含まれています。',
			'alpha_num_check'	=> ':attributeには半角英数字のみからなる文字列を入力してください',
			'check_empty_password'		 => ':attributeが設定されていません。:attributeを設定してください。',
		],
		'current_password' => [
			'required'					 => '現在の:attributeを入力してください。',
			'password_registed_match'	 => '現在の:attributeが違います。',
			'is_space'					 => ':attributeに空白文字が含まれています。',
			'use_char_check'			 => ':attributeに使用できない文字が含まれています。',
			'alpha_num_check'			 => ':attributeには半角英数字のみからなる文字列を入力してください。',
		],
		'new_password'		=> [
			'required'		 => ':attributeを入力してください。',
			'max'			 => ':attributeの文字数は:max文字以下です。',
			'min'			 => ':attributeの文字数は:min文字以上です。',
			'confirmed'		 => ':attributeが確認用パスワードと一致しません。',
			'is_space'		 => ':attributeに空白文字が含まれています。',
			'use_char_check' => ':attributeに使用できない文字が含まれています。',
		],
		'new_password_confirmation' => [
			'required'		 => '確認の:attributeを入力してください。',
			'max'			 => ':attributeの文字数は:max文字以下です。',
			'min'			 => ':attributeの文字数は:min文字以上です。',
			'is_space'		 => ':attributeに空白文字が含まれています。',
			'use_char_check' => ':attributeに使用できない文字が含まれています。',
		],
		'pc_email' => [
			'required'			 => ':attributeを入力してください。',
			'check_mx_domain'	 => ':attributeに存在しないドメインが使用されています。正しいドメインを入力してください。',
		],
		'email_account' => [
			'email_account'		 => ':attributeの形式が間違っています。',
			'check_mx_domain'	 => ':attributeに存在しないドメインが使用されています。正しいドメインを入力してください。',
		],
		'contents' => [
			'required'				 => ':attributeを入力してください。',
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'tel' => [
			'numeric' => ':attributeには番号のみで入力してください。',
		],
 		'msg1' => [
			'required'				 => ':attributeを入力してください。',
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
 		'race_name' => [
			'required'				 => ':attributeを入力してください。',
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
 		'writer' => [
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
 		'lp_content' => [
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
 		'description' => [
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
 		'sentence' => [
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
 		'ng_word' => [
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
 		'disp_msg' => [
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
 		'disp_msg.*' => [
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
 		'key' => [
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
 		'key.*' => [
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
 		'value' => [
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
 		'value.*' => [
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
 		'message' => [
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
 		'item_value' => [
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'domain' => [
			'check_exist_domain'	 => '存在しない:attributeです。存在する:attributeを入力してください',
		],
		'domain.*' => [
			'check_exist_domain'	 => '存在しない:attributeです。存在する:attributeを入力してください',
		],
		'piwik_id' => [
			'alpha_num_check'		 => ':attributeは半角数字で入力してください'
		],
		'tag_name' => [
			'required'				 => ':attributeを入力してください。',											
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'tag_name.*' => [
			'required'				 => ':attributeを入力してください。',											
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'tag' => [
			'required'				 => ':attributeを入力してください。',											
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'tag.*' => [
			'required'				 => ':attributeを入力してください。',											
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'after_day' => [
			'required'				 => ':attributeか入力してください。',											
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'reserve_date' => [
			'required'				 => ':attributeを入力してください。',											
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'reserve_week' => [
			'required'				 => ':attributeを入力してください。',											
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'regular_time' => [
			'required'				 => ':attributeを入力してください。',											
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'title1' => [
			'required'				 => ':attributeを入力してください。',											
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'text1' => [
			'required'				 => ':attributeを入力してください。',											
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'label1' => [
			'required'				 => ':attributeを入力してください。',											
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'value1' => [
			'required'				 => ':attributeを入力してください。',											
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'title2' => [
			'required'				 => ':attributeを入力してください。',											
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'text2' => [
			'required'				 => ':attributeを入力してください。',											
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'label2' => [
			'required'				 => ':attributeを入力してください。',											
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'value2' => [
			'required'				 => ':attributeを入力してください。',											
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'title3' => [
			'required'				 => ':attributeを入力してください。',											
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'text3' => [
			'required'				 => ':attributeを入力してください。',											
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'label3' => [
			'required'				 => ':attributeを入力してください。',											
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'value3' => [
			'required'				 => ':attributeを入力してください。',											
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'title4' => [
			'required'				 => ':attributeを入力してください。',											
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'text4' => [
			'required'				 => ':attributeを入力してください。',											
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'label4' => [
			'required'				 => ':attributeを入力してください。',											
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'value4' => [
			'required'				 => ':attributeを入力してください。',											
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'title5' => [
			'required'				 => ':attributeを入力してください。',											
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'text5' => [
			'required'				 => ':attributeを入力してください。',											
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'label5' => [
			'required'				 => ':attributeを入力してください。',											
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'value5' => [
			'required'				 => ':attributeを入力してください。',											
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'title6' => [
			'required'				 => ':attributeを入力してください。',											
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'text6' => [
			'required'				 => ':attributeを入力してください。',											
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'label6' => [
			'required'				 => ':attributeを入力してください。',											
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'value6' => [
			'required'				 => ':attributeを入力してください。',											
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'title7' => [
			'required'				 => ':attributeを入力してください。',											
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'text7' => [
			'required'				 => ':attributeを入力してください。',											
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'label7' => [
			'required'				 => ':attributeを入力してください。',											
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'value7' => [
			'required'				 => ':attributeを入力してください。',											
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'title8' => [
			'required'				 => ':attributeを入力してください。',											
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'text8' => [
			'required'				 => ':attributeを入力してください。',											
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'label8' => [
			'required'				 => ':attributeを入力してください。',											
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'value8' => [
			'required'				 => ':attributeを入力してください。',											
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'title9' => [
			'required'				 => ':attributeを入力してください。',											
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'text9' => [
			'required'				 => ':attributeを入力してください。',											
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'label9' => [
			'required'				 => ':attributeを入力してください。',											
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'value9' => [
			'required'				 => ':attributeを入力してください。',											
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'title10' => [
			'required'				 => ':attributeを入力してください。',											
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'text10' => [
			'required'				 => ':attributeを入力してください。',											
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'label10' => [
			'required'				 => ':attributeを入力してください。',											
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'value10' => [
			'required'				 => ':attributeを入力してください。',											
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'push_title' => [
			'required'				 => ':attributeを入力してください。',											
			'surrogate_pair_check'	 => ':attributeに使用できない文字が含まれています。',
			'emoji_check'			 => ':attributeに絵文字は使用できません。',
		],
		'import_file' => [
			'check_upload_image_width' => 'LINEの仕様でイメージマップの:attributeの横幅は1040pxにしてください',
		],
		'tmp_titlemsg1' => [
			'required'				 => ':attributeを入力してください。',											
		],
		'tmp_titlemsg2' => [
			'required'				 => ':attributeを入力してください。',											
		],
		'tmp_titlemsg3' => [
			'required'				 => ':attributeを入力してください。',											
		],
		'tmp_titlemsg4' => [
			'required'				 => ':attributeを入力してください。',											
		],
		'tmp_titlemsg5' => [
			'required'				 => ':attributeを入力してください。',											
		],
		'tmp_urlmsg1' => [
			'required'				 => ':attributeを入力してください。',											
		],
		'tmp_urlmsg2' => [
			'required'				 => ':attributeを入力してください。',											
		],
		'tmp_urlmsg3' => [
			'required'				 => ':attributeを入力してください。',											
		],
		'tmp_urlmsg4' => [
			'required'				 => ':attributeを入力してください。',											
		],
		'tmp_urlmsg5' => [
			'required'				 => ':attributeを入力してください。',											
		],
	],
    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */
    'attributes' => [
		'tmp_titlemsg1'				 => '代替えテキスト',
		'tmp_titlemsg2'				 => '代替えテキスト',
		'tmp_titlemsg3'				 => '代替えテキスト',
		'tmp_titlemsg4'				 => '代替えテキスト',
		'tmp_titlemsg5'				 => '代替えテキスト',
		'tmp_urlmsg1'				 => 'クリック先URL',
		'tmp_urlmsg2'				 => 'クリック先URL',
		'tmp_urlmsg3'				 => 'クリック先URL',
		'tmp_urlmsg4'				 => 'クリック先URL',
		'tmp_urlmsg5'				 => 'クリック先URL',
		'import_file'				 => 'アップロード画像',
		'push_title'				 => 'メッセージタイトル',
		'title1'					 => 'カラム１のカラムタイトル',
		'text1'						 => 'カラム１のテキスト',
		'label1'					 => 'カラム１のアクションラベル',
		'value1'					 => 'カラム１のアクションラベルの値',
		'title2'					 => 'カラム２のカラムタイトル',
		'text2'						 => 'カラム２のテキスト',
		'label2'					 => 'カラム２のアクションラベル',
		'value2'					 => 'カラム２のアクションラベルの値',
		'title3'					 => 'カラム３のカラムタイトル',
		'text3'						 => 'カラム３のテキスト',
		'label3'					 => 'カラム３のアクションラベル',
		'value3'					 => 'カラム３のアクションラベルの値',
		'title4'					 => 'カラム４のカラムタイトル',
		'text4'						 => 'カラム４のテキスト',
		'label4'					 => 'カラム４のアクションラベル',
		'value4'					 => 'カラム４のアクションラベルの値',
		'title5'					 => 'カラム５のカラムタイトル',
		'text5'						 => 'カラム５のテキスト',
		'label5'					 => 'カラム５のアクションラベル',
		'value5'					 => 'カラム５のアクションラベルの値',
		'title6'					 => 'カラム６のカラムタイトル',
		'text6'						 => 'カラム６のテキスト',
		'label6'					 => 'カラム６のアクションラベル',
		'value6'					 => 'カラム６のアクションラベルの値',
		'title7'					 => 'カラム７のカラムタイトル',
		'text7'						 => 'カラム７のテキスト',
		'label7'					 => 'カラム７のアクションラベル',
		'value7'					 => 'カラム７のアクションラベルの値',
		'title8'					 => 'カラム８のカラムタイトル',
		'text8'						 => 'カラム８のテキスト',
		'label8'					 => 'カラム８のアクションラベル',
		'value8'					 => 'カラム８のアクションラベルの値',
		'title9'					 => 'カラム９のカラムタイトル',
		'text9'						 => 'カラム９のテキスト',
		'label9'					 => 'カラム９のアクションラベル',
		'value9'					 => 'カラム９のアクションラベルの値',
		'title10'					 => 'カラム１０のカラムタイトル',
		'text10'					 => 'カラム１０のテキスト',
		'label10'					 => 'カラム１０のアクションラベル',
		'value10'					 => 'カラム１０のアクションラベルの値',
		'after_day'					 => '友だち登録から何日後',
		'reserve_date'				 => '配信日時',
		'reserve_week'				 => '配信曜日',
		'regular_time'				 => '配信時間',
		'page'						 => 'ページ名',
		'melmaga_relayserver'		 => 'メルマガ用リレーサーバー',
		'setting_relayserver'		 => 'ユーザー用リレーサーバー',
		'personal_relayserver'		 => '個別用リレーサーバー',
		'melmaga_port'				 => 'メルマガ用のポート',
		'setting_port'				 => 'ユーザー用のポート',
		'personal_port'				 => '個別用のポート',
		'msg1'						 => 'メッセージ',
		'banner'					 => 'バナー',
		'banner.*'					 => 'バナー',
		'agency'					 => '代理店',
		'agency_id'					 => '代理店ID',
		'ad_name'					 => '広告コード名称',
		'ad_cd'						 => '広告コード',
		'message'					 => '表示文言',
		'tel'						 => '電話番号',
		'del'						 => '削除',
		'user_name'					 => '名前',
		'user_name.*'				 => '名前',
		'del_img'					 => '画像',
		'img'						 => '画像',
		'priority_id'				 => '優先順位表示ID',
		'race_date'					 => '開催日',
		'race_name'					 => 'レース名',
		'disp_sdate'				 => '開始表示日時',
		'disp_edate'				 => '終了表示日時',
		'open_sdate'				 => '開始公開日時',
		'open_edate'				 => '終了公開日時',
		'comment'					 => 'コメント',
		'detail'					 => '内容',
		'product_id'				 => '商品',
		'money'						 => '金額',
		'money.*'					 => '金額',
		'start_date'				 => '開始日時',
		'end_date'					 => '終了日時',
		'start_date.*'				 => '開始日時',
		'end_date.*'				 => '終了日時',
		'remarks'					 => '備考',
		'remarks.*'					 => '備考',
		'category_name'				 => 'カテゴリ名',
		'category_name.*'			 => 'カテゴリ名',
		'description'				 => 'MEMO',
		'subject'					 => '件名',
		'to_mail'					 => '送信先メールアドレス',
		'from_mail'					 => '送信元メールアドレス',
		'from_name'					 => '送信者名',
		'writer'					 => '投稿者',
		'remember_token'			 => 'アクセスキー',
		'body'						 => '内容',
		'html_body'					 => '内容(HTML)',
		'move_group_id'				 => 'グループ',
		'group_id'					 => 'グループ',
		'groups'					 => 'グループID',
		'campaigns'					 => 'キャンペーンID',
		'point'						 => 'ポイント',
		'point.*'					 => 'ポイント',
		'point_id'					 => '追加ポイント',
		'group'						 => 'グループ名',
		'group.*'					 => 'グループ名',
		'key'						 => '変換キー',
		'key.*'						 => '変換キー',
		'value'						 => '変更内容',
		'value.*'					 => '変更内容',
		'title'						 => 'タイトル',
		'name'						 => 'ログインID',
		'pc_email'					 => 'PCメールアドレス',
		'email'						 => 'メールアドレス',
		'email.*'					 => 'メールアドレス',
		'email_account'				 => '携帯メールアドレス',
		'sort'						 => '表示順',
		'login_id'					 => 'ID',
		'password'					 => 'パスワード',
		'current_password'			 => 'パスワード',
		'new_password'				 => 'パスワード',
		'check_new_password'		 => 'パスワード',
		'new_password_confirmation'	 => '確認用パスワード',
		'contents'					 => 'お問い合わせ内容',
		'lp_content'				 => 'Content',
		'sentence'					 => '出力文言',
		'ng_word'					 => '禁止ワード',
		'disp_msg'					 => '表示テキスト',
		'disp_msg.*'				 => '表示テキスト',
		'item_value'				 => '抽出項目',
		'default_id'				 => '通常',
		'setting_name'				 => '設定名',
		'lpid'						 => 'ランディングページID',
		'file'						 => 'ファイル名',
		'domain'					 => 'ドメイン名',
		'domain.*'					 => 'ドメイン名',
		'memo'						 => '説明',
		'memo.*'					 => '説明',
		'piwik_id'					 => 'piwikiのID',
		'tag_name'					 => 'タグ名',
		'tag_name.*'				 => 'タグ名',
		'tag'						 => 'タグ',
		'tag.*'						 => 'タグ',
	],
];