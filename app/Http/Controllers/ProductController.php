<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Http\Resources\CategoryResource;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $user = $request->user();

        return ProductResource::collection(Product::where('user_id', $user->id)->orderBy("created_at", "desc")->paginate(5));
        
    }

    public function viewPublicProducts(Request $request){
        return ProductResource::collection((new Product())->orderBy("created_at", "desc")->limit(300)->paginate(5));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreProductRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProductRequest $request)
    {
        //
        $data = $request->validated();
        // Check if image was given and save on local file system

        if (isset($data['image'])){
            $relativePath = $this->saveImage($data['image']);
            $data['image'] = $relativePath;
        }

        $product = Product::create($data);

        return new ProductResource($product);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product, Request $request)
    {
        //
        $user = $request->user();
        if ($user->id != $product->user_id){
            return abort(403, 'Unauthorized action.');
        }
        return new ProductResource($product);
    }

    public function activate(Request $request){
        $idProduct = $request->input('id');

        Product::where('id', $idProduct)->update(['activate' => true]);

        return response(['success' => true]);
    }

    public function desactivate(Request $request){
        $idProduct = $request->input('id');

        Product::where('id', $idProduct)->update(['activate' => false]);

        return response(['success' => true]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateProductRequest  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        //
        $data = $request->validated();

        // Check if image was given and save on local file system
        if (isset($data['image'])){
            $relativePath = $this->saveImage($data['image']);
            $data['image'] = $relativePath;

            // If there is an old image, delete it
            if ($product->image){
                $absolutePath = public_path($product->image);
                File::delete($absolutePath);
            }
        }

        // Update survey in the database
        $product->update($data);

        return new ProductResource($product);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product, Request $request)
    {
        //
        $user = $request->user();
        if ($user->id != $product->user_id){
            return abort(403, 'Unauthorized action.');
        }
        
        $product->delete();

        // If there is an old image, delete it
        if ($product->image){
            $absolutePath = public_path($product->image);
            File::delete($absolutePath);
        }

        return response('', 204);
    }

    private function saveImage($image){
        // Check if image is valid base64 string
        if (preg_match('/^data:image\/(\w+);base64,/', $image, $type)){
            // Take out the base64 encoded text without mime type
            $image = substr($image, strpos($image, ',') + 1);

            // Get the file extension
            $type = strtolower($type[1]); // jpg, png, gif

            // check if file is an image
            if (!in_array($type, ['jpg', 'jpeg', 'png', 'gif', 'webp'])){
                throw new \Exception('invalid image type');
            }
            $image = str_replace(' ', '+', $image);
            $image = base64_decode($image);

            if ($image === false){
                throw new \Exception('base64_decode failed');
            }
        }else{
            throw new \Exception('did not match data URI with image data.');
        }

        $dir = 'products/';
        $file = Str::random() . '.' . $type;
        $absolutePath = public_path($dir);
        $relativePath = $dir . $file;

        if (!File::exists($absolutePath)){
            File::makeDirectory($absolutePath, 0755, true);
        }
        file_put_contents($relativePath, $image);

        return $relativePath;
    }

    public function categories(){
        return CategoryResource::collection(Category::all());
    }

    public function seedCategories(){
        $categories = [
            'Vacances',
            'Emploi',
            'Vehicules',
            'Immobilier',
            'Mode',
            'Maison',
            'Multimedia',
            'Losirs',
            'Animaux',
            'Materiel Professionnel',
            'Services',
            'Divers'
        ];

        foreach($categories as $categoryName){
            Category::create([
                'name' => $categoryName
            ]);
        }

        return response([
            'success' => true,
            'message' => 'Les categories ont ete creees'
        ]);
    }
}
