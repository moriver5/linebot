<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Month_pv_log extends Model
{
	protected $fillable = [
		'access_date', 
		'url', 
		'total',
		'created_at',
		'updated_at'
	];
}
