<?php

namespace App\Model;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Agency extends Authenticatable
{
	use Notifiable;

	protected $fillable = [
		'id',
		'name',
		'login_id',
		'password',
		'password_raw',
		'remember_token',
		'memo',
		'created_at',
		'updated_at'
	];
}
