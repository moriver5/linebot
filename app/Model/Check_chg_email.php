<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Check_chg_email extends Model
{
    //
	protected $fillable = [
		'id',
		'login_id',
		'token',
		'email',
		'created_at',
		'updated_at'
	];
}
