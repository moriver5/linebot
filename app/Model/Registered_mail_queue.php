<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Registered_mail_queue extends Model
{
	protected $fillable = [
		'ad_cd',
		'client_id',
		'mail',
		'group_id',
		'created_at',
		'updated_at'
	];
}
