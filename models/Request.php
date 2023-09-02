<?php

	namespace helpers\UserAuthApi\models;
	
	use \helpers\Logger\Manager as Logger;
	use \helpers\Validator\Core as Validator;

	class Request
	{
		public static function check($case , $inputs = [ ])
		{
			/*if ( $filters = \App::option( 'filters.rules.' . $case ) )
			{ 
				if ( $err = API::setQueryFilters( $filters ) ){ return $err; }
				if ( $err = static::_validate( $case , \App::storage( 'filter' ) , $filters , 'filters' ) ){ return $err; }
			}*/
			$inputs = (!empty($inputs)) ? $inputs :Validator::inputs('_request');
			$rules = \App::option('validator.user-auth-api.rules.' . $case);
			if ($err = static::_validate($case, $inputs, $rules)){ return $err; }
			return null;
		}
		
		public static function duplicate($value, $validator, $type)
		{
			if (is_null($value)){ return true; } // required will fail
			switch ($type)
			{
				case 'email': 
				case 'username': 
					$query = Users::where( $type , '=' , $value )->row( );
					return (!$query) ? true : false;
				break;
				default:
					return false;
			}
			return false;
		}
		
		public static function firstError($validator)
		{
			$errors = $validator->getErrors();
			$rule = key(reset($errors));
			$first_key = key($errors);
			$params = [ ];
			$params['{' . $rule . '}'] = '{' . $validator->getRules($first_key, $rule) . '}';
			$params['{field}'] = '{' . $first_key . '}';
			return $params;
		}
		
		protected static function _cleanValue($value)
		{
			if ('null' === strtolower($value)){ return null; } 
			else if ('false' === strtolower($value)){ return false; } 
			else if ('true' === strtolower($value)){ return true; } 
			return $value;
		}
		
		protected static function _validate($case , $inputs , $rules , $type = 'rules' , $errMsgs = null)
		{
			/* patches */
			foreach ($inputs as $key => $value)
			{ 
				if (is_array($value))
				{
					foreach ($value as $k => $v){ $inputs[$key][$k] = static::_cleanValue($v); }
					continue;
				}
				else{ $inputs[$key] = static::_cleanValue($value); }
			}
			/* end patches */
			$messages = ($errMsgs) ? $errMsgs : \App::option('validator.user-auth-api.error_messages');
			Logger::msg('validating request ' . $type . ' "' . $case . '"')
				->data($rules)->script(__METHOD__ , __LINE__)->save( ); 
			$validator = Validator::make($inputs, $rules, $messages);
			if (!$validator->isValid())
			{
				Logger::msg('failed validating request ' . $type . ' "' . $case . '"')
					->data($validator->getErrors())->script(__METHOD__ , __LINE__)->save(); 
				return $validator;
			}
			\App::storage('_req.' . $case, $validator->getValues());
			Logger::msg('request ' . $type . ' "' . $case . '" validated successfully')
				->data(static::_cleanLogValues($validator->getValues()))
				->script(__METHOD__, __LINE__)
				->save();
			return null;
		}
		
		protected static function _cleanLogValues($data)
		{
			array_walk($data, function(&$value, $key)
			{
				if ($key == "password_1" || $key == "password_2") { $value = "XXXXXX"; }
			});
			return $data;
		}
	}