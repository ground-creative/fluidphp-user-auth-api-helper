<?php

	namespace helpers\UserAuthApi\observers;
	
	use \helpers\Logger\Manager as Logger;

	class ErrorHandler
	{
		public static function error_handler($errno, $errstr, $errfile, $errline) 
		{
			if (error_reporting() === 0)	// errors are suppressed with @
			{
				return;
			}
			$error = \Debug::msgType($errno);
			Logger::error($error .  ': ' . $errstr . ' in ' . $errfile . ':' . $errline)
				->script(__METHOD__, __LINE__)
				->save();
			error_log($error .  ': ' . $errstr . ' in ' . $errfile . ':' . $errline, 0);
			if (getenv('DIE_ON_ERROR') && 
				\Debug::msgType($errno) == 'Php Error') { die( ); }
			//return true;	// don't execute php error handler
		}
		
		public static function exception_handler($exception) 
		{
			Logger::error("PHP Fatal error: " . $exception->getMessage() . " in " . 
							$exception->getFile() . ':' .  $exception->getLine())
				->data($exception->getTraceAsString())
				->script(__METHOD__, __LINE__)
				->save();
			error_log("PHP Fatal error: " . $exception->getMessage() . " in " . 
						$exception->getFile() . ':' .  $exception->getLine() . 
										PHP_EOL . "Stack trace:" . PHP_EOL . 
											$exception->getTraceAsString(), 0);
			//return true;
		}
	}