<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title("Product Detail - Inverizo")]
class ProductDetailPage extends Component
{
    public $slug;
    public $quantity;

    public function mount($slug)
    {
        $this->slug = $slug;
    }

    public function render()
    {
        return view('livewire.product-detail-page', [
            'products' => Product::where('slug', $this->slug)->firstOrFail()
        ]);
    }
}
