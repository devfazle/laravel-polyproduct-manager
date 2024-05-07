<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Color;
use App\Models\Image;
use App\Models\Product;
use App\Models\Size;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $d = Product::with('tags', 'colors', 'sizes', 'images', 'category')->get();
        return view('admin.shop.product.producttable', compact('d'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $c = Category::all();
        $t = Tag::all();
        $co = Color::all();
        $s = Size::all();
        return view('admin.shop.product.productstore', compact('c', 't', 'co', 's'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $name = $request->name;
        $description = $request->description;
        $category = $request->category;
        $tag = $request->tag;
        $color = $request->color;
        $size = $request->size;
        $image = $request->file('image');
        $price = $request->price;

        // inserting data in product table

        if ($request->has('name') && $request->has('description') && $request->has('price') && $request->has('category')) {
            Product::create([
                'name' => $name,
                'description' => $description,
                'price' => $price,
                'category_id' => $category
            ]);
        }

        $recentProduct = Product::latest()->first(); //finding recently added products

        //attaching tags
        if ($request->has('tag')) {
            foreach ($tag as $t) {
                $recentProduct->tags()->attach($t);
            }
        }

        //attaching colors

        if ($request->has('color')) {
            foreach ($color as $c) {
                $recentProduct->colors()->attach($c);
            }
        }

        //attaching sizes

        if ($request->has('size')) {
            foreach ($size as $s) {
                $recentProduct->sizes()->attach($s);
            }
        }

        //uploading and attaching images
        if ($request->has('image')) {
            $images = [];
            foreach ($image as $i) {
                $extention = $i->getClientOriginalName();
                $image_name = time() . '-n-' . $extention;
                $images[] = ['image_url' => $image_name];
                $path = 'admin/images/products/';
                $i->move($path, $image_name);
            }
            $recentProduct->images()->createMany($images);
        }

        return redirect(route('product.index'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $c = Category::all();
        $t = Tag::all();
        $co = Color::all();
        $s = Size::all();

        $p = Product::with('tags', 'colors', 'sizes', 'images', 'category')->find($id);
        return view('admin.shop.product.productupdate', compact('p', 'c', 't', 'co', 's'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $name = $request->name;
        $description = $request->description;
        $category = $request->category;
        $selectedTagIds = $request->tag;
        $selectedColorIds = $request->color;
        $selectedSizeIds = $request->size;
        $image = $request->file('image');
        $price = $request->price;

        // updating data in products table
        $p = Product::with('tags', 'colors', 'images', 'sizes')->find($id);

        if ($request->has('name') && $request->has('description') && $request->has('price') && $request->has('category')) {
            $p->update([
                'name' => $name,
                'description' => $description,
                'price' => $price,
                'category_id' => $category
            ]);
        }

        //  UPDATING TAGS --------------------------------------

        $existingTagIds = $p->tags->pluck('id')->toArray();

        if ($request->has('tag')) {
            if ($selectedTagIds !== $existingTagIds) {

                // Find the tag IDs to be added and removed
                $tagsToAdd = array_diff($selectedTagIds, $existingTagIds);
                $tagsToRemove = array_diff($existingTagIds, $selectedTagIds);

                // Add new tags
                if (!empty($tagsToAdd)) {
                    $p->tags()->attach($tagsToAdd);
                }

                // Remove unselected tags
                if (!empty($tagsToRemove)) {
                    $p->tags()->detach($tagsToRemove);
                }
            }
        } else {
            $p->tags()->detach($existingTagIds);
        }

        // Updating Colors -----------------------------------------
        $existingColorIds = $p->colors->pluck('id')->toArray();

        if ($request->has('color')) {
            if ($selectedColorIds !== $existingColorIds) {

                $colorsToAdd = array_diff($selectedColorIds, $existingColorIds);
                $colorsToRemove = array_diff($existingColorIds, $selectedColorIds);

                if (!empty($colorsToAdd)) {
                    $p->colors()->attach($colorsToAdd);
                }

                if (!empty($colorsToRemove)) {
                    $p->colors()->detach($colorsToRemove);
                }
            }
        } else {
            $p->colors()->detach($existingColorIds);
        }

        // Updating Sizes --------------------------------------------

        $existingSizeIds = $p->sizes->pluck('id')->toArray();

        if ($request->has('size')) {
            if ($selectedSizeIds !== $existingSizeIds) {

                // Find the size IDs to be added and removed
                $sizesToAdd = array_diff($selectedSizeIds, $existingSizeIds);
                $sizesToRemove = array_diff($existingSizeIds, $selectedSizeIds);

                // Add new sizes
                if (!empty($sizesToAdd)) {
                    $p->sizes()->attach($sizesToAdd);
                }

                // Remove unselected sizes
                if (!empty($sizesToRemove)) {
                    $p->sizes()->detach($sizesToRemove);
                }
            }
        } else {
            $p->sizes()->detach($existingSizeIds);
        }

        // Updating Images

        if ($request->has('removed_image_ids')) {
            $imagesRemoved = $request->removed_image_ids;
            foreach ($imagesRemoved as $i) {
                $img = Image::find($i); 
                if (File::exists('admin/images/products/' . $img->image_url)) {
                    File::delete('admin/images/products/' . $img->image_url);
                }
            }
            $p->images()->detach($imagesRemoved);
            Image::destroy($imagesRemoved);
        }

        if ($request->has('image')) {
            $images = [];
            foreach ($image as $i) {
                $extention = $i->getClientOriginalName();
                $image_name = time() . '-n-' . $extention;
                $images[] = ['image_url' => $image_name];
                $path = 'admin/images/products/';
                $i->move($path, $image_name);
            }
            $p->images()->createMany($images);
        }
        return redirect(route('product.index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $p = Product::with('tags', 'colors', 'sizes', 'images', 'category')->find($id);

        //detaching tags
        $tags = [];
        foreach ($p->tags as $t) {
            $tags[] = $t->id;
        }
        $p->tags()->detach($tags);

        //detaching colors
        $colors = [];
        foreach ($p->colors as $c) {
            $colors[] = $c->id;
        }
        $p->colors()->detach($colors);

        //detaching sizes
        $sizes = [];
        foreach ($p->sizes as $s) {
            $sizes[] = $s->id;
        }
        $p->sizes()->detach($sizes);

        //detaching, destroying and deleting images
        $images = [];
        foreach ($p->images as $i) {
            $images[] = $i->id;
            if (File::exists('admin/images/products/' . $i->image_url)) {
                File::delete('admin/images/products/' . $i->image_url);
            }
        }
        $p->images()->detach($images);
        Image::destroy($images);

        $p->delete();
        return redirect(route('product.index'));
    }
}
