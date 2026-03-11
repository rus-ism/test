<?php

namespace App\Services;

use OpenAI;

class AnswerCheckService
{

    public static function normalize($text)
    {
        $text = mb_strtolower($text);

        $text = trim($text);

        $text = preg_replace('/[^\p{L}\p{N}\s]/u','',$text);

        $text = preg_replace('/\s+/',' ',$text);

        return $text;
    }



    public static function checkShortAnswer($correct,$student)
    {

        $correct = self::normalize($correct);

        $student = self::normalize($student);



        if ($student == '') {
            return false;
        }



        /* 1. точное совпадение */

        if ($correct == $student) {
            return true;
        }



        /* 2. числовые ответы */

        if (is_numeric($correct)) {

            preg_match('/[0-9]+/',$student,$m);

            if(isset($m[0]) && $m[0] == $correct){
                return true;
            }

        }



        /* 3. близкое совпадение */

        $distance = levenshtein($correct,$student);

        if ($distance <= 2) {
            return true;
        }



        /* 4. ключевые слова */

        if (str_contains($student,$correct)) {
            return true;
        }



        /* 5. проверка через ИИ */

        return self::checkWithAI($correct,$student);

    }



    private static function checkWithAI($correct,$student)
    {

        $client = OpenAI::client(env('OPENAI_API_KEY'));

        $response = $client->chat()->create([

                'model' => 'gpt-4.1-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => "Corr:$correct Ans:$student Same? YES/NO"
                    ]
                ],
                'temperature' => 1,
                'max_completion_tokens' => 10
        ]);

        $answer = trim($response->choices[0]->message->content);

        return $answer === 'YES';

    }

}