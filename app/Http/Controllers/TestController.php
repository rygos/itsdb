<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function test(){
        LogHelper::log('test');
    }
}
