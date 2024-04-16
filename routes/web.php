<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;

use Junges\Kafka\Contracts\ConsumerMessage;
use Junges\Kafka\Contracts\MessageConsumer;
use Junges\Kafka\Facades\Kafka;
use Junges\Kafka\Message\Message;

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


Route::get('product', [ProductController::class, 'index']);

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

Route::get('/kafka-publish', function () {
    $user = User::find(1);
    $message = new Message(
        headers: ['header-key' => 'header-value'],
        body: json_encode($user),
        key: 'kafka key here'
    );

    $producer = Kafka::publish('broker')->onTopic('topic')->withMessage($message);
    $producer->send();
});

Route::get('/kafka-consume', function () {
    $consumer = Kafka::consumer();

    // Using callback:
    $consumer->withHandler(function(ConsumerMessage $message, MessageConsumer $consumer) {
        // Handle your message here
        print_r($message->getBody());

        Log::info('Consumer started');
    });
});

Route::resource('users', UserController::class)->except(['delete']);
