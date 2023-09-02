<?php

	namespace helpers\UserAuthApi\models;
	
	use \helpers\Logger\Manager as Logger;

	class Response
	{
		public static function error($code, $params = [ ], $msg = null)
		{
			if (!isset(static::$_errorCodes[$code]))
			{
				$message = $code;
				$code = 999;
			}
			else
			{
				$message = static::$_errorCodes[$code];
			}
			if (!empty($params)) // add validator params
			{
				$keys = array_keys($params);
				foreach ($keys as $k => $v)
				{
					$keys[$k] = '{'  . $v  . '}';
					$keys[$k] = str_replace(['{{', '}}'], ['{' , '}'] , $keys[$k]);
				}
				$values = array_values($params);
				foreach ($values as $k => $v)
				{
					//$values[ $k ] = '{'  . $v  . '}';
                                    $values[$k] = $v  ;
				}
				$message = str_replace($keys, $values, $message);
			}
			if ($msg !== null){ $message = $msg; }
			Logger::warning('response returned error code: ' . $code)
				->data($message)->script(__METHOD__, __LINE__)->save( );
			$return_data = array
			( 
				'success' 			=>	false ,
				'error'			=>	true ,
				'message'		=>	$message ,	
				'code'			=>	$code
			);
			//return (\Debug::isLoaded() || \App::storage('patch_json')) ? 
			//		ptc_json($return_data, null, false) : ptc_json($return_data);
			return ptc_json($return_data);
		}
		
		public static function success($msg , $data = null)
		{
			//$return_data = array('success' => true , 'message' => $msg );
			//if ($data){ $return_data['data'] = $data; }
			Logger::msg('api sent response message "' . $msg . '"')
				->data($data)->script( __METHOD__ , __LINE__)->save();
			//return (\Debug::isLoaded()) ? ptc_json($return_data, null, false) : ptc_json($return_data);
			$return_data = array
			( 
				'success' 			=>	true ,
				'error'			=>	false ,
				'message'		=>	$msg ,	
				'data'			=>	$data,
				'code'			=>	200
			);
			return ptc_json($return_data);
		}
		
		protected static $_errorCodes = array
		(
			100	=>	'duplicate value found in database for parameter {field}' ,
			101	=>	'parameter {field} must be equal to parameter {equalTo}' ,
			102	=>	'invalid email address for parameter {field}' ,
			103	=>	'invalid of expired affiliate code' ,
			104	=>	'this account has been already verified' ,
			105	=>	'verification code not found in database' ,	
			106	=>	'value for parameter {field} does not exist in database table' ,		
			107	=>	'invalid or expired user control link' ,	
			108	=>	'invalid scope value {param}' ,
			109	=>	'invalid filter rule {param}' ,
			110	=>	'username already exists' ,
			111	=>	'account is not verified' ,
			300	=>	'invalid value for parameter {field}. Values are based on pattern {match}' ,
			301	=>	'invalid query filter for parameter {field}. Values are based on pattern {match}' ,
			306	=>	'invalid datetime for {field} parameter, accepted format is Y-m-d H:i:s' ,
			400	=>	'could not find list with supplied id and code' ,
			401	=>	'user is unauthorized to perform this action' ,
			404	=>	'no webservice is associated with this uri' ,
			405	=>	'http protocol is not supported, please use https' ,
			500	=>	'some error occurred' ,
			501	=>	'required parameter {field} is missing or empty' ,
			502	=>	'value for parameter {field} must be numeric' ,
			503	=>	'user not found' ,
			504	=>	'account is disabled' ,
			511	=>	'incorrect authentication signature' ,
			512	=>	'unauthorized' ,
			514	=>	'request has expired' ,
			517	=>	'query filter parameter error: {error}' ,
			712	=>	'this ip address has been blocked, to many failed login attempts' ,
			713	=>	'this account has been blocked temporarily, please try again in a few minutes. account is blocked untill {time}' ,
			714	=>	'this account has been blocked, please contact admnistrator. account is blocked untill {time}' ,
			801	=>	'invalid username or password' ,
			802	=>	'invalid min number of characters for {field}. MInimum is {min}' ,
			803	=>	'invalid max number of characters for {field}. MInimum is {max}' ,
			804	=>	'invalid or expired login token' ,
			805	=>	'sale id not found' ,
			807	=>	'transaction failed and has been logged' ,			
			808	=>	'invalid password' ,			
			813	=>	'invalid max number of characters for {field}. Maximum is {max}' ,		
			814	=>	'autologin cookie is expired or invalid' ,					
			821	=>	'this sale has been cancelled' ,			
			823	=>	'this sale has been paid already' ,				
			901	=>	'no user found with the given facebook id' ,
			919	=>	'missing param "remote_address" is required for all API requests',
			921	=>	'This user is in the black list' ,
			926  =>  'sale data not found'
		);
	}