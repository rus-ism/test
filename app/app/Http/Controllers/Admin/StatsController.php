<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\TestBank;
use App\Models\TestAttempt;
use App\Models\District;
use App\Models\School;
use App\Models\TestAnswer;

class StatsController extends Controller
{
    public function districts()
    {

        $districts = \App\Models\District::with(['schools'])
            ->where('id','!=',1)
            ->get();

        /* Общая статистика */
         $GeneralStat = \App\Models\TestAttempt::selectRaw('
                COUNT(*) as total,
                AVG(score) as avg_score
            ')
            ->whereNotNull('test_attempts.finished_at')
            ->get();      

        /* статистика районов */
        $districtStats = \App\Models\TestAttempt::selectRaw('
                district_id,
                COUNT(*) as total,
                AVG(score) as avg_score
            ')
            ->groupBy('district_id')
            ->whereNotNull('test_attempts.finished_at')
            ->get()
            ->keyBy('district_id');

        /* статистика школ */
        $schoolStats = \App\Models\TestAttempt::selectRaw('
                school_id,
                COUNT(*) as total,
                AVG(score) as avg_score
            ')
            ->groupBy('school_id')
            ->whereNotNull('finished_at')
            ->get()
            ->keyBy('school_id');

        return view('admin.districts', compact(
            'GeneralStat',
            'districts',
            'districtStats',
            'schoolStats'
        ));

    }

    public function district($id)
    {

        $district = \App\Models\District::findOrFail($id);

        $stats = \App\Models\TestAttempt::selectRaw('
                schools.id,
                schools.name_ru,
                COUNT(test_attempts.id) as total,
                AVG(test_attempts.score) as avg_score
            ')
            ->join('schools','schools.id','=','test_attempts.school_id')
            ->where('test_attempts.district_id',$id)
            ->whereNotNull('finished_at')
            ->groupBy('schools.id','schools.name_ru')
            ->orderBy('schools.name_ru')
            ->get();

        return view('admin.schools', compact('stats','district'));

    }



    public function school($id)
    {

        $school = \App\Models\School::findOrFail($id);

        /* классы */

        $classes = \App\Models\TestAttempt::selectRaw('
                grade,
                COUNT(*) as total,
                AVG(score) as avg_score
            ')
            ->where('school_id',$id)
            ->groupBy('grade')
            ->orderBy('grade')
            ->get();

        /* предметы внутри классов */

        $subjects = \App\Models\TestAttempt::selectRaw('
                grade,
                subject,
                COUNT(*) as total,
                AVG(score) as avg_score
            ')
            ->where('school_id',$id)
            ->groupBy('grade','subject')
            ->get()
            ->groupBy('grade'); // важно!

        return view('admin.school', compact(
            'school',
            'classes',
            'subjects'
        ));

    }    


  
    
    
    
public function subjectStats($school, $grade, $subject)
{

    $subject = urldecode($subject);

    $attempts = \App\Models\TestAttempt::where('school_id',$school)
        ->where('grade',$grade)
        ->where('subject',$subject)
        ->whereNotNull('finished_at')
        ->orderBy('score','desc')
        ->get();

    return view('admin.subject', compact(
        'attempts',
        'grade',
        'subject'
    ));

}   


public function subjectAnalysis(Request $request)
{

    $subject = $request->subject;
    $grade = $request->grade;

    /* список для фильтра */

    $subjects = \App\Models\TestBank::select('subject')->distinct()->get();
    $grades = \App\Models\TestBank::select('grade')->distinct()->get();

    $result = [];

    if ($subject && $grade) {

    $attempts = TestAttempt::where('subject',$subject)
        ->where('grade',$grade)
        ->whereNotNull('finished_at')
        ->get();

        /* группировка по язык + вариант */

        $groups = $attempts->groupBy(function($a){
            return $a->lang . '_' . $a->variant;
        });

        foreach ($groups as $key => $groupAttempts) {

            $attemptIds = $groupAttempts->pluck('id');

            $participants = $attemptIds->count();

            $first = $groupAttempts->first();

            $questions = \App\Models\TestBank::where('subject',$subject)
                ->where('grade',$grade)
                ->where('lang',$first->lang)
                ->where('variant',$first->variant)
                ->get();

            $stats = [];

            foreach ($questions as $q) {

                $total = \App\Models\TestAnswer::whereIn('attempt_id',$attemptIds)
                    ->where('question_id',$q->id)
                    ->count();

                $correct = \App\Models\TestAnswer::whereIn('attempt_id',$attemptIds)
                    ->where('question_id',$q->id)
                    ->where('points_awarded','>',0)
                    ->count();

                $percent = $total > 0 ? round(($correct/$total)*100,2) : 0;

                $stats[] = [
                    'question' => $q->question,
                    'correct' => $correct,
                    'percent' => $percent
                ];
            }

            $result[] = [
                'lang' => $first->lang,
                'variant' => $first->variant,
                'participants' => $participants,
                'stats' => $stats,
                'topEasy' => collect($stats)->sortByDesc('percent')->take(5),
                'topHard' => collect($stats)->sortBy('percent')->take(5),
            ];
        }
    }

    return view('admin.subject_analysis', compact(
        'subjects',
        'grades',
        'result',
        'subject',
        'grade'
    ));

}

}
