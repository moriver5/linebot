<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
	//
	protected $fillable = [
		'id', 
		'name', 
		'memo',
		'created_at',
		'updated_at'
	];
}
