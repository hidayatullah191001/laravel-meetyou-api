<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Helpers\ResponseFormatter;
use App\Models\Gallery;
use App\Models\SocialMedia;
use App\Models\UserProfile;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserProfileController extends Controller
{

    public function all(Request $request)
    {
        $id = $request->input('id');
        $userId = $request->input('user_id');

        if ($id) 
        {
            $userProfile = UserProfile::find($id);
            if ($userProfile) {
                return ResponseFormatter::success($userProfile, 'Data user profile berhasil diambil');
            }else{
                return ResponseFormatter::error(null, 'Data user profile tidak ditemukan', 404);
            }
        }

        $userProfile = UserProfile::with(['user', 'socialMedias', 'galleries'])->where('user_id', Auth::user()->id);

        if($userId){
            $userProfile->where('user_id', $userId); 
        }

        return ResponseFormatter::success($userProfile->get(), 'Data user profile berhasil diambil');
    }
    

    public function store(Request $request){
        try {
            $user = Auth::user();
            $validasi = Validator::make($request->all(), [
                'user_id'=> 'exists|users,id',
                'description' => 'required',
            ]);

            $dataUser = UserProfile::where('user_id', $user->id)->count();

            if ($dataUser >= 1) {
                return ResponseFormatter::error([
                    'error' => 'Already have a user profile',
                ], 'Failed', 400);
            }

            if ($validasi->fails()) {
                $errors = $validasi->errors();
                return ResponseFormatter::error([
                    'message' => 'Gagal membuat user profile',
                    'error' => $errors->first(),
                ], 'Failed', 400);
            }

            $userProfile = UserProfile::create([
                'user_id' => $user->id,
                'description' => $request->description,
            ]);
            
            return ResponseFormatter::success($userProfile, 'Data user profile created successfully');

        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong!',
                'error' => $error,
            ], 'Failed', 500);
        }
    }

    public function update(Request $request, $id)
    {
        $userProfile = UserProfile::find($id);
        $validasi = Validator::make($request->all(), [
            'description' => 'required',
        ]);

        if($validasi->fails())
        {
            return ResponseFormatter::error($validasi->errors(), $validasi->errors()->messages() );
        }

        $userProfile->description = $request->description;
        $userProfile->save();
        return ResponseFormatter::success($userProfile, 'Data user profile berhasil diperbarui');
    }

    public function destroy($id)
    {
        $userProfile = UserProfile::find($id);
        if (!$userProfile) {
            return ResponseFormatter::error(null, 'Data user profile tidak ditemukan', 404);
        }
        $socialMedia = SocialMedia::where('user_profile_id', $id);
        $gallery = Gallery::where('user_profile_id', $id);
        $userProfile->delete();
        return ResponseFormatter::success($userProfile, 'Data user profile berhasil dihapus');
    }
}
