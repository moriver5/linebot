<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
	protected $fillable = [
		'id', 
		'domain', 
		'memo',
		'created_at',
		'updated_at'
	];
}
