<?php

	namespace helpers\UserAuthApi\controllers;
	
	use \helpers\Logger\Manager as Logger;
	use \helpers\Validator\Core as Validator;
	use \Auth;
	use \App;
	use \helpers\UserAuthApi\models\Request;
	use \helpers\UserAuthApi\models\Response;
	use \helpers\UserAuthApi\models\Users;
	use \helpers\UserAuthApi\models\User_Control_Links;
	use \helpers\UserAuthApi\models\Users_Autologin_Tokens;
	use \helpers\UserAuthApi\models\Mail;

	class User
	{
		public static $prefix_uri = '/account';
		/** 
		* uri: /user-auth-api/account/login/
		* description: tries to log user in
		* params: username, password
		* return data: user data
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
					if ($inputs['remember_me'])
					{
						$token = Users_Autologin_Tokens::insert($user['id']);
						$user['autologin_token'] = $token->code;
						$user['autologin_expires'] = $token->expires;
					}
					unset($user['id']);
					return Response::success("User logged in succesfully", $user);
			}
		}
		/** 
		* uri: /user-auth-api/account/auto-login/{code}/
		* description: tries to log in with existing login_token
		* params: see config/validator.php
		* return data: returns user data
		*/
		public function put_auto_login($code)
		{ 
			if (!$user_id = Users_Autologin_Tokens::getUserId($code))
			{ 
				return Response::error(814);
			}
			return Response::success("succefully validated autologin token", Auth::user($user_id)->toArray());
		}
		/** 
		* uri: /user-auth-api/account/register/
		* description: registers a new user to the database
		* params: see config/validator.php
		*/
		public function post_register()
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
			$remove = ['username', 'email', 'password'];
			$extra_data = array_diff_key($data, array_flip($remove));
			$extra_data['email'] = $data['email'];
			$created = Auth::create($data['username'], $data['password'], $extra_data, 0);
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
		public function put_verify($verificationCode)
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
		* uri: /user-auth-api/account/forgot-pass/
		* params: see config/validator.php
		* description: send an email with a link to reset the user password
		* params: username
		*/
		public function post_forgot_password()
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
			if (!$code = User_Control_Links::insert($user->id))
			{
				return Response::error(500);
			}
			$mail_options = \App::options("user-auth-api.mail_config");
			$data = $user->toArray();
			$data['_code'] = $code;
			Mail::sendUserResetPass($mail_options['noreply_address'], 
				\App::options('user-auth-api.mail_tpls_data._company_name'), $data);
			return Response::success("User password request email sent successfully");
		}
		/**
		* uri: /user-auth-api/account/change-password/{resetLink}/
		* description: change user password
		* params: see config/validator.php
		*/
		public function post_change_password($resetLink)
		{
			$case = "user_change_password";
			$inputs = Validator::inputs('_request');
			if ($invalid = Request::check($case,$inputs))
			{
				$errors = $invalid->getErrors();
				return Response::error($invalid->getErrMsgs(key($errors), 
							key(reset($errors))), Request::firstError($invalid));
			}
			if (!$user_id = User_Control_Links::getUserId($resetLink))
			{
				return Response::error(107);
			}
			if (!Auth::password($user_id, $inputs['password']))
			{
				return Response::error(500);
			}
			User_Control_Links::setExpired($resetLink);
			return Response::success("User password has been changed");
		}
		/** 
		* uri: /user-auth-api/account/logout/{autoLoginToken}/
		* description: removes user session data and autologin cookie
		*/			
		public function put_logout($autoLoginToken)
		{
			Users_Autologin_Tokens::setExpired($autoLoginToken);
			return Response::success("User autologin token has been removed from database");
		}
		/**
		* parameters regex patterns
		*/
		protected static $_where = array
		( 
			'verificationCode' 	=> '^[[:alnum:]]*$',		// alphanumeric only
			'resetLink' 		=> '^[[:alnum:]]*$' 		// alphanumeric only
		);
	}
