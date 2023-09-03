<?php

	/**
	* Request Validator Config FIle 
	*/
	return 
	[
		/* autoload method */
		//'_load'		=>	'\helpers\Validator\Core::loadConfig' ,
		/* Response error codes */
		'error_messages'		=>	
		[
			'required'				=>	501,
			'email'				=>	102,
			'equalTo'				=>	101,
			'match'				=>	300,
			'min'				=>	802,
			'max'				=>	803,
			'number'				=>	502,
			'duplicate'			=>	100,
			'password'			=>	801,
			'login_token'			=>	804
		] ,
		/* Values regular expressions */
		'regex'			=>	
		[
			'lang'				=>	'~en_GB~' ,
			'birthdate'			=>	'~^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$~' ,
			//'agreement'			=>	'~on~' ,
			'gender'				=>	'~male|female~'
		] ,
		/* Defaults if value is not set */
		'defaults'			=>	array
		(
			'lang'				=>	'en_GB' ,
			'birthdate'			=>	'_NULL_',
			'remember_me'		=>	'_NULL_'
		) ,
		/* Defaults if value is empty */
		'empty'			=>	array
		(
		
		) ,
		/* Custom validation methods */
		'custom_methods'	=>	array
		(
			'duplicate'			=>	'\helpers\UserAuthApi\models\Request::duplicate'
		) ,
		/* Rules */	
		'rules'			=>	
		[
			/* Register new login user */
			'user_register'			=>	
			[
				'firstname' 			=>	'required' ,
				'lastname' 			=>	'required' ,
				'username' 			=>	'required||duplicate:username||min:6' ,
				'email' 				=>	'required||email||duplicate:email' ,			
				'password'  			=>	'required||min:5||max:20' ,
				//'lang'				=>	'match:{regex}||default:{defaults}||empty:en_GB',		regex is failing here
				//'birthdate'			=>	'match:{regex}||default:{defaults}||empty:1980-01-01' 	regex is failing here
				'lang'				=>	'default:{defaults}||empty:en_GB',
				'birthdate'			=>	'default:{defaults}||empty:1980-01-01'
			] ,
			'user_login'			=>	
			[
				'username' 			=>	'required' ,
				'password'  			=>	'required',
				'remember_me'		=>	'default:{defaults}'
			],
			'user_new_pass_request'	=>
			[
				'username' 			=>	'required'
			],
			'user_change_password'	=>
			[
				'password'  			=>	'required||min:5||max:20'
			]
		]
	];