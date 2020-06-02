<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Client_export_log extends Model
{
	//
	protected $fillable = [
		'id',
		'login_id',
		'file',
		'created_at',
		'updated_at'
	];
}
