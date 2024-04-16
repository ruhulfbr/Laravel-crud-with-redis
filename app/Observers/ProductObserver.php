<?php

namespace App\Observers;

use App\Models\product;
use Illuminate\Support\Str;

class ProductObserver
{

    public function creating(Product $product)
    {
        $product->slug = Str::slug($product->name);
    }

    /**
     * Handle the product "created" event.
     */
    public function created(product $product): void
    {
        $product->unique_id = 'PR-'.$product->id;
        $product->save();
    }

    /**
     * Handle the product "updated" event.
     */
    public function updated(product $product): void
    {
        //
    }

    /**
     * Handle the product "deleted" event.
     */
    public function deleted(product $product): void
    {
        //
    }

    /**
     * Handle the product "restored" event.
     */
    public function restored(product $product): void
    {
        //
    }

    /**
     * Handle the product "force deleted" event.
     */
    public function forceDeleted(product $product): void
    {
        //
    }
}
