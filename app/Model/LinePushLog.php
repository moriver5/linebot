<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class LinePushLog extends Model
{
	protected $fillable = [
		'id', 
		'line_basic_id', 
		'send_after_minute',
		'send_week',
		'send_type', 
		'send_status', 
		'send_count',
		'msg1',
		'msg2',
		'msg3',
		'msg4',
		'msg5',
		'segment',
		'send_regualr_time',
		'send_date',
		'reserve_send_date',
		'sort_reserve_send_date',
		'created_at',
		'updated_at'
	];
}
