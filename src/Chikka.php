<?php 

/*
 * AUTHOR: 	Ivan Paul Bay
 * DATE: 	Sept 29, 2015
 *
 * A Package that will able to send and receive SMS using Chikka API
 *
 * Copyright 2015 
 */

namespace Ivanbay\Chikka;

use Illuminate\Support\Facades\Config;


define('MESSAGE_TYPE_1', 'incoming');
define('MESSAGE_TYPE_2', 'REPLY');
define('MESSAGE_TYPE_3', 'SEND');
define('MESSAGE_TYPE_4', 'outgoing');
define('MESSAGE_ID_LENGTH', 32);

/**
* 
*/
class Chikka
{
	
	protected $url;
	protected $client_id;
	protected $secret_key;
	protected $short_code;
	protected $message_id;


	/**
	* Initialize chikka configuration
	*
	* @return void
	*/
	protected function _init()
	{
		$this->url 			= Config::get('chikka.url');
		$this->client_id 	= Config::get('chikka.client_id');
		$this->secret_key 	= Config::get('chikka.secret_key');
		$this->short_code 	= Config::get('chikka.short_code');
		$this->message_id 	= $this->_generateMessageID();
	}


	/**
	* Generates 32-digits random numbers for message ID
	*
	* @return int 32-digits random ID
	*/
	protected function _generateMessageID()
	{
		return str_pad(rand(), MESSAGE_ID_LENGTH, rand(), STR_PAD_LEFT);
	}


	/** 
	 * Send new sms
	 * 
	 * @param string 	message
	 * @param int 	mobile number
	 * @param bool 	mustinit
	 *
	 * @return object 	Chikka response containing status, message, and message ID
	 */
	public function send($message, $mobile, $mustinit = true)
	{
		if( $mustinit ) $this->_init();

		$params = [
			"message_type" 	=> MESSAGE_TYPE_3,
			"mobile_number"	=> $mobile,
			"shortcode"		=> $this->short_code,
			"message_id"	=> $this->message_id,
			"message"		=> $message,
			"client_id"		=> $this->client_id,
			"secret_key"	=> $this->secret_key
		];

		$query = http_build_query($params);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->url);
		curl_setopt($ch, CURLOPT_POST, count($params));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //require when accessing secure connections. https
		$response = curl_exec($ch);
		curl_close($ch);

		$response = json_decode($response, true);
		$response['message_id'] = $this->message_id;
		$response = json_encode($response);

		return $response;

	}

}