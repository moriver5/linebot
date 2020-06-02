<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AdminChannelAllowList extends Model
{
	protected $fillable = [
		'admin_id',
		'line_basic_id',
	];
}
