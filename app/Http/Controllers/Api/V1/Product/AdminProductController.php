<?php

namespace App\Http\Controllers\Api\V1\Product;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Translation\ProductTranslation;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Product\CreateProductRequest;
use App\Http\Requests\Api\V1\Product\UpdateProductRequest;
use App\Http\Resources\Api\V1\Common\SuccessResource;
use App\Http\Resources\Api\V1\Product\AdminProductResource;
use App\Models\ProductCategory;
use App\Service\Services\ImageServices\ImageService;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;

class AdminProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = QueryBuilder::for(Product::class)
                    ->defaultSort('-id')
                    ->withMin('variants', 'usd_price')
                    ->allowedSorts([
                        'name',
                        'popular',
                        AllowedSort::field('price', 'variants_min_usd_price')
                    ])
                    ->allowedFilters([
                        AllowedFilter::partial('search', 'name')
                    ])
                    ->paginate(25)->appends(request()->query());

        return AdminProductResource::collection($products);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product = $product->load([
            'variants',
            'category',
            ]);

        return new AdminProductResource($product);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateProductRequest $request)
    {
        $request = $request->validated();

        DB::transaction(function() use ($request){

            $product = $this->setProduct(new Product(), $request);
    
            if ($product->is_varied) {
                
                foreach ($request['variants'] as $key => $variantRequest) {

                    $this->setVariant($variantRequest, new ProductVariant(), $product);   
                }
            } else {
                $this->setVariant($request, new ProductVariant(), $product);            
            }
        });
        
        return new SuccessResource(201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $request = $request->validated();

        DB::transaction( function() use ($request, $product)
        {
            $oldProductIsVaried = $product->is_varied;
            $newProduct         = $this->setProduct($product, $request);

            if ($request['is_varied']) {

                $this->updateProductToManyVariant($request['variants'], $newProduct);
            } else {

                $this->updateProductToSingelVariant($request, $newProduct, $oldProductIsVaried);        
            }
            
        });
        
        return new SuccessResource(200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        try {
            DB::beginTransaction();
            
            $imageServices = ImageService::collection($product->images);
            $imageServices->delete();

            $product->images()->delete();
            $product->variants()->delete();
            $product->delete();
    
            DB::commit();
            
            return new SuccessResource();

        } catch (\Throwable $th) {
            
            DB::rollBack();

            if (isset($imageServices)) {
                $imageServices->rollback();
            }

            throw $th;
        }
    }
    
    ////////////////////
    //private function//
    ////////////////////

    private function setProduct(Product $product, array $request)
    {
        $product->name                  = $request['name'];
        $product->available             = $request['available'];
        $product->category_id           = $request['category_id'];
        $product->full_description      = $request['full_description'];
        $product->short_description     = $request['short_description'];
        $product->is_varied             = $request['is_varied'];

        $product->save();

        return $product;
    }

    private function setVariant(array $request, ProductVariant $variant, Product $product)
    {
        $variant->product_id    = $product->id;
        $variant->idr_price     = $request['idr_price'];
        $variant->usd_price     = $request['usd_price'];
        $variant->stock         = $request['stock'];
        $variant->available     = $request['available'];

        if ($variant->code != $request['code']) {
            $variant->code = $request['code'];
        }

        if ($product->is_varied) {
            $variant->type = $request['type'];
        } else {
            $variant->type = $variant::DEFAULT_TYPE;
        }
        
        $variant->save();
    }

    /**
     * update product to just has one variant
     */
    private function updateProductToSingelVariant(array $request, Product $product, bool $oldProductIsVaried)
    {
        if ($oldProductIsVaried) {
            $product->variants()->delete();
        }

        $variant = $product->variants()->default()->firstOrNew();

        $this->setVariant($request, $variant, $product);
    }

    /**
     * update product to many variants
     */
    private function updateProductToManyVariant($requestVariants, Product $product)
    {
        $updateRequest = collect($requestVariants)->whereNotNull('id');
        $newRequest    = collect($requestVariants)->whereNull('id');

        foreach ($product->variants as $variant) { // update old variant

            $request = $updateRequest->where('id', $variant->id)->first();
            
            if ($request != null) {
                $this->setVariant($request, $variant, $product);
            } else {
                $variant->delete();
            }
        }

        foreach ($newRequest as $request) { // make new variant
            $this->setVariant($request, new ProductVariant(), $product);
        }
    }
}
