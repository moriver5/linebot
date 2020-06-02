<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Personal_mail_log extends Model
{
    //
	protected $fillable = [
		'id', 
		'client_id', 
		'subject', 
		'body', 
		'created_at',
		'updated_at'
	];
}
