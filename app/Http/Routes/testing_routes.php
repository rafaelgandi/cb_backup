<?php 
use Respect\Validation\Validator as Valid;

Route::get('/carbon', function () { 
	var_dump(Carbon::now());
	echo 'asdf';
	//return 'test'; 
});

Route::get('/paths', function () {
	// See: http://laravel.com/docs/5.1/helpers#paths
	echo 'url() => '.url('/heheh').'<br>';
	echo 'app_path() => '.app_path('asdfasd').'<br>'; 
	echo 'base_path() => '.base_path().'<br>';

	//App\Helpers::load('app_helpers.php');
	echo ENV_PREFIX.'LOCAL_DB_HOST'.'<br>';
	echo env(ENV_PREFIX.'DB_HOST').'<br>';
	echo config('database.connections.mysql.host').'<br>';
	
	var_dump(Valid::int()->notEmpty()->validate('1'));
	//xplog('hello');
});

Route::get('/get/hostname', function () {
	$hostname = strtolower(trim(gethostname()));
	//echo $hostname;
	echo bcrypt('abc123456');
});

Route::get('/sessions', function () {
	_pr(session()->all());
});

Route::get('/testing', function () {
	$data = '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';

	/* _pr(App\Upload::saveBase64($data, [
		'destination' => public_path('uploads'),
		'extension' => 'jpg',
		'wasabiii.jpg'
	])); */
	//return 'aaaaa';
	//App\Xplog::write('asdfasdf');
	return App\Upload::foo();
});