<?php

namespace App\Livewire;

use App\Helpers\CartManagement;
use App\Mail\OrderPlaced;
use App\Models\Address;
use App\Models\Order;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Title;
use Livewire\Component;
use Stripe\Checkout\Session;
use Stripe\Stripe;

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

    public function mount()
    {
        $cart_items = CartManagement::getCartItemsFromCookie();

        if (count($cart_items) == 0)
        {
            return redirect('/products');
        }
    }

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

        foreach ($cart_items as $item) {
            $line_items[] = [
                'price_data' => [
                    'currency' => 'USD',
                    'unit_amount' => $item['unit_amount'] * 100,
                    'product_data' => [
                        'name' => $item['name'],
                    ],
                ],
                'quantity' => $item['quantity'],
            ];
        }

        $order = new Order();

        $order->user_id = auth()->user()->id;

        $order->grand_total = CartManagement::calculateGrandTotal($cart_items);

        $order->payment_method = $this->payment_method;

        $order->payment_status = 'pending';

        $order->status = 'new';

        $order->currency = 'USD';

        $order->shipping_amount = CartManagement::calculateShipping($cart_items);

        $order->shipping_method = 'none';

        $order->notes = 'Order placed by ' . auth()->user()->name;

        $address = new Address();

        $address->first_name = $this->first_name;

        $address->last_name = $this->last_name;

        $address->phone = $this->phone;

        $address->street_address = $this->street_address;

        $address->city = $this->city;

        $address->state = $this->state;

        $address->zip_code = $this->zip_code;

        $redirect_url = '';

        if ($this->payment_method == 'stripe') {
            Stripe::setApiKey(env('STRIPE_SECRET'));

            $sessionCheckOut = Session::create([
                'payment_method_types' => ['card'],
                'customer_email' => auth()->user()->email,
                'line_items' => $line_items,
                'mode' => 'payment',
                'success_url' => route('success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('cancel'),
            ]);

            $redirect_url = $sessionCheckOut->url;

        } else {
            $redirect_url = route('success');
        }

        $order->save();

        $address->order_id = $order->id;

        $address->save();

        $order->items()->createMany($cart_items);

        Mail::to(request()->user())->send(new OrderPlaced($order));

        CartManagement::clearCartItemsFromCookie();

        return redirect($redirect_url);
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
