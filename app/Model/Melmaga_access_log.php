<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Melmaga_access_log extends Model
{
	protected $fillable = [
		'melmaga_id', 
		'login_id', 
		'access_date', 
		'created_at',
		'updated_at'
	];
}
