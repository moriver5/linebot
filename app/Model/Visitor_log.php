<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Visitor_log extends Model
{
	protected $fillable = [
		'forecast_id',
		'client_id',
		'category',
		'created_at',
		'updated_at'
	];
}
