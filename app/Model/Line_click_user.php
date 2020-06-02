<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Line_click_user extends Model
{
	protected $fillable = [
		'line_push_id', 
		'line_basic_id',
		'user_line_id', 
		'short_url',
		'url',
		'read',
		'click',
		'sort_date',
		'created_at',
		'updated_at'
	];
}
