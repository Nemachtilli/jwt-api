<?php

namespace App\Http\Transformers;

use App\Models\Product;
use League\Fractal\TransformerAbstract;

class ProductTransformer extends TransformerAbstract {

    public function transform(Product $product): array {
        return [
            "id" => $product->id,
            "name" => $product->name,
            "price" => $product->price,
            "description" => $product->description,
            "available" => $product->available ? "Sí" : "No",
            "created_at" => $product->created_at->format("d-m-Y")
        ];
    }
}
