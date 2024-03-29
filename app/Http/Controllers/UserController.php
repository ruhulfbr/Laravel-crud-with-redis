<?php

namespace App\Http\Controllers;

use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    private $view_prefix = 'users.';
    private $per_page = 10;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $page = "1";
        if (!empty($request->input('page'))) {
            $page = $request->input('page');
        }

        $users = Cache::remember('users_' . $page, 600, function () {
            $result = User::paginate($this->per_page);
            Cache::set('users_last_page', $result->lastPage());
            return $result;
        });

        return $users;
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $faker = Faker::create();

        $user = new User();
        $user->name = $faker->name;
        $user->email = $faker->unique()->safeEmail;
        $user->password = Hash::make('password');

        $user->save();
    }

    /**
     * Display the specified resource.
     */
    public function show(mixed $id)
    {
        $user = Cache::remember('user_details_' . $id, 600, function () {
            return User::find($id);
        });

        return $user;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, mixed $id)
    {
        // Forget Cache For user paginated List
        $this->forgetCaches();
        Cache::forget('user_details_' . $id);

        $faker = Faker::create();

        $user = User::findOrFail($id);
        $user->name = $faker->name;
        $user->email = $faker->unique()->safeEmail;
        $user->save();

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(mixed $id)
    {
        // Forget Cache For user paginated List
        $this->forgetCaches();
        Cache::forget('user_details_' . $id);

        $user = User::findOrFail($id);
        $user->delete();
    }

    // Forget Cache For user paginated List
    public function forgetCaches(string $prefix = 'users_'): void
    {
         $lastPage = Cache::get('users_last_page');
        for ($i = 1; $i < $lastPage; $i++) {
            $key = $prefix . $i;
            if (Cache::has($key)) {
                Cache::forget($key);
            }
        }
    }
}
