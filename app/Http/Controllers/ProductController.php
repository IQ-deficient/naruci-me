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

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function addToCart(Request $request, Product $product)
    {

        $data = $this->validate($request, [
            'product_id' => 'required|integer',
            'price' => 'required|numeric|min:0',
            'status' => 'required|boolean',
            'user_id' => 'required|integer',
            'restaurant_id' => 'required|integer',
            'quantity' => 'required|numeric|min:1',
        ]);

        $user_order = DB::table('orders')
            ->where('status', 1)
            ->where('user_id', $data['user_id'])
            ->get();

        if ($user_order->isEmpty()) {
            Order::query()->create([
                'user_id' => $data['user_id'],
                'bill' => 2,                    // initial euros for final price
                'status' => 1,                  // created (not yet ordered)
            ]);
        }

        $user_order = DB::table('orders')
            ->where('status', 1)
            ->where('user_id', $data['user_id'])
            ->first();

        Cart::query()->create([
            'product_id' => $data['product_id'],
            'order_id' => $user_order->id,
            'quantity' => $data['quantity'],
        ]);

        $addToBill = $data['price'] * $data['quantity'];

        DB::table('orders')
            ->where('status', 1)
            ->where('user_id', $data['user_id'])
            ->update([
                'bill' => $user_order->bill + $addToBill,
                'updated_at' => Carbon::now()
            ]);

        return redirect()->back()->with('message', [
            'type' => 'success',
            'text' => 'Uspjesno dodavanje u korpu!',
        ]);
    }

    public function index(Request $request)
    {
        $user_type = Auth::user()->type;

        $restaurant_status = DB::table('restaurants')
            ->where('id', $request->restaurant_id)
            ->first();

        if ($user_type == 'Admin' || $restaurant_status->status == 1) {
            $data = Product::when($request->sort_by, function ($query, $value) {
                $query->orderBy($value, request('order_by', 'asc'));
            })
                ->when(!isset($request->sort_by), function ($query) {
                    $query->latest();
                })
                ->when($request->search, function ($query, $value) {
                    $query->where('name', 'LIKE', '%' . $value . '%');
                })
                ->when($request->restaurant_id, function ($query, $value) {
                    $query->where('restaurant_id', 'LIKE', '%' . $value . '%');
                })
                ->paginate($request->page_size ?? 10);

            return Inertia::render('Products', [
                'items' => $data,
                'restaurant_id' => $request->restaurant_id,
            ]);
        } else {
            return redirect()->route('restaurants.index')->with('message', [
                'type' => 'error',
                'text' => 'Ovaj restoran trenutno nije u funkciji!',
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

    public function store(Request $request)
    {

        $data = $this->validate($request, [
            'name' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'status' => 'required|boolean',
            'user_id' => 'required|integer',
            'restaurant_id' => 'required|integer',
        ]);

        Product::create($data);
        return redirect()->back()->with('message', [
            'type' => 'success',
            'text' => 'Uspjesno unesena stavka u meni restorana!',
        ]);

    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        //
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validate($request, [
            'name' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'status' => 'required|boolean',
            'user_id' => 'required|integer',
            'restaurant_id' => 'required|integer',
        ]);
//        $data['price'] = floatval($data['price']);

        $product->update($data);
        return redirect()->back()->with('message', [
            'type' => 'success',
            'text' => 'Uspjesno izmijenjeni podaci za proizvod!',
        ]);
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->back()->with('message', [
            'type' => 'success',
            'text' => 'Uspjesno ste uklonili proizvod iz ponude!',
        ]);
    }
}
