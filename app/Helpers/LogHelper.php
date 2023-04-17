<?php

namespace App\Helpers;

use App\Models\Log;
use Illuminate\Routing\Route;

class LogHelper{
    public static function log($section, $type, $msg){
        $log = new Log;
        $log->user_id = \Auth::id();
        $log->section = $section;
        $log->type = $type;
        $log->msg = $msg;
        $log->save();
    }
}
