<?php

	namespace helpers\UserAuthApi;
	
	use  \helpers\Validator\Core as Validator;
	use \helpers\Logger\Manager as Logger;
	use \helpers\UserAuthApi\models\Response;
	
	class Core
	{
		public static function getApiUrl()
		{
			$options = \App::options('auth_user_api');
			return $options['url'] . $options['prefix'];
		}
	
		public static function setUp($options)
		{
			# patch log error
			if (!$_SERVER ['HTTP_USER_AGENT']){ $_SERVER ['HTTP_USER_AGENT'] = "none"; }
			//if (false === strpos(!\Router::getUri('path'), $class_vars['prefix_uri'])){ return; }
			$auth_options = array_merge(\App::options('auth'), $options);
			\App::options('user-auth-api' , $auth_options);
			\App::options('validator.user-auth-api', require_once( __DIR__ . '/config/validator.php'));
			Validator::loadConfig(\App::options('validator.user-auth-api'));
			\App::storage('_user_auth_api.session.id', \Auth::random(26));
			if  ($auth_options['set_handlers'] == true){ static::_setHandlers();}
			//static::_setHandlers();
			if ('put' === strtolower($_SERVER['REQUEST_METHOD']) && empty($data))	// patching the put request
			{
				$_REQUEST = Validator::inputs('_put');
			}
			if  (!\HandyMan::getProperty( '\Auth' , '_configured')){ static::_setUpAuth(); }
			//Event::fire('api.maintenance', [ ]); 	// fire maintenance event
			static::_setObservers();
			static::_setRoutes();
		}
		
		protected static function _setObservers()
		{
			Logger::observe('\helpers\UserAuthApi\observers\Logger');
			\Auth::observe('\helpers\UserAuthApi\observers\Users');
		}
	
		protected static function _setUpAuth()
		{
			$options = \App::options('auth');
			//Logger::debug('configuring auth component')->script(__METHOD__, __LINE__)->save( );
			\Auth::configure($options);
			$qb = \DB::getQB(\App::options('user-auth-api.connection_name'));
			if (empty($qb->run('SHOW TABLES LIKE "users"')))
			{
				\Auth::setUp();	
				$qb->run('ALTER TABLE `users` ADD lang varchar(5) DEFAULT NULL, 
											ADD birthdate date DEFAULT NULL;');
			}
			if (empty($qb->run('SHOW TABLES LIKE "log"')))
			{
				$qb->run('CREATE TABLE `log` (`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
						`logtype` varchar(255) DEFAULT NULL,
						`message` varchar(255) DEFAULT NULL,
						`time_create` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
						`data` text,
						`request_data` text,
						`ip_address` varchar(255) DEFAULT NULL,
						`user_agent` text,
						`domain` varchar(255) DEFAULT NULL,
						`request_uri` text,
						`referer` text,
						`method` varchar(255) DEFAULT NULL,
						`line` int(11) NOT NULL,
						`session_id` varchar(255) DEFAULT NULL,
						`user_id` int(11) DEFAULT NULL,
						`request_method` varchar(255) DEFAULT NULL,
						`api_version` varchar(255) DEFAULT NULL,
						PRIMARY KEY (`id`)
				);');
			}
			if (empty($qb->run('SHOW TABLES LIKE "user_control_links"')))
			{
				$qb->run('CREATE TABLE `user_control_links` (`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
						`code` varchar(255) DEFAULT NULL,
						`user_id` int(11) DEFAULT NULL,
						`expires` timestamp DEFAULT NULL,
						`status` tinyint(1) DEFAULT NULL,
						PRIMARY KEY (`id`)
				);');
			}
			if (empty($qb->run('SHOW TABLES LIKE "users_autologin"')))
			{
				$qb->run('CREATE TABLE `users_autologin` (`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
						`code` varchar(255) DEFAULT NULL,
						`user_id` int(11) DEFAULT NULL,
						`expires` timestamp DEFAULT NULL,
						`status` tinyint(1) DEFAULT NULL,
						PRIMARY KEY (`id`)
				);');
			}
		}
		
		protected static function _setRoutes()
		{
			$options = \App::options('user-auth-api');
			\Router::when($options['prefix'] . '/*', function()
			{
				Logger::msg('starting to process new request')->script(__METHOD__, __LINE__)->save(); 
				$options = \App::options('user-auth-api');
				if (\Router::getProtocol() . '://'. $_SERVER[ 'HTTP_HOST' ] != $options['url'])
				{
					echo Response::error(401);
					return true;
				}
			} );
			\Router::group('user-auth-api' ,function()
			{
				\Router::controller(controllers\User::$prefix_uri, '\helpers\UserAuthApi\controllers\User'); 
				\Router::notFound(404, function()
				{
					if (false !== strpos(\Router::getUri('path'), \App::options('user-auth-api.prefix')))
					{
						echo Response::error(404);
						return true; 
					}
				}, 0);
				
			})->prefix($options['prefix']);
		}
		
		protected static function _setHandlers()
		{
			//Logger::debug('setting user-auth-api handlers')->script(__METHOD__, __LINE__)->save( );
			set_error_handler(['helpers\UserAuthApi\observers\ErrorHandler', 'error_handler']);
			set_exception_handler(['helpers\UserAuthApi\observers\ErrorHandler', 'exception_handler']);
		}
	}