<?php

	namespace helpers\UserAuthApi\models;

	class Users_Autologin_Tokens extends \Model
	{
		public static function insert($userID, $code = null)
		{
			$code = ( $code ) ? $code : \Auth::random(50);
			$record = new Users_Autologin_Tokens( );
			$record->code = $code;
			$record->user_id = $userID;
			$record->expires = date( 'Y-m-d H:i:s' , strtotime('+' . \App::option('user-auth-api.autologin_expires') . ' day'));
			$record->status = 1;
			return (!$record->save()) ? false : $record;
		}
		
		public static function setExpired($code)
		{
			return User_Control_Links::where( 'code' , '=' , $code )
					->update( array( 'status' => 0 ) )
					->run( );
		}
		
		public static function getUserId($code)
		{
			return Users_Autologin_Tokens::where( 'code' , '=' , $code )
					->where( 'expires' , '>' , \DB::raw( 'NOW()' ) )
					->where( 'status' , '=' , 1 )
					->row( 'user_id' );
		}
	}