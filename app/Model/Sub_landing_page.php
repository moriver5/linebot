<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Sub_landing_page extends Model
{
	protected $fillable = [
		'lp_id', 
		'open_flg', 
		'page_name', 
		'memo', 
		'img', 
		'sort_date',
		'created_at',
		'updated_at'
	];
}
