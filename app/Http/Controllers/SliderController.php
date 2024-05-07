<?php

namespace App\Http\Controllers;

use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SliderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $d = Slider::all();
        return view('admin.home.slider.slidertable', compact('d'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.slider');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($request->has('photo')) {

            $file = $request->file('photo');
            $extention = $file->getClientOriginalName();
            $image_name = time() . '.' . $extention;
            $path = 'admin/images/';
            $file->move($path, $image_name);
        }

        Slider::create([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'photo' => $image_name,
        ]);

        return redirect(route('slide.index'));
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
        $d = Slider::findOrFail($id);
        return view('admin.home.slider.slideredit', compact('d'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $s = Slider::findOrFail($id);

        if ($request->has('photo')) {

            $file = $request->file('photo');
            $extention = $file->getClientOriginalName();
            $image_name = time() . '.' . $extention;
            $path = 'admin/images/';
            $file->move($path, $image_name);

            $s->update([
                'title' => $request->title,
                'subtitle' => $request->subtitle,
                'photo' => $image_name,
            ]);
        } else {
            $s->update([
                'title' => $request->title,
                'subtitle' => $request->subtitle,
            ]);
        }

        return redirect(route('slide.index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = Slider::find($id);
        if (File::exists('admin/images/' . $data->photo)) {
            File::delete('admin/images/' . $data->photo);
        }
        $data->delete();
        return redirect(route('slide.index'));
    }
}
