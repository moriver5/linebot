<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Sub_landing_pages_preview extends Model
{
	protected $fillable = [
		'lp_id', 
		'page_name', 
		'name', 
		'content',
		'created_at',
		'updated_at'
	];
}
