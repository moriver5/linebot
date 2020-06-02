<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Landing_page extends Model
{
	protected $fillable = [
		'id', 
		'open_flg', 
		'memo', 
		'img', 
		'piwik_id',
		'sort_date',
		'created_at',
		'updated_at'
	];
}
