<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Third Party Services
	|--------------------------------------------------------------------------
	|
	| This file is for storing the credentials for third party services such
	| as Stripe, Mailgun, Mandrill, and others. This file provides a sane
	| default location for this type of information, allowing packages
	| to have a conventional place to find your various credentials.
	|
	*/

	'mailgun' => [
		'domain' => '',
		'secret' => '',
	],
    'almaty' =>[
        'host'=>'komandirtaxi.no-ip.biz',
        'port'=>'8089',
        'secret_key'=>'65536',
        'wiauser'=>'taxi_admin',
        'wiapass'=>'london69xxxal',
        'wiatoken'=>'a2046b066f63bdc7934839531ddd36cdAC02DE41FA1415EA26DF88790A82C66FFD8040D3',
        'local_db'=>'almaty',
        'firebird_db'=>'almaty_firebird',
    ],
	'mandrill' => [
		'secret' => '',
	],

	'ses' => [
		'key' => '',
		'secret' => '',
		'region' => 'us-east-1',
	],

	'stripe' => [
		'model'  => 'User',
		'secret' => '',
	],

];
