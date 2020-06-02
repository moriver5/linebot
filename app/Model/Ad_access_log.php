<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Ad_access_log extends Model
{
	protected $fillable = [
		'line_basic_id', 
		'ad_cd',
		'pv',
		'uu',
		'reg',
		'access_date',
		'created_at',
		'updated_at'
	];
}
