<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Mail_content_type extends Model
{
	protected $fillable = [
		'id', 
		'name', 
		'memo', 
		'created_at',
		'updated_at'
	];
}
