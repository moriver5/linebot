<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendMail extends Mailable
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
			//返信先設定
			//メールヘッダ設定
			$header = $message->getHeaders();

			if( !empty($this->options['replay_to']) ){
	            $header->addTextHeader('Reply-To', $this->options['replay_to']);
			}
			
			if( !empty($this->options['received']) ){
	            $header->addTextHeader('Received', $this->options['received']);
			}

			$return_email = $this->options['return_path'];
			list($account, $domain) = explode("@", $return_email);

			if( !empty($this->options['client_id']) ){
				$return_email = "{$account}.{$this->options['client_id']}@{$domain}";
			}

			//リターンパス設定
            $message->getHeaders()->addTextHeader('Return-Path', $return_email);

			//Message-ID設定
			$message->getHeaders()->addTextHeader('Message-ID', "<".md5(uniqid(microtime()))."@{$domain}>");
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
