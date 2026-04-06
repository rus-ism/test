<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">

<title>Результат теста</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container mt-5">

<h2 class="mb-4">Результат тестирования</h2>

<div class="alert alert-success mb-4">

<h4>Ваш результат: {{ $attempt->score }} баллов</h4>

</div>


@foreach($answers as $answer)

@php

$q = $answer->question;

$studentAnswer = $answer->student_answer;

@endphp

<div class="card mb-3">

<div class="card-body">

<h5>{{ $q->num }}. {{ $q->question }}</h5>


{{-- SINGLE --}}

@if($q->type == 'Single')

    @php

        $options = explode(';',$q->options);

        $correct = trim($q->correct_answer);

        $student = trim($studentAnswer);

    @endphp

    <p><b>Ваш ответ:</b></p>

@foreach($options as $opt)
    @php
        // Извлекаем букву и текст
        $letter = trim(mb_substr(trim($opt), 0, 1));
        $text = trim(mb_substr(trim($opt), 2));

        // Функция нормализации (заменяем латиницу на кириллицу для сравнения)
        $normalize = function($char) {
            $latin = ['A', 'B', 'C', 'E', 'H', 'K', 'M', 'O', 'P', 'T', 'X', 'Y', 'a', 'p', 'o', 'x', 'c', 'e'];
            $cyrillic = ['А', 'В', 'С', 'Е', 'Н', 'К', 'М', 'О', 'Р', 'Т', 'Х', 'У', 'а', 'р', 'о', 'х', 'с', 'е'];
            return str_replace($latin, $cyrillic, $char);
        };
    @endphp

    @if($normalize($letter) == $normalize($student))
        <div class="text-primary">
            {{ $text }}
        </div>
    @endif
@endforeach


    <p class="mt-2"><b>Правильный ответ:</b></p>
    @php
        //dd($options);
    @endphp

    @foreach($options as $opt)

        @php
            // Извлекаем букву и текст
            $letter = trim(mb_substr(trim($opt), 0, 1));
            $text = trim(mb_substr(trim($opt), 2));

            // Функция нормализации (заменяем латиницу на кириллицу для сравнения)
            $normalize = function($char) {
                $latin = ['A', 'B', 'C', 'E', 'H', 'K', 'M', 'O', 'P', 'T', 'X', 'Y', 'a', 'p', 'o', 'x', 'c', 'e'];
                $cyrillic = ['А', 'В', 'С', 'Е', 'Н', 'К', 'М', 'О', 'Р', 'Т', 'Х', 'У', 'а', 'р', 'о', 'х', 'с', 'е'];
                return str_replace($latin, $cyrillic, $char);
            };
        @endphp

        @if($normalize($letter) == $normalize($correct))

            <div class="text-success">

            {{ $text }}

            </div>

        @endif

    @endforeach

@endif



{{-- MULTIPLE --}}

@if($q->type == 'Multiple')

@php

$options = explode(';',$q->options);

$correct = array_map('trim', explode(';',$q->correct_answer));

$student = json_decode($studentAnswer,true) ?? [];

@endphp


<p><b>Ваш ответ:</b></p>

@foreach($options as $opt)

@php

$letter = trim(mb_substr(trim($opt),0,1));

$text = trim(mb_substr(trim($opt),2));

@endphp

@if(in_array($letter,$student))

<div class="text-primary">

{{ $text }}

</div>

@endif

@endforeach


<p class="mt-2"><b>Правильные ответы:</b></p>

@foreach($options as $opt)

@php

$letter = trim(mb_substr(trim($opt),0,1));

$text = trim(mb_substr(trim($opt),2));

@endphp

@if(in_array($letter,$correct))

<div class="text-success">

{{ $text }}

</div>

@endif

@endforeach

@endif



{{-- SHORT ANSWER --}}

@if($q->type == 'Short Answer')

<p>

<b>Ваш ответ:</b>

<span class="text-primary">

{{ $studentAnswer }}

</span>

</p>

<p>

<b>Правильный ответ:</b>

<span class="text-success">

