<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SocialMedia;
use Illuminate\Support\Facades\Validator;

class SocialMediaController extends Controller
{
    public function all(Request $request){
        $userProfileId = $request->input('user_profile_id');

        $socialMedia = SocialMedia::with(['userProfile'])->where('user_profile_id', $userProfileId)->get();

        return ResponseFormatter::success($socialMedia, 'Data social media user berhasil diambil');

    }

    public function update(Request $request, $id){
        $socialMedia = SocialMedia::find($id);
        $validasi = Validator::make($request->all(), [
            'type' => 'required|string',
            'account' => 'required|string|max:50',  
        ]);

        if ($validasi->fails()) {
            return ResponseFormatter::error($validasi->errors(), $validasi->errors()->messages());
        }

        $socialMedia->type = $request->type;
        $socialMedia->account = $request->account;

        $socialMedia->save();
        return ResponseFormatter::success($socialMedia, 'Data social media user berhasil diubah');
    }


    public function store(Request $request){
        $validasi = Validator::make($request->all(), [
            'type' => 'required|string',
            'account' => 'required|string|max:50',
            'user_profile_id' => 'required|exists:user_profiles,id'        
        ]);

        if ($validasi->fails()) {
            $errors = $validasi->errors();
            return ResponseFormatter::error([
                'message' => 'Gagal membuat social media user',
                'error' => $errors->first(),
            ], 'Failed', 400);
        }

        $socialMedia = SocialMedia::create($request->all());
        return ResponseFormatter::success($socialMedia, 'Data social media berhasil ditambahkan');
    }

    
    public function destroy($id)
    {
        $socialMedia = SocialMedia::find($id);

        if (!$socialMedia) {
            return ResponseFormatter::error([
                'message' => 'Data social media tidak ditemukan!',
            ], 'Failed', 404);
        }else{
            $socialMedia->delete();
            return ResponseFormatter::success(null, 'Berhasil menghapus social media');
        }
    }
}
