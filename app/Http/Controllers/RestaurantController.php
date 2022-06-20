<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class RestaurantController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {

        $data = Restaurant::when($request->sort_by, function ($query, $value) {
            $query->orderBy($value, request('order_by', 'asc'));
        })
            ->when(!isset($request->sort_by), function ($query) {
                $query->latest();
            })
            ->when($request->search, function ($query, $value) {
                $query->where('name', 'LIKE', '%' . $value . '%');
            })
            ->paginate($request->page_size ?? 10);

        return Inertia::render('Restaurants', [
            'items' => $data,
        ]);

//        $restaurants = Restaurant::all();
//
//        return Inertia::render('Restaurants', [
//            'restaurants' => $restaurants,
//        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $data = $this->validate($request, [
            'name' => 'required|string',
            'address' => 'required|string',
            'status' => 'required|boolean',
            'user_id' => 'required|integer'
        ]);

        Restaurant::create($data);
        return redirect()->back()->with('message', [
            'type' => 'success',
            'text' => 'Uspjesno unesen restoran!',
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Restaurant $restaurant
     * @return \Illuminate\Http\Response
     */
    public function show(Restaurant $restaurant)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Restaurant $restaurant
     * @return \Illuminate\Http\Response
     */
    public function edit(Restaurant $restaurant)
    {
        //
    }

    public function update(Request $request, Restaurant $restaurant)
    {
        $data = $this->validate($request, [
            'name' => 'required|string',
            'address' => 'required|string',
            'status' => 'required|boolean',
            'user_id' => 'required|integer'
        ]);
        $restaurant->update($data);
        return redirect()->back()->with('message', [
            'type' => 'success',
            'text' => 'Uspjesno izmijenjeni podaci za restoran!',
        ]);
    }

    public function destroy(Restaurant $restaurant, Request $request)
    {
        DB::table('products')
            ->where('restaurant_id', '=', $request->id)
            ->update(
                ['restaurant_id' => null]           // to avoid constraint violation for now
            );

        $restaurant->delete();
        return redirect()->back()->with('message', [
            'type' => 'success',
            'text' => 'Uspjesno ste uklonili restoran iz ponude!',
        ]);
    }
}
