<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Confirm_email extends Model
{
	protected $fillable = [
		'id',
		'name',
		'email',
		'created_at',
		'updated_at'
	];
}
