<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Line_2choices_log extends Model
{
	protected $fillable = [
		'id', 
		'master_id',
		'msg',
		'act1',
		'label1',
		'value1',
		'act2',
		'label2',
		'value2',
		'created_at',
		'updated_at',
	];
}
