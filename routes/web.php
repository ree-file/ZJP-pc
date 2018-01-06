<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
/*	\App\Nest::fixTree();
	$result = \App\Nest::withDepth()->having('depth', '<=', 1)->descendantsOf(1)->toArray();
	$nest = \App\Nest::find(1);
	dd($result);
	dd(\App\Nest::withDepth()->with('')->get()->toArray());
	return redirect('/home');*/

/*	$s = collect([new \App\Nest(), new \App\Nest()]);

	$a = $s->map(function ($item, $key) {
		$item->s = 2;
		return $item;
	});

	dd($a->toArray());*/
	\App\Nest::fixTree();
});
