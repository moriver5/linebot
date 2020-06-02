<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Melmaga_log extends Model
{
	protected $fillable = [
		'id', 
		'send_status', 
		'send_count',
		'send_method',
		'from_name',
		'from_mail',
		'subject',
		'text_body',
		'html_body',
		'query',
		'bindings',
		'items',
		'exclusion_groups',
		'send_date',
		'reserve_send_date',
		'sort_reserve_send_date',
		'created_at',
		'updated_at'
	];
}
