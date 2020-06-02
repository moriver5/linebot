<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Line_postback_template extends Model
{
	protected $fillable = [
		'id', 
		'line_basic_id',
		'type',
		'name',
		'label',
		'postback',
		'msg1',
		'msg2',
		'msg3',
		'msg4',
		'msg5',
		'created_at',
		'updated_at',
	];
}
