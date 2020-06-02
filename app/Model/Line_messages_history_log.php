<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Line_messages_history_log extends Model
{
	protected $fillable = [
		'line_push_id', 
		'user_line_id', 
		'read_flg', 
		'sort_date', 
		'created_at',
		'updated_at'
	];
}
