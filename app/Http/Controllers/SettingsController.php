<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class SettingsController extends Controller
{
    // get /api/settings (roles 1,2)
    public function show()
    {
        $s = DB::table('settings')->orderBy('id')->first();
        return response()->json([
            'data' => $s,
        ], 200);
    }

    // put /api/settings (roles 1,2)
    public function update(Request $request)
    {
        $data = Validator::make($request->all(), [
            'gym_name' => 'required|string|max:100',
            'gym_email' => 'nullable|email|max:100',
            'version' => 'required|string|max:20',
            'developer_name' => 'required|string|max:100',
        ])->validate();

        $row = DB::table('settings')->orderBy('id')->first();
        DB::table('settings')->where('id', $row->id)->update($data + ['updated_at' => now()]);

        $updated = DB::table('settings')->where('id', $row->id)->first();
        return response()->json(['data' => $updated], 200);
    }
}
