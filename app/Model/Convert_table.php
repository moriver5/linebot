<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Convert_table extends Model
{
	protected $fillable = [
		'id', 
		'group_id',
		'key', 
		'value', 
		'memo',
		'created_at',
		'updated_at'
	];
}
