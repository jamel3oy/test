<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestCT extends Controller
{
    public function test() {
        return DB::table("npu_proj")->where('bYear', 2566)->where('id', "<>", 89)
            ->get();
    }
}
