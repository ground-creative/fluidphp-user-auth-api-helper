<?php

	namespace helpers\UserAuthApi\observers;
	
	use \helpers\Validator\Core as Validator;

	class Logger
	{
		public static function inserting($data)
		{
			/*if ( !\App::option( 'app.test_env' ) && 
				'get' == strtolower( $_SERVER[ 'REQUEST_METHOD' ] ) ) // disable logging for all get requets
			{
				$data->reset( );
				return false;
			}
			if ( $api_client_id = \App::storage( 'api_client.client_id' ) )
			{ 
				$data->id_api = $api_client_id; 
			}*/
			if ($user_id = \App::storage('login_user.id'))
			{ 
				$data->user_id = $user_id; 
			}
			$remote_ip = (Validator::inputs('_request.remote_ip')) ? 
							Validator::inputs('_request.remote_ip') : \Debug::getClientIP();
			if ($remote_ip)
			{ 
				$data->ip_address = $remote_ip; 
			}
			$data->request_method = $_SERVER['REQUEST_METHOD']; 
			$data->api_version = \App::option('revision.number'); 
			$data->session_id = \App::storage('_user_auth_api.session.id'); 
		}	
	}