{{ $q->correct_answer }}

</span>

</p>

@endif



{{-- MATCHING --}}

@if($q->type == 'Matching')
@php
    // 1. Функция нормализации (обрабатывает и латиницу, и возможные пробелы/символы)
    $normalize = function($char) {
        $char = trim(mb_strtoupper($char)); // К верхнему регистру для верности
        $latin = ['A', 'B', 'C', 'E', 'H', 'K', 'M', 'O', 'P', 'T', 'X', 'Y'];
        $cyrillic = ['А', 'В', 'С', 'Е', 'Н', 'К', 'М', 'О', 'Р', 'Т', 'Х', 'У'];
        return str_replace($latin, $cyrillic, $char);
    };

    $options = $q->options;
    // Разбиваем на Row и Col
    $parts = preg_split('/\/?\s*Col:/', $options); 
    $rowPart = trim(str_replace('Row:', '', $parts[0]));
    $colPart = trim($parts[1] ?? '');

    // Создаем массивы, очищая от лишних пробелов
    $rows = array_filter(explode(',', $rowPart));
    $cols = array_filter(explode(',', $colPart));

    // Ответы студента: нормализуем КЛЮЧИ (1, 2, 3) и ЗНАЧЕНИЯ (A, B, C)
    $rawStudent = json_decode($studentAnswer, true) ?? [];
    $student = [];
    foreach($rawStudent as $k => $v) {
        $student[$normalize($k)] = $normalize($v);
    }

    // Правильные ответы: нормализуем КЛЮЧИ и ЗНАЧЕНИЯ
    $correctPairs = explode(',', $q->correct_answer);
    $correctMap = [];
    foreach($correctPairs as $pair) {
        $pairParts = explode('-', trim($pair));
        if(count($pairParts) == 2) {
            $correctMap[$normalize($pairParts[0])] = $normalize($pairParts[1]);
        }
    }
@endphp

<table class="table table-bordered">
    <thead>
        <tr class="bg-light">
            <th>Элемент</th>
            <th>Ваш ответ</th>
            <th>Правильный</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $row)
            @php
                $row = trim($row);
                // Извлекаем "1" из "1.Fe3+"
                $rowNumRaw = mb_substr($row, 0, mb_strpos($row, '.')); 
                if(!$rowNumRaw) $rowNumRaw = mb_substr($row, 0, 1); // fallback если нет точки
                
                $rowNum = $normalize($rowNumRaw);
                $rowText = trim(mb_substr($row, mb_strpos($row, '.') + 1));

                $studentLetter = $student[$rowNum] ?? '';
                $correctLetter = $correctMap[$rowNum] ?? '';

                $studentText = '—';
                $correctText = '—';

                // Ищем расшифровку текста для букв A, B, C...
                foreach($cols as $col) {
                    $col = trim($col);
                    $colLetterRaw = mb_substr($col, 0, mb_strpos($col, '.'));
                    if(!$colLetterRaw) $colLetterRaw = mb_substr($col, 0, 1);
                    
                    $currentColLetter = $normalize($colLetterRaw);
                    $colContent = trim(mb_substr($col, mb_strpos($col, '.') + 1));

                    if($currentColLetter === $studentLetter && $studentLetter !== '') {
                        $studentText = $colContent;
                    }
                    if($currentColLetter === $correctLetter && $correctLetter !== '') {
                        $correctText = $colContent;
                    }
                }
            @endphp
            <tr>
                <td>{{ $rowText }}</td>
                <td class="{{ $studentLetter === $correctLetter ? 'text-success' : 'text-danger' }}">
                    <strong>{{ $studentLetter }}</strong> {{ $studentText }}
                </td>
                <td class="text-success">
                    <strong>{{ $correctLetter }}</strong> {{ $correctText }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@endif

<p class="mt-2">

<b>Баллы:</b>

{{ $answer->points_awarded }}

</p>


</div>

</div>

@endforeach


<a href="/test/start" class="btn btn-primary mt-3">

Пройти тест снова

</a>

</div>

</body>
</html>