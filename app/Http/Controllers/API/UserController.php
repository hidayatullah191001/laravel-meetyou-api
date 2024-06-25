<?php

namespace App\Http\Controllers\API;

use App\Actions\Fortify\PasswordValidationRules;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\MatchUser;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    use PasswordValidationRules;

    public function login(Request $request)
    {
       try {
        $request->validate([
            'email' => 'email|required|string',
            'password' => 'required|string|min:5'
        ]);
        $credential = request(['email', 'password']);
        
        if(!Auth::attempt($credential)){
            return ResponseFormatter::error([
                'message' => 'Unauthorized'
            ],'Authentication failed', 500);
        }

        $user = User::where('email', $request->email)->first();

        if (!Hash::check($request->password, $user->password)) {
            throw new \Exception('Invalid Credentials');
        }

        $tokenResult = $user->createToken('authToken')->plainTextToken;

        return ResponseFormatter::success([
            'access_token' => $tokenResult,
            'token_type' => 'Bearer',
            'user'=>$user
        ], 'Authenticated');

       } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error,
            ], 'Authentication failed', 500);
       }
    }

    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => $this->passwordRules(),
                'password_confirmation' => ['required', 'string', 'same:password'],
                'image_profile' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
    
            if ($validator->fails()) {
                $errors = $validator->errors();
                if ($errors->has('email')) {
                    $errors->add('email', 'Email telah terdaftar');
                    return ResponseFormatter::error([
                        'message' => 'Gagal',
                        'error' => $errors->first('email'),
                    ], 'Registered Failed', 422);
                }
                if ($errors->has('password_confirmation')) {
                    $errors->add('password_confirmation', 'Konfirmasi password tidak sesuai');
                    return ResponseFormatter::error([
                        'message' => 'Gagal',
                        'error' => $errors->first('password'),
                    ], 'Registered Failed', 422);
                }
            }
            $hashedPassword = Hash::make($request->input('password'));
            $userData = $request->all();
            $userData['password'] = $hashedPassword;

            if($request->file('image_profile') != null){
                $imagePath = $request->file('image_profile')->store('profile_images', 'public');
            }else{
                $imagePath = null;
            }

            $userData['image_profile'] = $imagePath;

            $dateOfBirth = new \DateTime($request->date_of_birth);
            if($dateOfBirth != null){
                $now = new \DateTime();
                $age = $now->diff($dateOfBirth)->y;
            }else{
                $age = null;
            }
            $userData['age'] = $age; 
            $user = User::create($userData);

            $user = User::where('email', $request->email)->first();
            $tokenResult = $user->createToken('authToken')->plainTextToken;
    
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user'=>$user
            ], 'User Registered');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error,
            ], 'Authentication failed', 500);
        }
    }

    public function fetch(Request $request)
    {
        // $userid = Auth::user()->id;
        // $user = User::with(['user_profile'])->where('id', $userid)->get();
        // return ResponseFormatter::success($user, 'Data Profile user berhasil diambil');
        return ResponseFormatter::success($request->user(), 'Data Profile user berhasil diambil');
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        // Perbarui data user yang masih null

        $user->date_of_birth = $request->date_of_birth;
        $user->occupation = $request->occupation;
        $user->no_telephone = $request->no_telephone;

        if ($request->hasFile('image_profile')) {
            // Hapus gambar lama jika ada
            if ($user->image_profile) {
                Storage::delete($user->image_profile);
            }

            // Simpan gambar baru
            $imagePath = $request->file('image_profile')->store('profile_images', 'public');
            $user->image_profile = $imagePath;
        }

        $user->save();
        return ResponseFormatter::success($user, 'Berhasil update data user');

    }

    public function user_to_match_all(Request $request){
        $userid = Auth::user()->id;
        $userGender = Auth::user()->gender;
        $limit = $request->input('limit', 10);

        $userMatchIds = MatchUser::pluck('user_match_id')->toArray();
        $excludedUserIds = array_merge([$userid], $userMatchIds);

        $users = User::whereNotIn('id', $excludedUserIds)
                ->where('gender', '!=', Auth::user()->gender)
                ->inRandomOrder()
                ->with('user_profile');
        return ResponseFormatter::success($users->paginate($limit), 'Data berhasil diambil');
    }

    public function logout(Request $request)
    {
        $token = $request->user()->currentAccessToken()->delete();
        return ResponseFormatter::success($token, 'Token Revoked');
    }
}
