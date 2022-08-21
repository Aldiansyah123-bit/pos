<?php

namespace App\Http\Controllers\Apps;


use Inertia\Inertia;
use App\Models\Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::when(request()->q, function($categories) {
            $categories = $categories->where('name', 'like', '%'. request()->q . '%');
        })->latest()->paginate(5);

        //return inertia
        return Inertia::render('Apps/Categories/Index', [
            'categories' => $categories,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('Apps/Categories/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'image'         => 'required|image|mimes:jpeg,jpg,png|max:2000',
            'name'          => 'required|unique:categories',
            'description'   => 'required'
        ]);

        //upload image
        $image = $request->file('image');
        $image->storeAs('public/categories', $image->hashName());

        //create category
        Category::create([
            'image'         => $image->hashName(),
            'name'          => $request->name,
            'description'   => $request->description
        ]);

        //redirect
        return redirect()->route('apps.categories.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        return Inertia::render('Apps/Categories/Edit', [
            'category' => $category,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        $this->validate($request, [
            'name'          => 'required|unique:categories,name,'.$category->id,
            'description'   => 'required'
        ]);

        //check image update
        if ($request->file('image')) {

            //remove old image
            Storage::disk('local')->delete('public/categories/'.basename($category->image));

            //upload new image
            $image = $request->file('image');
            $image->storeAs('public/categories', $image->hashName());

            //update category with new image
            $category->update([
                'image'=> $image->hashName(),
                'name' => $request->name,
                'description'   => $request->description
            ]);

        }

        //update category without image
        $category->update([
            'name'          => $request->name,
            'description'   => $request->description
        ]);

        //redirect
        return redirect()->route('apps.categories.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        //remove image
        Storage::disk('local')->delete('public/categories/'.basename($category->image));

        //delete
        $category->delete();

        //redirect
        return redirect()->route('apps.categories.index');
    }
}
