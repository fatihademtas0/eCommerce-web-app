<?php

namespace App\Livewire;

use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Order Detail- Inverizo')]
class MyOrderDetailPage extends Component
{
    public $order_id;

    public function mount($order_id)
    {
        $this->order_id = $order_id;
    }

    public function render()
    {
        $order_items = OrderItem::with('product')->where('order_id', $this->order_id)->get();

        $address = Address::where('order_id', $this->order_id)->first();

        $order = Order::where('id', $this->order_id)->first();

        if ($order) {
            $tax = $order->grand_total * 0.2; // %20 vergi
            $shipping = $order->grand_total * 0.01; // %1 kargo ücreti
            $final_total = $order->grand_total + $tax + $shipping;

            $order->tax = $tax;
            $order->shipping = $shipping;
            $order->final_total = $final_total;
        }

        return view('livewire.my-order-detail-page', [
            'order_items' => $order_items,
            'address' => $address,
            'order' => $order
        ]);
    }
}
