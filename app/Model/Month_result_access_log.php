<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Month_result_access_log extends Model
{
	//
	protected $fillable = [
		'access_date', 
		'no_pay', 
		'pay',
		'total',
		'created_at',
		'updated_at'
	];
}
