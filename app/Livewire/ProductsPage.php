<?php

namespace App\Livewire;

use App\Helpers\CartManagement;
use App\Livewire\Partials\Navbar;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;


#[Title('Products Page - Inverizo')]
class ProductsPage extends Component
{
    use WithPagination;
    use LivewireAlert;

    #[Url]
    public $selected_categories = [];

    #[Url]
    public $selected_brands = [];

    #[Url]
    public $featured;

    #[Url]
    public $on_sale;

    #[Url]
    public $price_range = 50;
    public $isFiltered = false; // Filtrenin aktif olup olmadığını takip eder

    #[Url]
    public $sort = "latest";

    // add product to cart method

    public function addItemToCart($product_id)
    {
        $total_count = CartManagement::addItemToCart($product_id);

        $this->dispatch('update-cart-count' , total_count: $total_count) ->to(Navbar::class);

        $this->alert('success' , 'Product added to cart successfully!' , [
            'position' =>  'bottom-end',
            'timer' =>  3000,
            'toast' =>  true
        ]);
    }
    public function updatedPriceRange()
    {
        $this->isFiltered = true; // Kullanıcı price_range değiştirdiğinde filtreyi aktif et
    }

    public function render()
    {
        $productQuery = Product::query()->where('is_active', 1);

        if (!empty($this->selected_categories)) {
            $productQuery->whereIn('category_id', $this->selected_categories);
        }

        if (!empty($this->selected_brands)) {
            $productQuery->whereIn('brand_id', $this->selected_brands);
        }

        if ($this->featured) {
            $productQuery->where('is_featured', 1);
        }

        if ($this->on_sale) {
            $productQuery->where('on_sale', 1);
        }

        if ($this->isFiltered && $this->price_range) {
            $productQuery->whereBetween('price', [0, $this->price_range]);
        }

        if ($this->sort == "latest") {
            $productQuery->latest();
        }

        if ($this->sort == "price") {
            $productQuery->orderBy('price', 'DESC');
        }

        return view('livewire.products-page', [
            'products' => $productQuery->paginate(9),
            'brands' => Brand::where('is_active', 1)->get(['id', 'name', 'slug']),
            'categories' => Category::where('is_active', 1)->get(['id', 'name', 'slug']),
        ]);
    }
}
