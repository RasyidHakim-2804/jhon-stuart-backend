<?php

namespace App\Http\Controllers\Api\V1\Product;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Product\ProductRequest;
use App\Http\Resources\Common\SuccessResource;
use App\Http\Resources\Product\ProductResource;
use App\Models\ProductCategory;
use App\Traits\JsonResponsesTrait;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;

class AdminProductController extends Controller
{
    use JsonResponsesTrait;

    public function index()
    {
        $products = QueryBuilder::for(Product::class)
                    ->defaultSort('-id')
                    ->selectVariantPrice()
                    ->allowedSorts([
                        'name',
                        'popular',
                        'price',
                        'rating',
                    ])
                    ->allowedFilters([
                        AllowedFilter::scope('search')
                    ])
                    ->with([
                        'category:id,name',
                    ])->paginate(25)->appends(request()->query());

        return ProductResource::collection($products);
    }

    public function show(Product $product)
    {
        $product = $product->load([
            'variants',
            'category:id,name,slug,type',
            ]);

        return new ProductResource($product, true);
    }

    public function store(ProductRequest $request)
    {
        $request = $request->validated();

        DB::beginTransaction();
        try {

            $product = $this->saveProduct(new Product(), $request);

            if ($product->is_varied) {
                foreach ($request['variants'] as $key => $value) {
                    $this->saveVariant($product, $value, false);
                }
            } else {
                $this->saveVariant($product, $request, true);
            }

            DB::commit();

            return $this->successStore();
        } catch (\Throwable $th) {

            DB::rollBack();

            throw $th;
        }
    }

    public function update(ProductRequest $request, Product $product)
    {
        $request = $request->validated();

        DB::beginTransaction();
        try {

            $product     = $this->saveProduct($product, $request);
            $variant_ids = [];

            if ($product->is_varied == 1) {
                foreach ($request['variants'] as $key => $value) {
                    $variant_ids[] = $this->saveVariant($product, $value, false);
                }
            } else {
                $variant_ids[] = $this->saveVariant($product, $request, true);
            }

            $this->deleteExcludedVariants($product, $variant_ids);

            DB::commit();

            return $this->successResponse();

        } catch (\Throwable $th) {

            DB::rollBack();

            throw $th;
        }
    }

    public function destroy(Product $product)
    {
        return $this->errorMessage('Product cannot deleted');

        DB::beginTransaction();
        try {
            DB::commit();
        } catch (\Throwable $th) {
            Db::rollBack();
            throw $th;
        }
    }

    // === PRIVATE FUNCTION ===

    private function saveProduct(Product $product, array $request)
    {
        $product->name          = $request['name'];
        $product->meta          = $request['meta'];
        $product->description   = $request['description'];
        $product->type          = $request['type'];
        $product->category_id   = $request['category_id'];
        $product->code          = $request['code'];
        $product->is_varied     = $request['is_varied'];
        $product->is_active     = $request['is_active'];

        $product->save();

        return $product;
    }

    private function saveVariant(Product $product, array $request, bool $default)
    {
        if ($default) { // for default variant

            $variant = $product->variants()->default()->withTrashed()->firstOrNew();

        } else if (!isset($request['id'])){ // for create new variant in product is_varied

            $variant = new ProductVariant();

        } else { // for update variant in product is_varied

            $variant = $product->variants()->where('id', '=', $request['id'])->default(false)->firstOrFail();

        }

        if ($product->type == ProductCategory::MUSEUM_TYPE) {
            $request['stock'] = 1;
        }

        $variant->code          = $request['code'];
        $variant->product_id    = $product->id;
        $variant->name          = $request['name'];
        $variant->price         = $request['price'];
        $variant->usd_price     = $request['usd_price'];
        $variant->code          = $request['code'];
        $variant->stock         = $request['stock'];
        $variant->is_active     = $request['is_active'];
        $variant->is_default    = $default;
        $variant->save();

        if ($variant->trashed() && $default) {
            $variant->restore();
        }

        return $variant->id;
    }

    private function deleteExcludedVariants(Product $product, array $variant_ids)
    {
        $variants = $product->variants()->whereNotIn('id', $variant_ids)->get();

        if ($variants != null) {
            foreach ($variants as $variant) {
                $variant->delete();
            }
        }
    }
}
