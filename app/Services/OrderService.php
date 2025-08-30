<?php

namespace App\Services;

use App\Models\{Order, OrderItem, Product, Payment, Address};
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class OrderService
{
    public function create(array $payload): Order
    {
        return DB::transaction(function () use ($payload) {
            $address = Address::create($payload['address'] ?? []);

            $order = new Order([
                'status'         => Order::STATUS_PENDING,
                'payment_status' => Order::PAYMENT_UNPAID,
                'currency'       => $payload['currency'] ?? 'YER',
            ]);
            $order->shippingAddress()->associate($address);
            $order->save();

            $subtotal = 0;
            foreach ($payload['items'] ?? [] as $row) {
                $product = Product::query()->lockForUpdate()->findOrFail($row['product_id']);
                $qty     = (int) ($row['quantity'] ?? 0);
                if ($qty <= 0) throw new InvalidArgumentException('Quantity must be > 0');
                if ($product->stock < $qty) throw new InvalidArgumentException("Not enough stock for product #{$product->id}");

                $unit = (float) $product->price;
                $line = $unit * $qty;
                $subtotal += $line;

                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $product->id,
                    'quantity'   => $qty,
                    'unit_price' => $unit,
                    'subtotal'   => $line,
                ]);

                $product->decrement('stock', $qty);
            }

            $discount    = (float) ($payload['discount'] ?? 0);
            $shippingFee = (float) ($payload['shipping_fee'] ?? 0);
            $tax         = (float) ($payload['tax'] ?? 0);
            $total       = max(0, $subtotal - $discount + $shippingFee + $tax);

            $order->fill([
                'subtotal'     => $subtotal,
                'discount'     => $discount,
                'shipping_fee' => $shippingFee,
                'tax'          => $tax,
                'total'        => $total,
            ])->save();

            if (!empty($payload['payment'])) {
                $pay = $payload['payment'];
                Payment::create([
                    'order_id' => $order->id,
                    'amount'   => $pay['amount'] ?? $total,
                    'method'   => $pay['method'] ?? 'CASH',
                    'status'   => $pay['status'] ?? Payment::STATUS_PENDING,
                    'meta'     => $pay['meta'] ?? null,
                ]);

                if (($pay['status'] ?? null) === Payment::STATUS_PAID) {
                    $order->update(['payment_status' => Order::PAYMENT_PAID, 'status' => Order::STATUS_COMPLETED]);
                }
            }

            return $order->fresh(['shippingAddress','orderItems.product.category','payment']);
        });
    }
}
