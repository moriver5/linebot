<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class LineTempImmediateMsg extends Model
{
	protected $fillable = [
		'line_push_id', 
		'user_line_id', 
		'created_at',
		'updated_at'
	];
}
