<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Payment_log extends Model
{
	//
	protected $fillable = [
		'payment_id', 
		'pay_type', 
		'login_id',
		'type',
		'product_id',
		'order_id',
		'money',
		'point',
		'ad_cd',
		'status',
		'regist_date',
		'pay_count',
		'sendid',
		'sort_date',
		'created_at',
		'updated_at'
	];
}
