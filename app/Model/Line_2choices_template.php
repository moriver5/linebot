<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Line_2choices_template extends Model
{
	protected $fillable = [
		'id', 
		'line_basic_id',
		'send_type',
		'send_status',
		'send_count',
		'push_title',
		'send_date',
		'reserve_send_date',
		'sort_reserve_send_date',
		'created_at',
		'updated_at',
	];
}
