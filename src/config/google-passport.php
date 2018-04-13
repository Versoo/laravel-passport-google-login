<?php

return [
	/*
    |--------------------------------------------------------------------------
    | Application
    |--------------------------------------------------------------------------
    |
    | The Google settings from the Google developer's page
    |
    */
	'applicationName' => env( 'GoogleApplicationName', null ),
	'clientId'        => env( 'GoogleClientId', null ),
	'clientSecret'    => env( 'GoogleClientSecret', null ),
	'developerKey'    => env( 'GoogleDeveloperKey', null ),

	/*
	|--------------------------------------------------------------------------
	| Registration Fields
	|--------------------------------------------------------------------------
	|
	| The name of the fields on the user model that need to be updated,
	| if null, they shall not be updated. (valid for name, first_name, last_name)
	|
	*/

	'registration' => [
		'google_id'  => 'google_id',
		'name'       => 'name',
		'first_name' => 'first_name',
		'last_name'  => 'last_name',
		'email'      => 'email',
		'password'   => 'password',
	],
];
