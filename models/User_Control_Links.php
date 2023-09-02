<?php
	
	namespace helpers\UserAuthApi\models;

	class User_Control_Links extends \Model
	{
		public static function getUserId($code)
		{
			return User_Control_Links::where( 'code' , '=' , $code )
					->where( 'expires' , '>' , \DB::raw( 'NOW()' ) )
					->where( 'status' , '=' , 1 )
					->row( 'user_id' );
		}
		
		public static function insert($userID, $code = null)
		{
			$code = ( $code ) ? $code : \Auth::random( 50 );
			$record = new User_Control_Links( );
			$record->code = $code;
			$record->user_id = $userID;
			$record->expires = date( 'Y-m-d H:i:s' , strtotime( '+1 day' ) );
			$record->status = 1;
			return (!$record->save()) ? false : $code;
		}
		
		public static function setExpired($code)
		{
			return User_Control_Links::where( 'code' , '=' , $code )
					->update( array( 'status' => 0 ) )
					->run( );
		}
	}