<?php

namespace App\Http\Controllers\Admin\BrandKit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

final class BrandKitController extends Controller
{
    public function index(Request $request)
    {
        // MVP: endpoint "wiring OK" enquanto a UI não chega.
        // Depois: render da view do Brand Kit.
        if ($request->expectsJson()) {
            return response()->json([
                'ok'      => true,
                'message' => 'Brand Kit UI wiring OK',
            ]);
        }

        return view('admin::brandkit.index'); // ajuste quando você criar a view
    }
}
