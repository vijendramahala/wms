<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    public function test(){
        $data = [
            'title' => 'Welcome to the API',
            'message' => 'This content is not stored in database.',
            'status' => true
        ];
        return response()->json($data);
    }
}
