<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user_type = Auth::user()->type;
        $user_id = Auth::user()->id;

        if ($user_type == 'Admin') {
            $data = Order::when($request->sort_by, function ($query, $value) {
                $query->orderBy($value, request('order_by', 'asc'));
            })
                ->when(!isset($request->sort_by), function ($query) {
                    $query->latest();
                })
                ->when($request->search, function ($query, $value) {
                    $query->where('name', 'LIKE', '%' . $value . '%');
                })
                ->when($request, function ($query) {
                    $query->where('status', 'LIKE', '%0%');
                })
                ->paginate($request->page_size ?? 10);
        } else {
            $data = Order::when($request->sort_by, function ($query, $value) {
                $query->orderBy($value, request('order_by', 'asc'));
            })
                ->when(!isset($request->sort_by), function ($query) {
                    $query->latest();
                })
                ->when($request->search, function ($query, $value) {
                    $query->where('name', 'LIKE', '%' . $value . '%');
                })
                ->when($request, function ($query) {
                    $query->where('status', 'LIKE', '%0%');
                })
                ->when($user_id, function ($query, $value) {
                    $query->where('user_id', 'LIKE', '%' . $value . '%');
                })
                ->paginate($request->page_size ?? 10);
        }

        $order_ids = Order::query()
//            ->where('user_id', '=', $user_id)          // logged user
            ->where('status', '=', 0)          // Completed Orders
            ->pluck('id');

        $carts = Cart::query()
            ->whereIn('order_id', $order_ids)
            ->get()
            ->map(function ($cart) {
                return collect($cart->toArray())
                    ->only(['order_id', 'product_id', 'quantity'])
                    ->all();
            })
            ->map(function ($cart) {
                return data_set($cart, 'product_name',
                    DB::table('products')
                        ->where(
                            'id',
                            '=',
                            $cart['product_id'])
                        ->value('name'),
                    true);
            })
            ->map(function ($cart) {
                return data_set($cart, 'product_price',
                    DB::table('products')
                        ->where(
                            'id',
                            '=',
                            $cart['product_id'])
                        ->value('price'),
                    true);
            });

//        foreach ($carts as $cart) {
//            if ($cart['product_id']) {
//
//            }
//
//        }

        $cart_data = $carts;
        return Inertia::render('Orders', [
            'items' => $data,
            'cart_data' => $cart_data
        ]);

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

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Order $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Order $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Order $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Order $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        //
    }
}
