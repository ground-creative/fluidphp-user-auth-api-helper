<?php

	namespace helpers\UserAuthApi\controllers;
	
	use \helpers\Validator\Core as Validator;
	use \helpers\UserAuthApi\models\Response;
	use \helpers\UserAuthApi\models\Users_Autologin_Tokens;
	use WpOrg\Requests\Requests as Requests;

	class Wrapper
	{
		public static $prefix_uri = '/wrapper';
		/** 
		* uri: /user-auth-api/wrapper/login/
		* description: tries to log user in
		* params: username, password
		* return data: user data
		*/	
		public function post_login( )
		{
			if (session_id() == ''){ session_start(); }
			$inputs = Validator::inputs('_post');
			try
			{
				$request = Requests::post(\helpers\UserAuthApi\Core::getApiUrl() . '/account/login/', [], $inputs);
				$json = json_decode($request->body);
				if ($json->success == true)
				{
					ptc_session_set( 'user.is_loggedin' , true, true);
					ptc_session_set( 'user.data', (array)$json->data, true);
					if ($json->data->autologin_token)
					{
						\Auth::setCookie(\App::option('user-auth-api.autologin_cookie_name'), 
								$json->data->autologin_token, $json->data->autologin_expires, '/');
					}
				}
				return $request->body;
			}
			catch (\Throwable $e)
			{
				return '{"error": 1, "message": "' . $e->getMessage() . '", "code": ' . $e->getCode(). '}';
			}
		}
		/** 
		* uri: /user-auth-api/wrapper/auto-login/{code}/
		* description: tries to log in with existing login_token
		* params: see config/validator.php
		* return data: returns user data
		*/
		public function put_auto_login($code)
		{
			if (session_id() == ''){ session_start(); }
			try
			{
				$request = Requests::put(\helpers\UserAuthApi\Core::getApiUrl() . '/account/auto-login/' . $code . '/');
				$json = json_decode($request->body);
				if ($json->success == true)
				{
					ptc_session_set( 'user.is_loggedin', true, true);
					ptc_session_set( 'user.data', (array)$json->data, true);
				}
			}
			catch (\Throwable $e)
			{
				return '{"error": 1, "message": "' . $e->getMessage() . '", "code": ' . $e->getCode(). '}';
			}
		}
		/** 
		* uri: /user-auth-api/wrapper/logout/
		* description: removes user session data and autologin cookie
		*/			
		public function put_logout()
		{
			if (session_id() == ''){ session_start(); }
			ptc_session_set( 'user.is_loggedin', false, true);
			ptc_session_set( 'user.data', null, true);
			if ($code = \Auth::getCookie(\App::option('user-auth-api.autologin_cookie_name')))
			{
				try
				{
					$request = Requests::put(\helpers\UserAuthApi\Core::getApiUrl() . '/account/logout/' . $code . '/');
					$json = json_decode($request->body);
					if ($json->success == true)
					{
						ptc_session_set( 'user.is_loggedin', true, true);
						ptc_session_set( 'user.data', (array)$json->data, true);
					}
				}
				catch (\Throwable $e)
				{
					return '{"error": 1, "message": "' . $e->getMessage() . '", "code": ' . $e->getCode(). '}';
				}
			}
			\Auth::setCookie(\App::option('user-auth-api.autologin_cookie_name'), 0, 1, '/');
			return Response::success("user logged out successfully");
		}
		/** 
		* uri: /user-auth-api/wrapper/forgot-pass/
		* description: send an email with a link to reset password
		* params: username
		*/			
		public function post_forgot_pass()
		{
			$inputs = Validator::inputs('_post');
			try
			{
				$request = Requests::post(\helpers\UserAuthApi\Core::getApiUrl() . '/account/new-password-request/', [], $inputs);
				$json = json_decode($request->body);
				return $request->body;
			}
			catch (\Throwable $e)
			{
				return '{"error": 1, "message": "' . $e->getMessage() . '", "code": ' . $e->getCode(). '}';
			}
		}
		/**
		* uri: /user-auth-api/wrapper/verify/{verificationCode}/
		* description: tries to verify a user account
		*/
		public function put_verify($verificationCode)
		{
			try
			{
				$request = Requests::put(\helpers\UserAuthApi\Core::getApiUrl() . '/account/verify/' . $verificationCode . '/');
				$json = json_decode($request->body);
				return $request->body;
			}
			catch (\Throwable $e)
			{
				return '{"error": 1, "message": "' . $e->getMessage() . '", "code": ' . $e->getCode(). '}';
			}
		}
		/** 
		* uri: /user-auth-api/wrapper/register/
		* description: send an email with a link to reset password
		* params: see config/validator.php
		*/			
		public function post_register()
		{
			$inputs = Validator::inputs('_post');
			try
			{
				$request = Requests::post(\helpers\UserAuthApi\Core::getApiUrl() . '/account/register/', [], $inputs);
				return $request->body;
			}
			catch (\Throwable $e)
			{
				return '{"error": 1, "message": "' . $e->getMessage() . '", "code": "' . $e->getCode(). '"}';
			}
		}
		/**
		* uri: /user-auth-api/wrapper/change-password/{resetLink}/
		* description: change user password
		* params: see config/validator.php
		*/
		public function post_change_password($resetLink)
		{
			$inputs = Validator::inputs('_post');
			try
			{
				$request = Requests::post(\helpers\UserAuthApi\Core::getApiUrl() . '/account/change-password/' . $resetLink . '/', [], $inputs);
				return $request->body;
			}
			catch (\Throwable $e)
			{
				return '{"error": 1, "message": "' . $e->getMessage() . '", "code": "' . $e->getCode(). '"}';
			}
		}
	}