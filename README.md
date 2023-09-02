# FluidPhp User Authentication Api Helper

This is a helper for fluidphp framework, to authenticate users on your website or backend panel

## Installation

1 - Add the package to your composer.json to install the helpers
```
"require": 
{
	"mnsami/composer-custom-directory-installer": "2.0.*" ,
	"fluidphp/user-auth-api-helper": "^1.0.0"
} ,
"extra": 
{
	"installer-paths": 
	{
		"./vendor/fluidphp/helpers/Translator": ["fluidphp/translator-helper"] ,
		"./vendor/fluidphp/helpers/Validator": ["fluidphp/validator-helper"],
		"./vendor/fluidphp/helpers/Logger": ["fluidphp/logger-helper"],
		"./vendor/fluidphp/helpers/EmailManager": ["fluidphp/emailmanager-helper"],
		"./vendor/fluidphp/helpers/UserAuthApi": ["fluidphp/user-auth-api-helper"]
	}
}
```

2 - Create "user-auth-api.php" config file in app/config folder and copy the code from "user-auth-api.config.sample.php"

3 - Create "logger.php" file in app/config folder and add your configuration if not present already
```
return
[
	'develop'	=>
	[
		'connection'	=>	'default' ,
		'table'		=>	'log'
	] ,
	'prod'	=>
	[
		'connection'	=>	'default' ,
		'table'		=>	'log'
	]
];
```
4 - Configure "auth.php" in app/config to add the api user model
```
'model'	=>	'\helpers\UserAuthApi\models\Users',	
```

5- Move email-templates folder in app/views

## Usage

### Using protected pages filter

Add the filter "_user-auth-api.check_login" to all the routes that require access to authenticated users

### Using the api wrapper

The api wrapper is a convenient way to make the calls from js directly.<br />
To call the api wrapper u can use any request library
```
$app = \App::option('app');
try
{
	$request = Requests::post($app['url'] . $app['env'] . '/wrapper/reset-password/', [], ['username' => 'some_user_name']);
	return $request->body;
}
catch (\Throwable $e)
{
	return '{"error": 1, "message": "' . $e->getMessage() . '", "code": ' . $e->getCode(). '}';
}
```

### Calling the authentication api 

The authentication api accepts only calls from the specified domain set with the parameter "url" in the config file user-auth-api.php.<br />
To call the authentication api u can use any request library
```
$app = \App::option('app');
try
{
	$request = Requests::put($app['url'] . $app['env'] . '/account/asutologin/'. $code . '/');
	return $request->body;
}
catch (\Throwable $e)
{
	return '{"error": 1, "message": "' . $e->getMessage() . '", "code": ' . $e->getCode(). '}';
}
```

## Endpoints

### Wrapper

### API