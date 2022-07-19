<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Transformers\ProductTransformer;
use App\Models\Product;
use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;
use \Dingo\Api\Http\Response as DingoResponse;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @group Productos
 */
class ProductController extends Controller
{
    use Helpers;

    /**
     * Productos paginados
     *
     * @authenticated
     *
     * @queryParam page int
     *
     * @response
     * status=200
     * scenario="success"
     * {
     *      "data": [
     *          {
     *              "id": 1,
     *              "name": "Pigeon went on, 'I must.",
     *              "description": "Nihil fugit animi ut voluptatem quidem ea. Nihil ratione assumenda ab voluptates aspernatur. Sit aut consequatur aut libero. Aut rerum voluptatem nihil consequuntur eaque laboriosam. Dolor commodi voluptas nam quia id. Nulla provident id vel voluptatem occaecati esse. Eum numquam qui aspernatur libero sed voluptatem velit.",
     *              "available": 1,
     *              "price": 46.08,
     *              "created_at": "2021-08-19T15:21:27.000000Z"
     *          },
     *          {
     *              "id": 2,
     *              "name": "Gryphon. 'Of course,'.",
     *              "description": "Perferendis esse nemo quidem sequi quas omnis repellat. Eveniet ut doloribus ipsa quibusdam illo sit. Libero occaecati quam et perferendis eos totam dicta laborum. Et explicabo saepe aspernatur. Eum quibusdam facilis reiciendis dolores rerum. Repellendus vero et quod voluptas. Similique iure id est.",
     *              "available": 0,
     *              "price": 28.96,
     *              "created_at": "2021-08-19T15:21:27.000000Z"
     *          }
     *      ],
     *      "meta": {
     *          "pagination": {
     *              "total": 16,
     *              "count": 10,
     *              "per_page": 10,
     *              "current_page": 1,
     *              "total_pages": 2,
     *              "links": {
     *                  "next": "https://laravel-dingo-api.test/api/products?page=2"
     *              }
     *          }
     *      }
     * }
     *
     * @return DingoResponse
     */
    public function index()
    {
        \request()->validate([
            "page" => "nullable|int|min:1|max:200"
        ]);
        return $this->response->paginator(Product::paginate(10), new ProductTransformer);
    }

     /**
     * Crear producto
     *
     * @authenticated
     *
     * @bodyParam name string Nombre del producto
     * @bodyParam description string Descripción del producto
     * @bodyParam available bool ¿Está disponible el producto?
     * @bodyParam price float Precio del producto
     *
     * @response
     * status=200
     * scenario="success"
     * {
     *      "data": {
     *          "id": 10,
     *          "name": "producto actualizado",
     *          "description": "Un nuevo producto en la tienda",
     *          "available": 1,
     *          "price": 34.99,
     *          "created_at": "2021-08-19T15:21:27.000000Z"
     *      }
     * }
     *
     * @param Request $request
     *
     * @return DingoResponse
     */
    public function store(Request $request)
    {
        $request->merge(["available" => !($request->input("available") === "false")]);
        $request->validate([
            'name' => 'required|string|min:2|max:50|unique:products',
            'description' => 'string|min:10|max:500',
            'available' => 'nullable|bool',
            'price' => 'required|numeric',
        ]);

        $product = Product::create($request->input());

        return $this->response->item($product, new ProductTransformer);
    }

    /**
     * Obtener producto por ID
     *
     * @authenticated
     *
     * @urlParam id int ID del producto
     *
     * @response
     * status=200
     * scenario="success"
     * {
     *      "data": {
     *          "id": 10,
     *          "name": "Alice. 'Exactly so,'.",
     *          "description": "Quam aut reiciendis omnis aut aliquid sequi. Omnis est voluptas laudantium culpa itaque. Assumenda dolorem officia eum. Facere quia nostrum enim vel fuga dolor quibusdam nisi. Ea voluptatem explicabo iste. Aut a veritatis aut voluptatem. Provident eveniet ex voluptate dolore sequi.",
     *          "available": 0,
     *          "price": 34.99,
     *          "created_at": "2021-08-19T15:21:27.000000Z"
     *      }
     * }
     *
     * @param Product $product
     *
     * @return DingoResponse
     */
    public function show(Product $product)
    {
        return $this->response->item($product, new ProductTransformer);
    }

     /**
     * Actualizar producto
     *
     * @authenticated
     *
     * @bodyParam name string Nombre del producto
     * @bodyParam description string Descripción del producto
     * @bodyParam available bool ¿Está disponible el producto?
     * @bodyParam price float Precio del producto
     *
     * @param Request $request
     * @param Product $product
     * @return DingoResponse
     */
    public function update(Request $request, Product $product)
    {
        $request->merge(["available" => !($request->input("available") === "false")]);
        $request->validate([
            'name' => 'required|string|min:2|max:50|unique:products,name,' . $product->id,
            'description' => 'string|min:10|max:500',
            'available' => 'nullable|bool',
            'price' => 'required|numeric',
        ]);

        $product->fill($request->input())->save();

        return $this->response->item($product, new ProductTransformer);
    }

    /**
     * Eliminar producto por ID
     *
     * @authenticated
     *
     * @param Product $product
     *
     * @response status=204 scenario="success"
     *
     * @return DingoResponse
     */
    public function destroy(Product $product) {
        $product->delete();

        return $this->response->noContent();
    }
}
