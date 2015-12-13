<?php

return [

	/*
	|--------------------------------------------------------------------------
	| PDO Fetch Style
	|--------------------------------------------------------------------------
	|
	| By default, database results will be returned as instances of the PHP
	| stdClass object; however, you may desire to retrieve records in an
	| array format for simplicity. Here you can tweak the fetch style.
	|
	*/

	'fetch' => PDO::FETCH_CLASS,

	/*
	|--------------------------------------------------------------------------
	| Default Database Connection Name
	|--------------------------------------------------------------------------
	|
	| Here you may specify which of the database connections below you wish
	| to use as your default connection for all database work. Of course
	| you may use many connections at once using the Database library.
	|
	*/

	'default' => 'almaty',

	/*
	|--------------------------------------------------------------------------
	| Database Connections
	|--------------------------------------------------------------------------
	|
	| Here are each of the database connections setup for your application.
	| Of course, examples of configuring each database platform that is
	| supported by Laravel is shown below to make development simple.
	|
	|
	| All database work in Laravel is done through the PHP PDO facilities
	| so make sure you have the driver for your particular database of
	| choice installed on your machine before you begin development.
	|
	*/

	'connections' => [
		'almaty' => [
			'driver'    => 'mysql',
			'host'      => env('localhost', 'localhost'),
			'database'  => env('ecotaxik_almaty', 'ecotaxik_almaty'),
			'username'  => env('root', 'root'),
			'password'  => env('', ''),
			'charset'   => 'utf8',
			'collation' => 'utf8_unicode_ci',
			'prefix'    => '',
			'strict'    => false,
		],
        'astana' => [
            'driver'    => 'mysql',
            'host'      => env('localhost', 'localhost'),
            'database'  => env('ecotaxik_astana', 'ecotaxik_astana'),
            'username'  => env('root', 'root'),
            'password'  => env('', ''),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ],
        'firebird_almaty'=> [
            'driver'    => 'firebird',
            'host'      => 'komandirtaxi.no-ip.biz/22432',
            'database'  => 'taxialmaty',
            'username'  => 'SYSDBA',
            'password'  => 'admin',
            'charset'   => 'cp1251',
            'collation' => '',
            'prefix'    => '',
        ],
        'firebird_astana'=> [
            'driver'    => 'firebird',
            'host'      => 'komandirtaxi.no-ip.biz/22431',
            'database'  => 'taxiastana',
            'username'  => 'SYSDBA',
            'password'  => 'admin',
            'charset'   => 'cp1251',
            'collation' => '',
            'prefix'    => '',
        ]
	],

	/*
	|--------------------------------------------------------------------------
	| Migration Repository Table
	|--------------------------------------------------------------------------
	|
	| This table keeps track of all the migrations that have already run for
	| your application. Using this information, we can determine which of
	| the migrations on disk haven't actually been run in the database.
	|
	*/

	'migrations' => 'migrations',

	/*
	|--------------------------------------------------------------------------
	| Redis Databases
	|--------------------------------------------------------------------------
	|
	| Redis is an open source, fast, and advanced key-value store that also
	| provides a richer set of commands than a typical key-value systems
	| such as APC or Memcached. Laravel makes it easy to dig right in.
	|
	*/

	'redis' => [

		'cluster' => false,

		'default' => [
			'host'     => '127.0.0.1',
			'port'     => 6379,
			'database' => 0,
		],

	],

];
