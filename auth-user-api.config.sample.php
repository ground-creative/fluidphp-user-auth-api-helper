<?php

	return
	[
		'_load'					=>	'\helpers\UserAuthApi\Core::setUp',
		'url'						=>	$_ENV['USER_AUTH_APP_URL'],	// use localhost
		'prefix'					=>	$_ENV['APP_ENV'] . '/user-auth-api',
		'set_handlers'				=>	true, 
		'main_app_domain'			=>	$_ENV['MAIN_APP_DOMAIN'],
		'autologin_expires'			=>	'30', // days
		'autologin_cookie_name'		=>	'_autologin',
		'validator_file'				=>	ptc_path('root') . '/vendor/fluidphp/helpers/UserAuthApi/config/validator.php',
		'mail_tpls_data'	=>
		[
			"_company_name"		=>	$_ENV['EMAIL_COMPANY_ADDRESS'],
			"_noreply_email"		=>	$_ENV['EMAIL_NOREPLY_ADDRESS'],
			"_contact_email"		=>	$_ENV['EMAIL_CONTACT_ADDRESS'],
			"_app_domain"		=>	$_ENV['MAIN_APP_DOMAIN']
		],
		'mail_config'		=>
		[
			'log'						=>	ptc_path('storage' ) . '/smtp.log',
			'secure'					=>	$_ENV['MAILER_SECURE'],
			'port'					=>	$_ENV['MAILER_PORT'],
			'address'					=>	$_ENV['MAILER_ADDRESS'],
			'username'				=>	$_ENV['MAILER_USERNAME'],
			'password'				=>	$_ENV['MAILER_PASSWORD'],
			'debug_level'				=>	$_ENV['MAILER_DEBUG_LEVEL'],
			'company_address'			=>	$_ENV['EMAIL_COMPANY_ADDRESS'],
			'noreply_address'			=>	$_ENV['EMAIL_NOREPLY_ADDRESS']
		]
	];