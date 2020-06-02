<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class LineContents extends Model
{
	protected $fillable = [
		'id', 
		'line_basic_id', 
		'type',
		'msg1',
		'msg2',
		'msg3',
		'msg4',
		'msg5',
		'image',
		'created_at',
		'updated_at'
	];
}
