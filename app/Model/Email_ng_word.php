<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Email_ng_word extends Model
{
    //
	protected $fillable = [
		'type',
		'word',
		'created_at',
		'updated_at'
	];
}
