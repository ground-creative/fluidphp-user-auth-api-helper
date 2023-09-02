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

3 - Create "logger.php" file in app/config folder and copy the code from "logger.config.sample.php"

4 - Configure "auth.php" in app/config to add the api user model
```
'model'	=>	'\helpers\UserAuthApi\models\Users',	
```

5- Move email-templates folder in app/views

# Endpoints