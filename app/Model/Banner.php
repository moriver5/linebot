<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
	protected $fillable = [
		'id',
		'banner',
		'disp_flg',
		'created_at',
		'updated_at'
	];
}
