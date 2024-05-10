<?php

namespace App\Http\Controllers\Api\V1\Product;

use App\Models\Product;
use App\Models\Variant;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Product\CreateProductRequest;
use App\Http\Requests\Api\V1\Product\UpdateProductRequest;
use App\Http\Resources\Api\V1\Product\ProductResource;
use App\Http\Resources\Api\V1\Common\SuccessResource;
use App\Http\Resources\Api\V1\Product\ProductFullResource;

class AdminProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with(['images'])->paginate(25);
        
        return ProductResource::collection($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateProductRequest $request)
    {
        $product                = new Product();
        $product->name          = $request->name;
        $product->is_varied     = $request->is_varied;
        $product->description   = $request->description;

        $product->save();

        $categories = $request->validated('categories');
        $product->categories()->attach($categories);

        if ($request->is_varied) {
            foreach ($request->validated()['variants'] as $key => $request) {
                $this->createVariant($request, $product->id);   
            }
        } else {
            $this->createVariant($request->validated(), $product->id);            
        }

        return new SuccessResource(201);
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        $product = Product::where('slug', $slug)->with(['variants', 'categories', 'images'])->firstOrFail();
        
        return new ProductFullResource($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        if ($request->is_varied) {
            $this->modifyVariants($request->validated()['variants'], $product);
        } else {
            $this->modifySingelVariant($request->validated()['variants'], $product);        
        }
        
        $categories = $request->validated('categories');

        $product->name          = $request->name;
        $product->description   = $request->description;
        $product->is_varied     = $request->is_varied;
        $product->categories()->sync($categories);
        $product->save();

        return new SuccessResource(200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->variants()->delete();
        $product->categories()->detach();
        $product->delete();

        return new SuccessResource();
    }
    
    ///////////////
    //for variant//
    ///////////////

    private function modifySingelVariant(array $request, Product $product)
    {
        /** */
        if ($product->is_varied == true) {
            $this->deleteVariants($product->id);
            $this->createVariant($request, $product->id);
        } else {
            $request['id'] = Variant::select('id')->where('product_id',$product->id)->firstOrFail();
            $this->updateVariant($request, $product->id);
        }
    }

    private function modifyVariants($reqestVariants, Product $product)
    {
        $undeletedIds    = [];

        foreach ($reqestVariants as $key => $request) {
            if ($request['id'] === null) {
            
                $undeletedIds[] = $this->createVariant($request, $product->id);   
            } else {

                $undeletedIds[] = $request['id'];
                $this->updateVariant($request, $product->id);
            }   
        }

        $this->deleteVariants($product->id, $undeletedIds);
    }

    private function createVariant(array $request, $product_id)
    {
        $variant                = new Variant();
        $variant->product_id    = $product_id;
        $variant->name          = $request['name'];
        $variant->idr_price     = $request['idr_price'];
        $variant->usd_price     = $request['usd_price'];
        $variant->stock         = $request['stock'];
        $variant->max_order     = $request['max_order'];
        $variant->min_order     = $request['min_order'];
        
        $variant->save();

        return $variant->id;
    }

    private function updateVariant(array $request, $product_id)
    {
        /** @var Variant */
        $variant                = Variant::where('id', $request['id'])
                                    ->where('product_id', $product_id)
                                    ->firstOrFail();
        
        $variant->name          = $request['name'];
        $variant->idr_price     = $request['idr_price'];
        $variant->usd_price     = $request['usd_price'];
        $variant->stock         = $request['stock'];
        $variant->max_order     = $request['max_order'];
        $variant->min_order     = $request['min_order'];
        
        $variant->save();
    }

    private function deleteVariants(string $product_id, array $undeletedIds = [])
    {
        Variant::where('product_id', $product_id)
                    ->whereNotIn('id', $undeletedIds)
                    ->delete();
    }
}
