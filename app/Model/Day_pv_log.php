<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Day_pv_log extends Model
{
	protected $fillable = [
		'ad_cd', 
		'login_id', 
		'access_date',
		'created_at',
		'updated_at'
	];
}
