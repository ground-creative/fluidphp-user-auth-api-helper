<?php

	namespace helpers\UserAuthApi\controllers;
	
	use \helpers\Logger\Manager as Logger;
	use \helpers\Validator\Core as Validator;
	use \Auth;
	use \App;
	use \helpers\UserAuthApi\models\Request;
	use \helpers\UserAuthApi\models\Response;
	use \helpers\UserAuthApi\models\Users;

	class User
	{
	
		public static $prefix_uri = '/account';
		
		public function get_new_token( )
		{
			//echo "HELLO";
		}
		/** 
		* uri: /user-auth-api/account/login/
		* description: tries to log user in
		* params: username, password
		* return data:
		*/
		public function post_login( )
		{
			$case = "user_login";
			$inputs = Validator::inputs('_post');
			if ($invalid = Request::check($case,$inputs))
			{
			
				$errors = $invalid->getErrors();
				return Response::error($invalid->getErrMsgs(key($errors), 
							key(reset($errors))), Request::firstError($invalid));
			}
			$data = App::storage('_req.' . $case);
			$login = Auth::login($data['username'], $data['password']);
			switch ($login)
			{
				case 0:
					return Response::error(500);
				break;
				case 4:
					return Response::error(801);
				break;
				case 3:
					return Response::error(111);
				break;
				case 2:
					return Response::error(504);
				break;
				default: // 1
					$user = Auth::user($data['username'])->toArray();
					unset($user['id']);
					if ($inputs['remember_me'])
					{
					
					}
					return Response::success("User logged in succesfully", $user);
			}
		}
		/** 
		* uri: /user-auth-api/account/fb-login/
		* description:
		* return data:
		*/
		public function post_fb_login( )
		{ 
		
		}
		/** 
		* uri: /auto-login/{cookie}/
		* description: tries to log in with existing login_token
		* return data: returns a new user autologin token if successful
		*/
		public function put_auto_login( $cookie )
		{ 

		}
		/** 
		* uri: /user-auth-api/account/register/
		* description: registers a new user to the database
		* params: see config/validator.php
		*/
		public function post_register( )
		{
			$case = "user_register";
			$inputs = Validator::inputs('_post');
			if ($invalid = Request::check($case,$inputs))
			{
				$errors = $invalid->getErrors();
				return Response::error($invalid->getErrMsgs(key($errors), 
							key(reset($errors))), Request::firstError($invalid));
			}
			$data = App::storage('_req.' . $case);
			$remove = ['username', 'email_1', 'email_2', 'password_1', 'password_2'];
			$extra_data = array_diff_key($data, array_flip($remove));
			$extra_data['email'] = $data['email_1'];
			$created = Auth::create($data['username'], $data['password_1'], $extra_data, 0);
			switch ($created)
			{
				case 0:
					return Response::error(500);
				break;
				case 2:
					return Response::error(110);
				break;
				case 1:
					return Response::success("User created succesfully", Auth::user($data['username'])->toArray());
				break;
				default:
					return Response::error(500);
					
			}
		}
		/**
		* uri: /user-auth-api/account/verify/{verificationCode}/
		* description: tries to verify a user account
		*/
		public function put_verify($verificationCode, $lang = 'en_GB')
		{ 
			Logger::debug('starting to verify user account with code ' . $verificationCode)->script(__METHOD__, __LINE__)->save(); 
			$verify = Auth::verify($verificationCode);
			switch ($verify)
			{
				case 4:
					return Response::error(104);
				break;
				case 3:
					return Response::error(500);
				break;
				case 2:
					return Response::error(105);
				break;
				default:	// 1
					Auth::setActive(Users::get_email("verification_code", $verificationCode));
					return Response::success("User verified succesfully");
			}
		}
		/**
		* uri: /user-auth-api/account/new-password-request/
		* description: send an email with a link to reset the user password
		* params: username
		*/
		public function post_new_password_request()
		{
			$case = "user_new_pass_request";
			$inputs = Validator::inputs('_request');
			if ($invalid = Request::check($case,$inputs))
			{
				$errors = $invalid->getErrors();
				return Response::error($invalid->getErrMsgs(key($errors), 
							key(reset($errors))), Request::firstError($invalid));
			}
			$data = App::storage('_req.' . $case);
			if (!$user = \Auth::user(['username' => $data['username']]))
			{
				return Response::error(102);
			}
			if (!$code = \models\User_Control_Links::insert($user->id))
			{
				return Response::error(500);
			}
			$mail_options = \App::options("mail");
			$data = $user->toArray();
			$data['_code'] = $code;
			\models\Mail::sendUserResetPass($mail_options['noreply_address'], $mail_options['_company_name'], $data);
			return Response::success("User password request email sent successfully");
		}
		/**
		* uri: /user-auth-api/account/change-password/{resetLink}/
		* description: send an email with a link to reset the user password
		* params: 
		*/
		public function post_change_password( $resetLink )
		{
		
		}
		/**
		* parameters regex patterns
		*/
		protected static $_where = array
		( 
			//'serviceID'	 	=> '^\d+$' , 				// numeric only
			//'verificationCode' 	=> '^[[:alnum:]]*$' , 		// alphanumeric only
			//'cookie' 			=> '^[[:alnum:]]*$' , 		// alphanumeric only
			//'saleID' 			=> '^[[:alnum:]]*$' 		// alphanumeric only
		);
		/**
		* before filters
		*/
		protected static $_before = array
		( 
			//'put_auto_login' 	=>	'auth.autologin.bp' ,
			//'post_login'		=>	'auth.is_user_blocked|auth.is_account_blocked_bp'
		);
	}
