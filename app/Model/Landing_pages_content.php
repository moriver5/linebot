<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Landing_pages_content extends Model
{
	protected $fillable = [
		'lp_id', 
		'url_open_flg', 
		'name', 
		'content',
		'created_at',
		'updated_at'
	];
}
