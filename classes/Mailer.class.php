<?php
	class Mailer 
	{
		/* Email Setup */
		public $sender;
		public $name;
		public $body;
		public $subject;
		public $reply;
		public $email;
		public $copy;
		public $bcc;
		public $debug;

		public function send()
		{
			$url = "http://temp-api.redecredauto.com.br/mailer/index.php";
		   	$data = array(
		   		'subject'=>$this->subject,
		   		'body'=>$this->body,
		   		'name'=>$this->name,
		   		'sender'=>$this->sender,
		   		'email'=>$this->email,
		   		'copy'=>$this->copy,
		   		// 'bcopy'=>$this->bcopy,
		   		'token'=>'DJ37.SUE7.DH60.SDU54'
		   	);

		   	$verify = curl_init();
			curl_setopt($verify, CURLOPT_URL, $url);
			curl_setopt($verify, CURLOPT_POST, true);
			curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($data));
			curl_setopt($verify, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
			// curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); 
			// curl_setopt($ch, CURLOPT_TIMEOUT, 5);
			
			$response = curl_exec($verify);

		    $enviado = $response == 'OK' ? true : false;
		    return $enviado;
		}
	}
?>