<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Ban_access_ip extends Model
{
	protected $fillable = [
		'access_ip',
		'group_id',
		'created_at',
		'updated_at'
	];
}
