<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Line_asp extends Model
{
	protected $fillable = [
		'id', 
		'asp', 
		'kickback_url', 
		'created_at',
		'updated_at'
	];
}
