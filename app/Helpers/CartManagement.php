<?php

namespace App\Helpers;


use App\Models\Product;
use Illuminate\Support\Facades\Cookie;

class CartManagement
{
    // add item to cart
    public static function addItemToCart($product_id): int
    {
        $cart_items = self::getCartItemsFromCookie();

        $existing_item = null;

        foreach ($cart_items as $key => $item) {
            if ($item['product_id'] == $product_id) {
                $existing_item = $key;
                break;
            }
        }

        if ($existing_item !== null) {
            $cart_items[$existing_item]['quantity']++;
            $cart_items[$existing_item]['total_amount'] = $cart_items[$existing_item]['quantity'] * $cart_items[$existing_item]['price'];
        } else {
            $product = Product::where('id', $product_id)->first(['id', 'name', 'price', 'images']);

            if ($product) {
                $cart_items[] = [
                    'product_id' => $product_id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'image' => $product->images[0],
                    'quantity' => 1,
                    'unit_amount' => $product->price,
                    'total_amount' => $product->price
                ];
            }
        }

        self::addCartItemsToCookie($cart_items);
        return count($cart_items);
    }

    // add item to cart with quantity
    public static function addItemToCartWithQuantity($product_id , $quantity = 1): int
    {
        $cart_items = self::getCartItemsFromCookie();

        $existing_item = null;

        foreach ($cart_items as $key => $item) {
            if ($item['product_id'] == $product_id) {
                $existing_item = $key;
                break;
            }
        }

        if ($existing_item !== null) {
            $cart_items[$existing_item]['quantity'] += $quantity;
            $cart_items[$existing_item]['total_amount'] = $cart_items[$existing_item]['quantity'] * $cart_items[$existing_item]['unit_amount'];
        } else {
            $product = Product::where('id', $product_id)->first(['id', 'name', 'price', 'images']);

            if ($product) {
                $cart_items[] = [
                    'product_id' => $product_id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'image' => $product->images[0],
                    'quantity' => $quantity,
                    'unit_amount' => $product->price,
                    'total_amount' => $quantity * $product->price
                ];
            }
        }

        self::addCartItemsToCookie($cart_items);
        return count($cart_items);
    }

    // remove item from cart
    public static function removeItemFromCart($product_id): array
    {
        $cart_items = self::getCartItemsFromCookie();

        foreach ($cart_items as $key => $item) {
            if ($item['product_id'] == $product_id) {
                unset($cart_items[$key]);
            }
        }

        self::addCartItemsToCookie($cart_items);

        return $cart_items;
    }

    // add cart items to cookie
    public static function addCartItemsToCookie($cart_items): bool
    {
        $json_data = json_encode($cart_items);

        if ($json_data === false) {
            return false; // Eger JSON donusum basarisiz olursa hata dondur
        }

        Cookie::queue('cart_items', $json_data, 60 * 24 * 30);
        return true;
    }

    // clear cart items from cookie
    public static function clearCartItemsFromCookie($cart_items): void
    {
        Cookie::queue(Cookie::forget('cart_items'));
    }

    // get all cart item from cookie

    public static function getCartItemsFromCookie(): array
    {
        $cart_items = Cookie::get('cart_items');

        if (!$cart_items) {
            return []; // Eğer çerez yoksa boş dizi döndür
        }

        $decoded_items = json_decode($cart_items, true);

        return is_array($decoded_items) ? $decoded_items : []; // JSON geçerli değilse boş dizi döndür
    }
    /*
    public static function getCartItemsFromCookie(): array
    {
        $cart_items = json_encode(Cookie::get('cart_items'), true);

        if (!$cart_items) {
            $cart_items = '[]';
        }

        return $cart_items;
    }*/

    // increment item quantity
    public static function incrementQuantityToCartItem($product_id): array
    {
        $cart_items = self::getCartItemsFromCookie();

        foreach ($cart_items as $key => $item) {
            if ($item['product_id'] == $product_id) {
                $cart_items[$key]['quantity']++;
                $cart_items[$key]['total_amount'] = $cart_items[$key]['quantity'] * $cart_items[$key]['unit_amount'];
            }
        }

        self::addCartItemsToCookie($cart_items);

        return $cart_items;
    }

    // decrement item quantity
    public static function decrementQuantityToCartItem($product_id): array
    {
        $cart_items = self::getCartItemsFromCookie();

        foreach ($cart_items as $key => $item) {
            if ($item['product_id'] == $product_id) {
                if ($item['quantity'] > 1) {
                    $cart_items[$key]['quantity']--;
                    $cart_items[$key]['total_amount'] = $cart_items[$key]['quantity'] * $cart_items[$key]['unit_amount'];
                }
            }
        }

        self::addCartItemsToCookie($cart_items);

        return $cart_items;
    }

    // calculate grand total
    public static function calculateGrandTotal($items): float|int
    {
        return array_sum(array_column($items, 'total_amount'));
    }


}
