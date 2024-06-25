<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MatchUser;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MatchUserController extends Controller
{

    public function all_public(){
        $matchusers = MatchUser::with(['userMatch', 'user'])->get();
        return ResponseFormatter::success($matchusers, 'Berhasil ambil data match');
    }

    public function all(Request $request)
    {
        $userid = Auth::user()->id;
        $matchusers = MatchUser::with(['userMatch'])->where('user_id', $userid)->get();
        return ResponseFormatter::success($matchusers, 'Berhasil ambil data match');
    }
    

    public function store(Request $request)
    {
        $userid = Auth::user()->id;
        Validator::make($request->all(), [
            'user_match_id' => 'required|exists:users,id|numeric',
        ]);

        $match = MatchUser::create([
            'user_id' => $userid,
            'user_match_id' => intval($request->user_match_id),
        ]);
        return ResponseFormatter::success($match, 'Berhasil match');
    }

    public function destroy($id)
    {
        $match = MatchUser::find($id);

        if (!$match) {
            return ResponseFormatter::error([
                'message' => 'Data match tidak ditemukan!',
            ], 'Failed', 404);
        }else{
            $match->delete();
            return ResponseFormatter::success(null, 'Berhasil menghapus match');
        }
    }
}
