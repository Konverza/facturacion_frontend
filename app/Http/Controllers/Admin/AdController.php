<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdBanner;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class AdController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ads = AdBanner::all();
        return view('admin.ads.index', compact('ads'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.ads.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'image_file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:3072',
                'link_url' => 'required|url',
            ]);

            DB::beginTransaction();

            $image_path = $request->file('image_file')->store('ads', 'public');
            $path = "/storage/$image_path";

            $ad = new AdBanner([
                'name' => $request->input('name'),
                'image_path' => $path,
                'link_url' => $request->input('link_url'),
            ]);

            $ad->save();
            DB::commit();

            return redirect()->route('admin.ads.index')->with('success', 'Anuncio creado exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error al guardar el anuncio: ' . $e->getMessage());
            DB::rollBack();
            return back()->with('error', 'Error')->with("error_message", "Ha ocurrido un error al guardar el anuncio")->withInput();
        }
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
        $ad = AdBanner::findOrFail($id);
        return view('admin.ads.edit', compact('ad'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:3072',
                'link_url' => 'required|url',
            ]);

            $ad = AdBanner::findOrFail($id);

            if ($request->hasFile('image_file')) {
                // Delete the old image file if it exists
                if ($ad->image_path) {
                    \Storage::disk('public')->delete(str_replace('/storage/', '', $ad->image_path));
                }
                $image_path = $request->file('image_file')->store('ads', 'public');
                $ad->image_path = "/storage/$image_path";
            }

            $ad->name = $request->input('name');
            $ad->link_url = $request->input('link_url');
            $ad->save();

            return redirect()->route('admin.ads.index')->with('success', 'Anuncio actualizado exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error al actualizar el anuncio: ' . $e->getMessage());
            return back()->with('error', 'Error')->with("error_message", "Ha ocurrido un error al actualizar el anuncio")->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $ad = AdBanner::findOrFail($id);
            if ($ad->image_path) {
                // Delete the image file from storage
                \Storage::disk('public')->delete(str_replace('/storage/', '', $ad->image_path));
            }
            $ad->delete();
            return redirect()->route('admin.ads.index')->with('success', 'Anuncio eliminado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al eliminar el anuncio: ' . $e->getMessage());
            return back()->with('error', 'Error')->with("error_message", "Ha ocurrido un error al eliminar el anuncio");
        }
    }
}
