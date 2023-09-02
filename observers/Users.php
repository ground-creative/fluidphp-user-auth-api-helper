<?php

	namespace helpers\UserAuthApi\observers;
	
	use helpers\UserAuthApi\models\Mail;
		
	class Users
	{
		public static function created($username, $code, $data)
		{
			if (\App::option("auth.verify"))
			{
				$mail_options = \App::options("user-auth-api.mail_config");
				Mail::sendUserVerification($mail_options['noreply_address'], \App::options('user-auth-api.mail_tpls_data._company_name'), $data);
			}
		}
	}