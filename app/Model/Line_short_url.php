<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Line_short_url extends Model
{
	protected $fillable = [
		'url', 
		'short_url', 
		'click',
		'created_at',
		'updated_at'
	];
}
