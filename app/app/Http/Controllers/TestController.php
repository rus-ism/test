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

        #return redirect('/test/start');
        /* ищем доступные варианты */

        $variants = TestBank::where('subject',$request->subject)
            ->where('grade',$request->grade)
            ->where('lang',$request->lang)
            ->distinct()
            ->pluck('variant')
            ->toArray();


        if(empty($variants)){
            abort(404,'Варианты теста не найдены');
        }


        /* если вариант один */

        if(count($variants) == 1){

            $variant = $variants[0];

        } else {

            /* выбираем случайный */

            $variant = $variants[array_rand($variants)];

        }

        //if (($request->grade != '9')AND($request->school_id != 166)) {
//
  //          return redirect('/test/start');
    //        
      //  }
        
        //$variant = 1;

        $attempt = TestAttempt::create([
            'student_name' => $request->student_name,
            'subject' => trim($request->subject),
            'grade' => $request->grade,
            'variant' => $variant,//rand(1,2),//$request->variant,
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
        $questions = TestBank::where('subject', 'LIKE', '%'.$attempt->subject.'%')
            ->where('grade', 'LIKE', '%'.$attempt->grade.'%')
            ->where('variant','LIKE', $attempt->variant)
            ->where('lang', 'LIKE', '%'.$attempt->lang.'%')
            ->get();
        //dd($questions);
        $variant = $attempt->variant;
        foreach ($questions as $question) {
            $question->options = $this->replaceCyrillicHomoglyphs($question->options);
            $question->correct_answer = $this->replaceCyrillicHomoglyphs($question->correct_answer);
        }
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
                if ($questionId == 1305) {
                    //dd($studentAnswer);
                }
                if ($this->replaceCyrillicHomoglyphs($studentAnswer) == $this->replaceCyrillicHomoglyphs(trim($question->correct_answer))) {
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

                $question->correct_answer = $this->replaceCyrillicHomoglyphs($question->correct_answer);
                $correctPairs = explode(',', $question->correct_answer);
                //dd($correctPairs);
                foreach ($correctPairs as $pair) {

                    $pair = trim($pair);

                    list($row, $letter) = explode('-', $pair);
                    $letter = $this->replaceCyrillicHomoglyphs($letter);
                    $row = $this->replaceCyrillicHomoglyphs($row);
                    if (isset($studentAnswer[$row])) {
                        $studentAnswer[$row]=$this->replaceCyrillicHomoglyphs($studentAnswer[$row]);
                    }
                    //dd($studentAnswer[$row]);
                    //dd($studentAnswer);
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
            
            return view('test.result', compact('attempt','answers'))->with('replaceCyrillic', function($text) {
                return $this->replaceCyrillicHomoglyphs($text);
            });;

        }


    public function replaceCyrillicHomoglyphs(?string $text): string
    {
        // Если пришел null, возвращаем пустую строку
        if ($text === null) {
            return '';
        }
        
        $cyrillic = ['А', 'В', 'С', 'Е'];
        $latin    = ['A', 'B', 'C', 'E'];

        return str_replace($cyrillic, $latin, $text);
    }      








/**
     * Пересчёт баллов только для затронутых попыток (Химия 9 класс каз.яз.)
     */
    public function recalculateChemistry9Kaz()
    {
        // === НАСТРОЙТЕ ФИЛЬТРЫ ПОД ВАШУ БД ===
        $attempts = TestAttempt::where('subject', 'Химия')           // точное название subject
            ->where('grade', '9')
            ->where('lang', 'LIKE', '%KZ%')      // или точно 'каз', 'казахский' и т.д.
            // ->where('variant', 1)              // если нужно только один вариант
            ->get();

        $count = 0;
        foreach ($attempts as $attempt) {
            $this->recalculateSingleAttempt($attempt);
            $count++;
        }

        return response()->json([
            'success' => true,
            'recalculated_attempts' => $count,
            'message' => 'Пересчёт завершён для ' . $count . ' попыток'
        ]);
    }

/**
     * Пересчёт баллов одной попытки с защитой от дубликатов
     * (берём только самый последний ответ по каждому вопросу)
     */
    private function recalculateSingleAttempt(TestAttempt $attempt)
    {
        // Получаем ответы, группируя по question_id и оставляя только самый новый
        $answers = TestAnswer::where('attempt_id', $attempt->id)
            ->orderBy('id', 'desc')           // или 'created_at', 'desc' если поле есть
            ->get()
            ->unique('question_id');          // оставляем только первый (самый новый) для каждого вопроса

        $totalScore = 0;

        foreach ($answers as $answerRecord) {
            $question = TestBank::find($answerRecord->question_id);
            if (!$question) {
                continue;
            }

            // Распаковываем student_answer
            $raw = $answerRecord->student_answer;
            $studentAnswer = $raw;
            if (is_string($raw)) {
                $decoded = json_decode($raw, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $studentAnswer = $decoded;
                }
            }

            $pointsAwarded = 0;

            /* SINGLE */
            if ($question->type === 'Single') {
                if ($this->replaceCyrillicHomoglyphs($studentAnswer ?? '') === 
                    $this->replaceCyrillicHomoglyphs(trim($question->correct_answer ?? ''))) {
                    $pointsAwarded = (int)$question->points;
                }
            }

            /* MULTIPLE */
            elseif ($question->type === 'Multiple') {
                $correct = array_map('trim', explode(';', $question->correct_answer ?? ''));
                $student = is_array($studentAnswer) ? $studentAnswer : [];
                sort($correct);
                sort($student);
                if ($correct === $student) {
                    $pointsAwarded = (int)$question->points;
                }
            }

            /* MATCHING */
            elseif ($question->type === 'Matching') {
                $correctAnswerStr = $this->replaceCyrillicHomoglyphs($question->correct_answer ?? '');
                $correctPairs = array_filter(explode(',', $correctAnswerStr));

                foreach ($correctPairs as $pair) {
                    $pair = trim($pair);
                    if (empty($pair) || !str_contains($pair, '-')) continue;

                    [$row, $letter] = explode('-', $pair, 2);
                    $letter = $this->replaceCyrillicHomoglyphs(trim($letter));
                    $row    = $this->replaceCyrillicHomoglyphs(trim($row));

                    $studentRowAnswer = null;
                    if (isset($studentAnswer[$row]) || isset($studentAnswer[trim($row)])) {
                        $studentRowAnswer = $this->replaceCyrillicHomoglyphs(
                            $studentAnswer[$row] ?? $studentAnswer[trim($row)]
                        );
                    }

                    if ($studentRowAnswer !== null && $studentRowAnswer === $letter) {
                        $pointsAwarded += 1;
                    }
                }
            }

            /* SHORT ANSWER */
            elseif ($question->type === 'Short Answer') {
                $correct = mb_strtolower(trim($question->correct_answer ?? ''));
                $student = mb_strtolower(trim(is_string($studentAnswer) ? $studentAnswer : ''));
                if (AnswerCheckService::checkShortAnswer($correct, $student)) {
                    $pointsAwarded = (int)$question->points;
                }
            }

            // Обновляем баллы только для выбранного ответа
            $answerRecord->update(['points_awarded' => $pointsAwarded]);

            $totalScore += $pointsAwarded;
        }

        // Обновляем итоговый балл попытки
        $attempt->update(['score' => $totalScore]);
    }
}
