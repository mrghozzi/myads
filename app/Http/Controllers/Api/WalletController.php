<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function balance()
    {
        $user = Auth::user();

        return response()->json([
            'pts' => $user->pts,
            'ad_credits' => [
                'banner' => $user->nbanner ?? 0,
                'link' => $user->nlink ?? 0,
                'smart' => $user->nsmart ?? 0,
            ]
        ]);
    }
}
