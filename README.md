# TotemSDK
Provides REST API access to Totem application instance.

TotemSDK requires PHP 5.6 or later.

## Basic Useage

```php
try
{
	// Totem API access initialization.
	$totem = new Totem(
		'user@email.com', 
		'xxxxx-API_KEY-xxxxx', 
		'http://localhost/ToTem/api'
	);
	
	// We want to get all our pipelines (in JSON).
	echo $totem->call(
		[
			'resource' => 'pipelines'
		], 
		Totem::ACTION_GET
	);
}
// Something went wrong - we print the error message.
catch (TotemException $e) 
{
	echo 'API call error: ' . $e->getMessage();
}
```