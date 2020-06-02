<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Ad_code extends Model
{
	protected $fillable = [
		'id',
		'line_basic_id',
		'group_id',
		'asp_id',
		'ad_cd',
		'agency_id',
		'category',
		'aggregate_flg',
		'name',
		'url',
		'memo',
		'created_at',
		'updated_at'
	];
}
