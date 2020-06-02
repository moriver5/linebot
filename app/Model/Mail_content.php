<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Mail_content extends Model
{
	//
	protected $fillable = [
		'id', 
		'group_id',
		'type',
		'from', 
		'from_mail',
		'subject',
		'body',
		'created_at',
		'updated_at'
	];
}
