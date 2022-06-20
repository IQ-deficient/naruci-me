<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user_type = Auth::user()->type;

        if ($user_type == 'Admin') {
            $data = User::when($request->sort_by, function ($query, $value) {
                $query->orderBy($value, request('order_by', 'asc'));
            })
                ->when(!isset($request->sort_by), function ($query) {
                    $query->latest();
                })
                ->when($request->search, function ($query, $value) {
                    $query->where('id', 'LIKE', '%' . $value . '%');
                })
                ->when($request, function ($query) {
                    $query->where('type', 'LIKE', '%Customer%');
                })
                ->paginate($request->page_size ?? 10);

            return Inertia::render('employee/index', [
                'items' => $data,
            ]);
        } else {
            return redirect()->route('restaurants.index')->with('message', [
                'type' => 'error',
                'text' => 'Ne posjedujete privilegije za pristup!',
            ]);
        }
    }

//    public function update(Request $request, Product $product)
//    {
//        $data = $this->validate($request, [
//            'name' => 'required|string',
//            'description' => 'required|string',
//            'price' => 'required|numeric|min:0',
//            'status' => 'required|boolean',
//            'user_id' => 'required|integer',
//            'restaurant_id' => 'required|integer',
//        ]);
//
//        $product->update($data);
//        return redirect()->back()->with('message', [
//            'type' => 'success',
//            'text' => 'Uspjeh!',
//        ]);
//    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->back()->with('message', [
            'type' => 'success',
            'text' => 'Uspjesno uklonjen korisnik iz sistema!',
        ]);
    }


}
