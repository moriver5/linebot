<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendRegist extends Mailable
{
	use Queueable, SerializesModels;

	protected $options;
	protected $data;

	/**
	 * Create a new message instance.
	 *
	 * @return void
	 */
	public function __construct($options, $data = [])
	{
		//
		$this->options	 = $options;
		$this->data		 = $data;
	}

	/**
	 * Build the message.
	 *
	 * @return $this
	 */
	public function build()
	{
		//smtp指定ならそのsmtpを設定
		if( !empty($this->options['host_ip']) ){
			config(['mail.host' => $this->options['host_ip']]);
		}

		//port指定ならそのportを設定
		if( !empty($this->options['port']) ){
			config(['mail.port' => $this->options['port']]);
		}

        $this->withSwiftMessage(function ($message) {
			//メールヘッダ設定
			$header = $message->getHeaders();

			if( !empty($this->options['replay_to']) ){
	            $header->addTextHeader('Reply-To', $this->options['replay_to']);
			}
			
			if( !empty($this->options['received']) ){
	            $header->addTextHeader('Received', $this->options['received']);
			}
/*
			if( !empty($this->options['return_path']) ){
				$header->addTextHeader('Return-Path', $this->options['return_path']);
			}
*/
			$return_email = config('const.return_path_to_mail');
//error_log(print_r($this->options,true)."\n",3,"/data/www/jray/storage/logs/nishi_log.txt");
			if( !empty($this->options['client_id']) ){
				list($account, $domain) = explode("@", config('const.return_path_to_mail'));
				$return_email = "{$account}.{$this->options['client_id']}@{$domain}";
			}

			//リターンパス設定
            $message->getHeaders()->addTextHeader('Return-Path', $return_email);
        });

		//HTMLメール
		if( isset($this->options['html_flg']) && $this->options['html_flg'] === true ){
			return $this->from($this->options['from'], $this->options['from_name'])
				->subject($this->options['subject'])
				->view($this->options['template'])
				->with($this->data);

		//プレーンメール
		}else{
			return $this->from($this->options['from'], $this->options['from_name'])
				->subject($this->options['subject'])
				->text($this->options['template'])
				->with($this->data);
		}
	}
}
