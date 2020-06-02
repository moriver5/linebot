<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Line_imagemap_log extends Model
{
	protected $fillable = [
		'id', 
		'line_basic_id',
		'send_type',
		'send_status',
		'send_count',
		'img',
		'baseurl',
		'alttext',
		'video_original_url',
		'video_preview_url',
		'video_areax',
		'video_areay',
		'video_area_width',
		'video_area_height',
		'video_external_link',
		'video_external_label',
		'area_json',
		'send_date',
		'reserve_send_date',
		'sort_reserve_send_date',
		'created_at',
		'updated_at',
	];
}
