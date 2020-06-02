<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Melmaga_temp_immediate_mail extends Model
{
	protected $fillable = [
		'melmaga_id', 
		'client_id', 
		'success_flg', 
		'created_at',
		'updated_at'
	];
}
