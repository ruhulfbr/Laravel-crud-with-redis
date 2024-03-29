<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function (Request $request) {

   // Cache::flush();

    $driver = Cache::getDefaultDriver();
    echo $driver; // This will output the name of the default cache driver


    DB::enableQueryLog();

    $page = "1";
    If (!empty($request->input('page'))) {
        $page = $request->input('page');
    }

    $users = Cache::remember('users_'.$page, 600, function () {
        $data = User::paginate(5);
        Cache::set('users_last_page', $data->lastPage());

        return $data;
    });

    echo "<pre>";
    print_r(DB::getQueryLog());
    print_r($users->toArray());

    echo "Last Page = ".Cache::get('users_last_page').PHP_EOL;
    exit();

    return view('welcome', ['users' => $users]);
});

Route::get('/redis-subscribe', function () {
    Redis::publish('test-channel', json_encode([
        'name' => 'Adam Wathan'.time()
    ]));
});

Route::resource('users', UserController::class)->except(['delete']);
