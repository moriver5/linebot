<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class User_group extends Model
{
	protected $fillable = [
		'client_id',
		'group_id',
		'status',
		'sex',
		'age',
		'ad_cd',
		'remember_token',
		'quit_datetime',
		'sort_quit_datetime',
		'regist_date',
		'last_access',
		'disable',
		'created_at',
		'updated_at'
	];
}
