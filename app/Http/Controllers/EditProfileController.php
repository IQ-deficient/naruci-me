<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Illuminate\Validation\Rules;
use function PHPUnit\Framework\isEmpty;

class EditProfileController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {

        $user_type = Auth::user()->type;
        $user_id = Auth::user()->id;

        $user = DB::table('users')
            ->where('id', '=', $user_id)
            ->first();

        return Inertia::render('EditProfile', [
            'items' => $user,
        ]);

    }


    public function update(Request $request)
    {

        $same_email = collect(DB::table('users')
            ->where('id', '=', $request->id)
            ->where('email', '=', $request->email)
            ->first());


        if (array_key_exists('email', $same_email->toArray())) {
            $data = $this->validate($request, [
                'id' => 'required|integer',
                'name' => 'nullable|string|max:255',
                'email' => 'nullable|string|email|max:255',
                'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
                'phone_number' => 'nullable|numeric',
            ]);
        } else {
            $data = $this->validate($request, [
                'id' => 'required|integer',
                'name' => 'nullable|string|max:255',
                'email' => 'nullable|string|email|max:255|unique:users',
                'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
                'phone_number' => 'nullable|numeric',
            ]);
        }

        if ($data['password'] == ''){
            DB::table('users')
                ->where('id', '=', $request->id)
                ->update([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'phone_number' => $data['phone_number'],
                    'updated_at' => Carbon::now(),
                ]);
        }else{
            DB::table('users')
                ->where('id', '=', $request->id)
                ->update([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make($data['password']),
                    'phone_number' => $data['phone_number'],
                    'updated_at' => Carbon::now(),
                ]);
        }

        return redirect()->back()->with('message', [
            'type' => 'success',
            'text' => 'Upjesno izmijenjene korisnicke informacije!',
        ]);
    }
}
