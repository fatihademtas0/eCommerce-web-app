<?php

namespace App\Livewire;

use App\Helpers\CartManagement;
use App\Models\Order;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Checkout')]
class CheckoutPage extends Component
{
    public $first_name;
    public $last_name;
    public $phone;
    public $street_address;
    public $city;
    public $state;
    public $zip_code;
    public $payment_method;

    public function placeOrder()
    {
        $this->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'phone' => 'required',
            'street_address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zip_code' => 'required',
            'payment_method' => 'required'
        ]);

        $cart_items = CartManagement::getCartItemsFromCookie();

        $line_items = [];

        foreach ($cart_items as $item)
        {
            $line_items[] = [
                'price_data' => [
                    'currency' => 'USD',
                    'unit_amount' =>$item['unit_amount'] * 100,
                    'product_data' => [
                        'name' => $item['name'],
                    ],
                    'quantity' => $item['quantity'],
                ]
            ];
        }

        $order = new Order();

        $order->user_id = auth()->user()->id;

        $order->grand_total = CartManagement::calculateGrandTotal($cart_items);
    }

    public function render()
    {
        $tax = 0;

        $shipping = 0;

        $final_total = 0;

        $cart_items = CartManagement::getCartItemsFromCookie();

        $grand_total = CartManagement::calculateGrandTotal($cart_items);

        $this->tax = $grand_total * 0.2;

        $this->shipping = $grand_total * 0.01;

        $this->final_total = $grand_total + $this->tax + $this->shipping;

        return view('livewire.checkout-page', [
            'grand_total' => $grand_total,
            'cart_items' => $cart_items,
            'final_total' => $this->final_total,
            'tax' => $this->tax,
            'shipping' => $this->shipping,
        ]);
    }
}
