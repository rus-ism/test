<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\TestBank;
use App\Models\TestAttempt;
use App\Models\District;
use App\Models\School;
use App\Models\TestAnswer;

use App\Services\AnswerCheckService;


class TestController extends Controller
{
    public function start()
    {
        $districts = District::orderBy('name_ru')->get();

        $subjects = TestBank::select('subject')->distinct()->orderBy('subject')->get();
        $grades = TestBank::select('grade')->distinct()->orderBy('grade')->get();
        $langs = TestBank::select('lang')->distinct()->orderBy('lang')->get();
        $variants = TestBank::select('variant')->distinct()->orderBy('variant')->get();
        $variants = rand(1,2);

        return view('test.start', compact(
            'districts',
            'subjects',
            'grades',
            'langs',
            'variants'
        ));
    }





    public function startTest(Request $request)
    {
        //dd($request);
        $attempt = TestAttempt::create([
            'student_name' => $request->student_name,
            'subject' => trim($request->subject),
            'grade' => $request->grade,
            'variant' => rand(1,2),//$request->variant,
            'lang' => $request->lang,
            'district_id' => $request->district_id,
            'school_id' => $request->school_id,
            'score' => 0,
            'started_at' => now()
        ]);

        return redirect('/test/'.$attempt->id);

    }

    public function showTest($attemptId)
    {

        $attempt = TestAttempt::findOrFail($attemptId);
       //dd($attempt);
        $questions = TestBank::where('subject', 'LIKE', $attempt->subject)
            ->where('grade', 'LIKE', $attempt->grade)
            ->where('variant','LIKE', $attempt->variant)
            ->where('lang', 'LIKE', $attempt->lang)
            ->get();
        
        return view('test.test', compact('questions','attempt'));

    }    


    public function submitTest(Request $request, $attemptId)
    {

        $attempt = TestAttempt::findOrFail($attemptId);

        $answers = $request->answers ?? [];

        $totalScore = 0;

        foreach ($answers as $questionId => $studentAnswer) {

            $question = TestBank::find($questionId);

            $pointsAwarded = 0;

            /* SINGLE */

            if ($question->type == 'Single') {

                if ($studentAnswer == trim($question->correct_answer)) {
                    $pointsAwarded = $question->points;
                }

            }

            /* MULTIPLE */

            elseif ($question->type == 'Multiple') {

                $correct = array_map('trim', explode(';', $question->correct_answer));

                $student = $studentAnswer ?? [];

                sort($correct);
                sort($student);

                if ($correct == $student) {
                    $pointsAwarded = $question->points;
                }

            }

            /* MATCHING */

            elseif ($question->type == 'Matching') {

                $correctPairs = explode(',', $question->correct_answer);

                foreach ($correctPairs as $pair) {

                    $pair = trim($pair);

                    list($row, $letter) = explode('-', $pair);

                    if (
                        isset($studentAnswer[$row]) &&
                        $studentAnswer[$row] == $letter
                    ) {
                        $pointsAwarded += 1;
                    }

                }

            }

            /* SHORT ANSWER */
            
            elseif ($question->type == 'Short Answer') {

                $correct = mb_strtolower(trim($question->correct_answer));

                $student = mb_strtolower(trim($studentAnswer));

                if (AnswerCheckService::checkShortAnswer($correct,$student)) {
                    $pointsAwarded = $question->points;
                }

            }

            /* сохраняем ответ */

            TestAnswer::create([

                'attempt_id' => $attempt->id,
                'question_id' => $question->id,
                'student_answer' => is_array($studentAnswer)
                    ? json_encode($studentAnswer)
                    : $studentAnswer,
                'points_awarded' => $pointsAwarded

            ]);

            $totalScore += $pointsAwarded;

        }

        /* сохраняем результат */

        $attempt->update([

            'score' => $totalScore,
            'finished_at' => now()

        ]);

        return redirect('/test/result/'.$attempt->id);

    }    



    public function getSchools($district)
    {

        $schools = School::where('district_id',$district)
            ->orderBy('name_ru')
            ->get();

        return response()->json($schools);

    }    


    public function result($attemptId)
        {
            $attempt = TestAttempt::findOrFail($attemptId);

            $answers = TestAnswer::where('attempt_id',$attemptId)
                ->with('question')
                ->get();

            return view('test.result', compact('attempt','answers'));

        }
}
