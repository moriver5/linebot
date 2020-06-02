<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Personal_access_log extends Model
{
    //
	protected $fillable = [
		'login_id', 
		'melmaga_id', 
		'page',
		'created_at',
		'updated_at'
	];
}
