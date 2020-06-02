<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminEmojiController extends Controller
{
	public function convert($id)
	{

		$disp_data = [
			'id'		=> $id,
			'ver'		=> time(),
		];

		return view('admin.master.convert_emoji', $disp_data);
	}

	public function convertHtmlMelmaga()
	{

		$disp_data = [
			'ver'		=> time(),
		];

		return view('admin.melmaga.convert_emoji_melmaga_html', $disp_data);
	}
}
