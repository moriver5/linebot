<?php

namespace App\Libs;

use Auth;
use App\Model\Admin;
use Carbon\Carbon;

class FormParts
{
	/*
	 * 
	 */
	public function getMakeSelectOptions($list_option_data, $signature = '', $selected = '')
	{
		$options = "";
		foreach($list_option_data as $line){
			if( !is_array($line) ){
				if( empty($line) ){
					continue;
				}
				if( $selected == $line ){
					$options .= "<option value='{$line}' selected>{$signature}{$line}</option>";					
				}else{
					$options .= "<option value='{$line}'>{$signature}{$line}</option>";
				}
			}else{
				if( empty($line[0]) ){
					continue;
				}
				if( $selected == $line ){
					$options .= "<option value='{$line[0]}' selected>{$signature}{$line[1]}</option>";					
				}else{
					$options .= "<option value='{$line[0]}'>{$signature}{$line[1]}</option>";
				}
			}
		}

		return $options;
	}
}
	

