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
use App\Query\Product\SortByPrice;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = QueryBuilder::for(Product::class)
                    ->defaultSort('name')
                    ->allowedSorts([
                        'name',
                        'id',
                        AllowedSort::custom('price', new SortByPrice)
                    ])
                    ->allowedFilters(['name'])
                    ->paginate(25)->appends(request()->query());

        return AdminProductResource::collection($products);
    }

    /**
     * Display the specified resource.
     */
    public function show($slug, Request $requet)
    {
        $product = Product::query()->where('slug', $slug)->with([
            'variants',
            'categories',
            'translations',
            'images' => function (Builder $query) {
                $query->where('is_saved', true)->orderBy('position', 'asc');
            }
            ])->firstOrFail();

        return new AdminProductResource($product);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateProductRequest $request)
    {
        DB::transaction( function() use ($request)
        {
            $product                = new Product();
            $product->name          = $request->name;
            $product->is_varied     = $request->is_varied;
            $product->is_saleable   = $request->is_saleable;
    
            $product->save();

            $categories = $request->validated('categories');
            $product->categories()->attach($categories);
            
            foreach ($request->description as $lang => $value) {
                $this->setTranslation($product, $request->validated(), $lang);
            }
    
            if ($request->is_varied) {
                foreach ($request->validated()['variants'] as $key => $request) {
                    $this->setVariant($request, $product);   
                }
            } else {
                $this->setVariant($request->validated(), $product, true);            
            }
        });

        return new SuccessResource(201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        DB::transaction( function() use ($request, $product)
        {
            if ($request->is_varied) {
                $this->productToManyVariant($request->validated()['variants'], $product);
            } else {
                $this->productToSingelVariant($request->validated(), $product);        
            }

            foreach ($request->description as $lang => $value) {
                $this->setTranslation($product, $request->validated(), $lang);
            }
            
            $categories = $request->validated('categories');
    
            $product->name          = $request->name;
            $product->is_varied     = $request->is_varied;
            $product->categories()->sync($categories);

            $product->save();    
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
    
    ///////////////
    //for variant//
    ///////////////

    /**
     * update product to just has one variant
     */
    private function productToSingelVariant(array $request, Product $product)
    {
        /** */
        if ($product->is_varied == true) { //if current product is varied
            $this->deleteVariants($product->id);
            $this->setVariant($request, $product, true);
        } else {
            $request['id'] = ProductVariant::where('product_id',$product->id)->value('id');
            $this->setVariant($request, $product, true);
        }
    }

    /**
     * update product to many variants
     */
    private function productToManyVariant($reqestVariants, Product $product)
    {
        $undeletedIds    = [];

        foreach ($reqestVariants as $key => $request) {
            $undeletedIds[] = $this->setVariant($request, $product);   
        }

        $this->deleteVariants($product->id, $undeletedIds);
    }

    private function setVariant(array $request, Product $product, bool $isDefault = false)
    {
        $request['id'] = isset($request['id']) ? $request['id'] : '';
        $variant = ProductVariant::query()
                    ->where('id', $request['id'])
                    ->where('product_id', $product->id)
                    ->firstOrNew();

        $variant->product_id    = $product->id;
        $variant->is_default    = $isDefault;
        $variant->name          = $request['name'];
        $variant->size          = $request['size'];
        $variant->idr_price     = $request['idr_price'];
        $variant->usd_price     = $request['usd_price'];
        $variant->stock         = $request['stock'];
        
        $variant->save();

        return $variant->id;
    }

    private function deleteVariants(string $product_id, array $undeletedIds = [])
    {
        ProductVariant::where('product_id', $product_id)
                    ->whereNotIn('id', $undeletedIds)
                    ->delete();
    }

    private function setTranslation(Product $product, $request, string $lang)
    {
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
