<?php

namespace App\Http\Controllers;

use App\Models\Container;
use Illuminate\Http\Request;

class ContainerController extends Controller
{
    public function show($title){
        $cont = Container::whereTitle($title)->first();

        return view('container.show', [
            'data' => $cont
        ]);
    }

    public function store(Request $request, $title){
        $cont = Container::whereTitle($title)->first();
        $cont->content = $request->get('content');
        $cont->save();

        return redirect()->route('container.show', $title);
    }
}
