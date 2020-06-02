<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Result_ad_log extends Model
{
	protected $fillable = [
		'ad_cd', 
		'access_date', 
		'pv', 
		'temp_reg',
		'reg',
		'quit',
		'active',
		'order_num',
		'pay',
		'amount',
		'created_at',
		'updated_at'
	];
}
