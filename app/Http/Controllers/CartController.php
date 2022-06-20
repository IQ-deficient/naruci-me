<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CartController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function orderCartItems(Request $request)
    {
        $user_type = Auth::user()->type;
        $user_id = Auth::user()->id;

        if ($request->type == 'cash') {
            $data = $this->validate($request, [
                'address' => 'required',
                'type' => 'required',
            ]);
        } else {
            $data = $this->validate($request, [         // if we are going to be storing credit card info
                'address' => 'required',
                'type' => 'required',
            ]);
        }


        DB::table('orders')
            ->where('user_id', '=', $user_id)
            ->where('status', '=', 1)
            ->update([
                'status' => 0,
                'updated_at' => Carbon::now()
            ]);

        return redirect()->back()->with('message', [
            'type' => 'success',
            'text' => 'Porudzbina uspjesna. Pratite je u istoriji porudzbina!',
        ]);

    }

    public function index(Request $request)
    {
        $user_type = Auth::user()->type;
        $user_id = Auth::user()->id;

        if ($user_type == 'Admin') {
            return redirect()->route('home');
        }

        $all_products = Product::all();

        $bill = DB::table('orders')
            ->where('user_id', '=', $user_id)
            ->where('status', '=', 1)
            ->first();

        $user_active_order = Order::query()
            ->where('user_id', '=', $user_id)
            ->where('status', '=', 1)
            ->pluck('id');

        if (!empty($user_active_order->toArray())) {
            $data = Cart::when($request->sort_by, function ($query, $value) {
                $query->orderBy($value, request('order_by', 'asc'));
            })
                ->when(!isset($request->sort_by), function ($query) {
                    $query->latest();
                })
                ->when($request->search, function ($query, $value) {
                    $query->where('name', 'LIKE', '%' . $value . '%');
                })
                ->when($user_active_order, function ($query, $value) {
                    $query->where('order_id', 'LIKE', '%' . $value[0] . '%');
                })
                ->paginate($request->page_size ?? 10);

            return Inertia::render('Cart', [
                'items' => $data,
                'products' => $all_products,
                'bill' => $bill
            ]);
        } else {
            return Inertia::render('Cart', [
                'items' => [],
                'products' => $all_products,
                'bill' => 0
            ]);
        }
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
     * @param \App\Models\Cart $cart
     * @return \Illuminate\Http\Response
     */
    public function show(Cart $cart)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Cart $cart
     * @return \Illuminate\Http\Response
     */
    public function edit(Cart $cart)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Cart $cart
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Cart $cart)
    {
        //
    }

    public function destroy(Cart $cart, Request $request)
    {
        $user_id = Auth::user()->id;

        $data = $this->validate($request, [
            'quantity' => 'required|integer',
            'product_id' => 'required|integer',
        ]);

        $order = DB::table('orders')
            ->where('user_id', '=', $user_id)
            ->where('status', '=', 1)
            ->first();

        $product_price = DB::table('products')
            ->where('id', '=', $data['product_id'])
            ->pluck('price');

        $billMinus = $product_price[0] * $data['quantity'];

        DB::table('orders')
            ->where('user_id', '=', $user_id)
            ->where('status', '=', 1)
            ->update([
                'bill' => $order->bill - $billMinus,
                'updated_at' => Carbon::now()
            ]);

        $cart->delete();
        return redirect()->back();
    }
}
