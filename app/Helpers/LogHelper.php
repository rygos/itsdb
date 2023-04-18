<?php

namespace App\Helpers;

use App\Models\Log;
use Illuminate\Routing\Route;

class LogHelper{
    public static function log($section, $content_id, $type, $msg){
        $log = new Log;
        $log->user_id = \Auth::id();
        $log->section = $section;
        $log->content_id = $content_id;
        $log->type = $type;
        $log->msg = $msg;
        $log->save();
    }
}
