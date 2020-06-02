<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class LineUserProfile extends Model
{
	protected $fillable = [
		'id', 
		'user_line_id', 
		'name',
		'image',
		'message',
		'created_at',
		'updated_at'
	];
}
