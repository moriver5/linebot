<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Access_log extends Model
{
	protected $fillable = [
		'id', 
		'login_id',
		'pay_type',
		'login_date',
		'created_at',
		'updated_at'
	];
}
