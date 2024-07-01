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
            'categories',
            'translations',
            ]);

        return new AdminProductResource($product);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateProductRequest $request)
    {
        $request = $request->validated();

        DB::transaction( function() use ($request)
        {
            $product                = new Product();
            $product->name          = $request['name'];
            $product->is_varied     = $request['is_varied'];
            $product->is_saleable   = $request['is_saleable'];
    
            $product->save();

            $categories = $request['categories'];
            $product->categories()->attach($categories);

            $this->setTranslation($product, $request);
    
            if ($product->is_varied) {

                foreach ($request['variants'] as $key => $value) {
                
                    $variant = new ProductVariant();

                    $this->setVariant($value, $variant, $product);   
                }
            } else {

                $variant = new ProductVariant();

                $this->setVariant($request, $variant, $product);            
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
            $categories         = $request['categories'];
    
            $product->name          = $request['name'];
            $product->is_varied     = $request['is_varied'];
            
            $product->categories()->sync($categories);

            $product->save();

            if ($request['is_varied']) {

                $this->updateProductToManyVariant($request['variants'], $product);
            } else {
                
                $this->updateProductToSingelVariant($request, $product, $oldProductIsVaried);        
            }

            $this->setTranslation($product, $request);
            
        });
        
        return new SuccessResource(200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        DB::transaction( function () use ($product){
            $product->variants()->delete();
            $product->translations()->delete();
            $product->categories()->detach();
            $product->delete();
        });

        return new SuccessResource();
    }
    
    ////////////////////
    //private function//
    ////////////////////

    /**
     * update product to just has one variant
     */
    private function updateProductToSingelVariant(array $request, Product $product, bool $oldProductIsVaried)
    {
        if ($oldProductIsVaried) { //if old product is varied
            $this->deleteVariants($product->id);
        }

        $variant = ProductVariant::query()
                    ->where([
                        [ 'is_default', '=', true ],
                        [ 'product_id', '=', $product->id ]
                    ])
                    ->firstOrNew();

        $this->setVariant($request, $variant, $product);
    }

    /**
     * update product to many variants
     */
    private function updateProductToManyVariant($reqestVariants, Product $product)
    {
        $undeletedIds    = [];

        foreach ($reqestVariants as $key => $request) {
            $variant = ProductVariant::query()
                    ->where([
                        [ 'id', '=', $request['id'] ],
                        [ 'product_id', '=', $product->id ]
                    ])
                    ->firstOrNew();
            $undeletedIds[] = $this->setVariant($request, $variant, $product);   
        }

        $this->deleteVariants($product->id, $undeletedIds);
    }

    private function setVariant(array $request, ProductVariant $variant, Product $product)
    {
        $variant->product_id    = $product->id;
        $variant->name          = $request['name'];
        $variant->idr_price     = $request['idr_price'];
        $variant->usd_price     = $request['usd_price'];
        $variant->stock         = $request['stock'];

        if ($product->is_varied) {
            $variant->is_default = 0;
        } else {
            $variant->is_default = 1;
        }
        
        $variant->save();

        return $variant->id;
    }

    private function deleteVariants(string $product_id, array $undeletedIds = [])
    {
        ProductVariant::where('product_id', $product_id)
                    ->whereNotIn('id', $undeletedIds)
                    ->delete();
    }

    private function setTranslation(Product $product, $request)
    {
        foreach ($request['description'] as $lang => $value) {

            $translation                   = ProductTranslation::query()
                                            ->where('product_id', $product->id)
                                            ->where('lang', $lang)
                                            ->firstOrNew();

            $translation->product_id       = $product->id;
            $translation->lang             = $lang;
            $translation->description      = $request['description'][$lang];
            
            $translation->save();
        }
    }
}
