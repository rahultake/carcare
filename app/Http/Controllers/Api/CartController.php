<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'product_options' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $product = Product::findOrFail($request->product_id);
        
        // Check if product is active and in stock
        if ($product->status !== 'active') {
            return response()->json([
                'status' => 'error',
                'message' => 'Product is not available'
            ], 400);
        }

        if ($product->stock_status === 'out_of_stock') {
            return response()->json([
                'status' => 'error',
                'message' => 'Product is out of stock'
            ], 400);
        }

        if ($product->track_inventory && $product->quantity < $request->quantity) {
            return response()->json([
                'status' => 'error',
                'message' => 'Insufficient stock. Only ' . $product->quantity . ' items available'
            ], 400);
        }

        $user = $request->user();
        
        // Check if item already exists in cart
        $cartItem = Cart::where('user_id', $user->id)
                       ->where('product_id', $product->id)
                       ->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $request->quantity;
            
            if ($product->track_inventory && $product->quantity < $newQuantity) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot add more items. Only ' . ($product->quantity - $cartItem->quantity) . ' more items available'
                ], 400);
            }
            
            $cartItem->update([
                'quantity' => $newQuantity,
                'price' => $product->price, // Update price in case it changed
            ]);
        } else {
            $cartItem = Cart::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'price' => $product->price,
                'product_options' => $request->product_options,
            ]);
        }

        $cartItem->load('product.images');

        return response()->json([
            'status' => 'success',
            'message' => 'Product added to cart successfully',
            'data' => [
                'cart_item' => $this->transformCartItem($cartItem)
            ]
        ]);
    }

    public function index(Request $request)
    {
        $user = $request->user();
        
        $cartItems = Cart::where('user_id', $user->id)
                        ->with(['product.images', 'product.categories'])
                        ->get();

        $transformedItems = $cartItems->map(function ($item) {
            return $this->transformCartItem($item);
        });

        $subtotal = $cartItems->sum(function ($item) {
            return $item->quantity * $item->price;
        });

        return response()->json([
            'status' => 'success',
            'data' => [
                'cart_items' => $transformedItems,
                'summary' => [
                    'items_count' => $cartItems->count(),
                    'total_quantity' => $cartItems->sum('quantity'),
                    'subtotal' => (float) $subtotal,
                ]
            ]
        ]);
    }

    public function remove(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cart_item_id' => 'required|exists:carts,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        $cartItem = Cart::where('id', $request->cart_item_id)
                        ->where('user_id', $user->id)
                        ->first();

        if (!$cartItem) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cart item not found'
            ], 404);
        }

        $cartItem->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Cart item removed successfully'
        ]);
    }

    public function checkout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shipping_address' => 'required|array',
            'shipping_address.name' => 'required|string|max:255',
            'shipping_address.phone' => 'required|string|max:20',
            'shipping_address.address_line_1' => 'required|string|max:255',
            'shipping_address.address_line_2' => 'nullable|string|max:255',
            'shipping_address.city' => 'required|string|max:100',
            'shipping_address.state' => 'required|string|max:100',
            'shipping_address.postal_code' => 'required|string|max:20',
            'shipping_address.country' => 'required|string|max:100',
            'coupon_code' => 'nullable|string|exists:coupons,code',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        
        $cartItems = Cart::where('user_id', $user->id)
                        ->with('product')
                        ->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cart is empty'
            ], 400);
        }

        // Validate stock
        foreach ($cartItems as $item) {
            if ($item->product->status !== 'active') {
                return response()->json([
                    'status' => 'error',
                    'message' => "Product '{$item->product->name}' is no longer available"
                ], 400);
            }

            if ($item->product->track_inventory && $item->product->quantity < $item->quantity) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Insufficient stock for '{$item->product->name}'. Only {$item->product->quantity} available"
                ], 400);
            }
        }

        // Calculate totals
        $subtotal = $cartItems->sum(function ($item) {
            return $item->quantity * $item->price;
        });

        $discount = 0;
        $coupon = null;

        if ($request->coupon_code) {
            $coupon = \App\Models\Coupon::where('code', $request->coupon_code)
                                    ->where('status', 'active')
                                    ->first();
            
            if ($coupon && $coupon->isValid()) {
                if ($coupon->type === 'fixed') {
                    $discount = min($coupon->value, $subtotal);
                } else {
                    $discount = ($subtotal * $coupon->value) / 100;
                    if ($coupon->maximum_discount) {
                        $discount = min($discount, $coupon->maximum_discount);
                    }
                }
            }
        }

        $total = $subtotal - $discount;

        // Store order
        DB::beginTransaction();
        try {
            $order = \App\Models\Order::create([
                'user_id' => $user->id,
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'status' => 'pending',
                'total_amount' => $total,
                'payment_method' => $request->payment_method ?? 'cod',
                'shipping_address' => json_encode($request->shipping_address),
                'billing_address' => json_encode($request->billing_address ?? $request->shipping_address),
                'ordered_at' => now(),
            ]);

            // Store order items
            foreach ($cartItems as $item) {
                \App\Models\OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product->id,
                    'product_name' => $item->product->name,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'subtotal' => $item->quantity * $item->price,
                ]);

                // Reduce stock if tracking inventory
                if ($item->product->track_inventory) {
                    $item->product->decrement('quantity', $item->quantity);
                }
            }

            // Clear cart after checkout
            Cart::where('user_id', $user->id)->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Order placed successfully',
                'order_id' => $order->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Checkout failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function transformCartItem($cartItem)
    {
        return [
            'id' => $cartItem->id,
            'quantity' => $cartItem->quantity,
            'price' => (float) $cartItem->price,
            'total' => (float) $cartItem->total,
            'product_options' => $cartItem->product_options,
            'product' => [
                'id' => $cartItem->product->id,
                'name' => $cartItem->product->name,
                'slug' => $cartItem->product->slug,
                'sku' => $cartItem->product->sku,
                'brand' => $cartItem->product->brand,
                'price' => (float) $cartItem->product->price,
                'stock_status' => $cartItem->product->stock_status,
                'quantity_available' => $cartItem->product->quantity,
                'primary_image' => $cartItem->product->images->where('is_primary', true)->first() 
                    ? asset('storage/' . $cartItem->product->images->where('is_primary', true)->first()->image_path)
                    : ($cartItem->product->images->first() ? asset('storage/' . $cartItem->product->images->first()->image_path) : null),
            ],
        ];
    }
}