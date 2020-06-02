<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Magnification_setting extends Model
{
	protected $fillable = [
		'type',
		'default_id',
		'category_id', 
		'start_date', 
		'end_date',
		'created_at',
		'updated_at'
	];
}
