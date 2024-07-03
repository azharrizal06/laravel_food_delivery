<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
     //User: create new order
    public function createOrder (Request $request){
        $request -> validate([
            'order_items' => 'required|array',
            'order_items.*.product_id' => 'required|integer|exists:products,id',
            'order_items.*.quantity' => 'required|integer|min:1',
            'restaurant_id' => 'required|integer|exists:users,id',
            'shipping_cost' => 'required|integer',
        ]);

        //total_price
        $totalPrice = 0;
        foreach ($request->order_items as $item) {
            //product
            $product = $item['product_id'];
            $product = Product::find($product);
            $totalPrice += $product->price * $item['quantity'];
        };
        $totalBill = $totalPrice + $request->shipping_cost;

        
        $user = $request->user();
        $data= $request->all();
        //user_id
        $data['user_id'] = $user->id;
        //shipping_address
        $shippingAddtess = $request->shipping_address;
        $data['shipping_address'] = $shippingAddtess;

        //shipping_latlong
        $shippingLatLong = $request->shipping_latlong;
        $data['shipping_latlong'] = $shippingLatLong;
        $data['status'] = 'pending';
        //total_price
    
        $data['total_price'] = $totalPrice;
        $data['total_bill'] = $totalBill;

        //create order
        $order = Order::create($data);
        foreach ($request->order_items as $item) {
            $product = Product::find($item['product_id']);
            $orderItem = new OrderItem([
                'product_id' => $product->id,
                'order_id' => $order->id,
                'quantity' => $item['quantity'],
                'price' => $product->price,
            ]);
            $order->orderItems()->save($orderItem);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Order created successfully',
            'data' => $order
        ]);

    }
}
