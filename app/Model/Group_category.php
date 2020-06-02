<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Group_category extends Model
{
	protected $fillable = [
		'id', 
		'group_id',
		'category',
		'memo',
		'created_at',
		'updated_at'
	];
}
