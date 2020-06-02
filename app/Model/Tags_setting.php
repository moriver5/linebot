<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Tags_setting extends Model
{
	protected $fillable = [
		'id', 
		'name', 
		'tag',
		'open_flg',
		'created_at',
		'updated_at'
	];
}
