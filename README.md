# Laravel-crud-with-redis

<pre>
<h2>1. Redis server: install/launch</h2>
Redis is not a Laravel-specific system, it is installed separately, just follow its official installation instructions.

Then, just launch it with the command redis-server.

<h2>2. Install Redis PHP Extension </h2>
Ensure you have the Redis PHP extension installed:

For Ubuntu based systems:

sudo apt-get install redis php8.1-redis

sudo systemctl restart php8.1-fpm.service

<h2>3. Install Predis Package </h2>
Predis is a flexible and feature-complete Redis client for PHP. Install it via Composer:

composer require predis/predis

<h2>4. Configure Redis in Laravel</h2>
Change the CACHE_STORE to redis in the .env file:

CACHE_STORE=redis

Edit the .env file to change Redis server configuration if not using the default values:

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

<h2>5. Use Redis in Laravel for Caching</h2>
<b>5.1 Cache an Eloquent Query</b>
Suppose you have an Eloquent query like this:

$users = User::where('active', 1)->get();

To cache this query result with Redis:

use Illuminate\Support\Facades\Cache;
 
$users = Cache::remember('active_users', 60, function () {
    return User::where('active', 1)->get();
});

Here, 'active_users' is the cache key, 60 is the number of seconds to cache the result, and the closure fetches the users when the cache is not found.

<b>5.2 Clear Cache</b>
To clear the cached result:

use Illuminate\Support\Facades\Cache;
 
Cache::forget('active_users');

</pre>


