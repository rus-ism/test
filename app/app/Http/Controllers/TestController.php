<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\TestBank;
use App\Models\TestAttempt;



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


    public function startTest(Request $request)
    {

        $attempt = TestAttempt::create([
            'student_name' => $request->student_name,
            'subject' => $request->subject,
            'grade' => $request->grade,
            'variant' => $request->variant,
            'lang' => $request->lang,
            'score' => 0,
            'started_at' => now()
        ]);

        return redirect('/test/'.$attempt->id);

    } 

    public function showTest($attemptId)
    {

        $attempt = TestAttempt::findOrFail($attemptId);

        $questions = TestBank::where('subject',$attempt->subject)
            ->where('grade',$attempt->grade)
            ->where('variant',$attempt->variant)
            ->where('lang',$attempt->lang)
            ->get();

        return view('test.test', compact('questions','attempt'));

    }    
}
