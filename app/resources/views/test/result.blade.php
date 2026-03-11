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

$letter = trim(substr(trim($opt),0,1));

$text = trim(substr(trim($opt),2));

@endphp

@if($letter == $student)

<div class="text-primary">

{{ $text }}

</div>

@endif

@endforeach


<p class="mt-2"><b>Правильный ответ:</b></p>

@foreach($options as $opt)

@php

$letter = trim(substr(trim($opt),0,1));

$text = trim(substr(trim($opt),2));

@endphp

@if($letter == $correct)

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

$letter = trim(substr(trim($opt),0,1));

$text = trim(substr(trim($opt),2));

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

$letter = trim(substr(trim($opt),0,1));

$text = trim(substr(trim($opt),2));

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

$options = $q->options;

$parts = preg_split('/Col:/',$options);

$rowPart = trim(str_replace('Row:','',$parts[0]));
$colPart = trim($parts[1]);

$rows = explode(',',$rowPart);
$cols = explode(',',$colPart);

$student = json_decode($studentAnswer,true) ?? [];

$correctPairs = explode(',',$q->correct_answer);

$correctMap = [];

foreach($correctPairs as $pair){

list($r,$c) = explode('-',trim($pair));

$correctMap[$r] = $c;

}

@endphp


<table class="table table-bordered">

<thead>

<tr>

<th>Элемент</th>

<th>Ваш ответ</th>

<th>Правильный</th>

</tr>

</thead>

<tbody>

@foreach($rows as $row)

@php

$row = trim($row);

$num = substr($row,0,1);

$text = trim(substr($row,2));

$studentLetter = $student[$num] ?? '';

$correctLetter = $correctMap[$num] ?? '';

$studentText = '';
$correctText = '';

foreach($cols as $col){

$col = trim($col);

$letter = substr($col,0,1);

$colText = trim(substr($col,2));

if($letter == $studentLetter) $studentText = $colText;
if($letter == $correctLetter) $correctText = $colText;

}

@endphp

<tr>

<td>{{ $text }}</td>

<td class="text-primary">{{ $studentText }}</td>

<td class="text-success">{{ $correctText }}</td>

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