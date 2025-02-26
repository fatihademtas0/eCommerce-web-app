<?php

namespace App\Livewire\Partials;

use App\Helpers\CartManagement;
use Livewire\Attributes\On;
use Livewire\Component;

class Navbar extends Component
{
    public $total_count = 0;

    public function mount(): void
    {
        $cartItems = CartManagement::getCartItemsFromCookie();
        $this->total_count = count(is_array($cartItems) ? $cartItems : []);
    }

    #[On('update-cart-count')]
    public function updateCartCount($total_count): void
    {
        $this->total_count = $total_count;
    }

    public function render()
    {
        return view('livewire.partials.navbar');
    }
}
