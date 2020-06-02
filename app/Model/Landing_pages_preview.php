<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Landing_pages_preview extends Model
{
	protected $fillable = [
		'lp_id', 
		'name', 
		'content',
		'created_at',
		'updated_at'
	];
}
