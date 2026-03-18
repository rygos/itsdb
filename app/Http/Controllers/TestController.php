<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function test(){
        $test = Carbon::now()->diffForHumans();
    }
}
