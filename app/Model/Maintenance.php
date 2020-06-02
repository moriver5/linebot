<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
	protected $fillable = [
		'type', 
		'mode', 
		'body',
		'created_at',
		'updated_at'
	];
}
