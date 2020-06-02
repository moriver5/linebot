<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Relay_server extends Model
{
	protected $fillable = [
		'type',
		'ip',
		'port',
		'created_at',
		'updated_at'
	];
}
