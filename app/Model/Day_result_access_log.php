<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Day_result_access_log extends Model
{
	//
	protected $fillable = [
		'access_date', 
		'no_pay', 
		'pay',
		'total',
		'no_pay24',
		'pay24',
		'total24',
		'created_at',
		'updated_at'
	];
}
