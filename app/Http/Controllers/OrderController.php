<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function customerOrderStore(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
        ]);

        $user = Auth::user();

        $product = Product::find($request->product_id);

        // create order data

        $order = Order::create(
            [
                'user_id' => $user->id,
                'status'  => 'pending',
                'total'   => $product->price,
            ]
        );

        OrderDetail::create([
            'order_id'   => $order->id,
            'product_id' => $request->product_id,
            'quantity'   => 1,
            'price'      => $product->price,
        ]);

        return response()->json([
            'status'   => 'success',
            'message'  => 'Order placed successfully',
            'order_id' => $order->id,
        ]);

    }

    public function adminCustomerOrders()
    {
        $orders = Order::orderBy('created_at', 'desc')->get();
        return view('pages.dashboard.customers.orders.list', compact('orders'));
    }

    public function customerOrders()
    {
        $user   = Auth::user();
        $orders = Order::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
        return view('pages.dashboard.customers.orders.list', compact('orders'));
    }
}
