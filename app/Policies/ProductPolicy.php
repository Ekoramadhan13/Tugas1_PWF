<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    /**
     * Hanya pemilik produk yang bisa update
     */
    public function update(User $user, Product $product): bool
    {
        return $user->id === $product->user_id;
    }

    /**
     * Admin bisa hapus produk siapa saja,
     * user biasa hanya bisa hapus produk miliknya sendiri
     */
    public function delete(User $user, Product $product): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $user->id === $product->user_id;
    }
}