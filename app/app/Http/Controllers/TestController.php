<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\TestBank;


class TestController extends Controller
{
    public function start()
    {

        $subjects = TestBank::select('subject')->distinct()->orderBy('subject')->get();

        $grades = TestBank::select('grade')->distinct()->orderBy('grade')->get();

        $langs = TestBank::select('lang')->distinct()->orderBy('lang')->get();

        $variants = TestBank::select('variant')->distinct()->orderBy('variant')->get();

        return view('test.start', compact(
            'subjects',
            'grades',
            'langs',
            'variants'
        ));
    }
}
