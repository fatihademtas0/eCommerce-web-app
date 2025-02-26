<?php

namespace App\Livewire;

use App\Helpers\CartManagement;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Cart Page - Inverizo')]
class CartPage extends Component
{
    public array $cartItems = [];
    public $grand_total;

    public int $tax = 0;

    public $shipping = 0;

    public $final_total = 0;

    public function mount()
    {
        $this->cartItems = CartManagement::getCartItemsFromCookie();

        $this->grand_total = CartManagement::calculateGrandTotal($this->cartItems);

        $this->calculateTotal();
    }

    public function removeItem($product_id)
    {
        $this->cartItems = CartManagement::removeItemFromCart($product_id);

        $this->grand_total = CartManagement::calculateGrandTotal($this->cartItems);

        $this->calculateTotal();
    }

    public function calculateTotal()
    {
        $this->tax = $this->grand_total * 0.2;

        $this->shipping = $this->grand_total * 0.01;

        $this->final_total = $this->grand_total + $this->tax + $this->shipping;
    }

    public function render()
    {
        return view('livewire.cart-page');
    }
}
