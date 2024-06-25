<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Helpers\ResponseFormatter;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GalleryController extends Controller
{
    public function all(Request $request){
        
        $userProfileId = $request->input('user_profile_id');

        $galleries = Gallery::where('user_profile_id', $userProfileId)->get();

        return ResponseFormatter::success($galleries, 'Data user gallery berhasil diambil');

    }

    public function store(Request $request){
        $validasi = Validator::make($request->all(), [
            'user_profile_id' => 'required|exists:user_profiles,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if($validasi->fails()){
            return ResponseFormatter::error($validasi->errors(), 'Data user gallery gagal diambil');
        }
        $imagePath = $request->file('image')->store('gallery_images', 'public');
        
        $gallery = Gallery::create([
            'user_profile_id' => $request->user_profile_id,
            'image_gallery' => $imagePath,
        ]);

        return ResponseFormatter::success($gallery, 'Data gallery berhasil disimpan');
    }

    public function destroy($id)
    {
        $gallery = Gallery::find($id);
        if (!$gallery) {
            return ResponseFormatter::error([
              'message' => 'Data gallery tidak ditemukan'
            ], 'Data gallery tidak ditemukan', 404);
        }else{
            $gallery->delete();
            return ResponseFormatter::success(null, 'Berhasil menghapus gallery');
        }
    }
}
