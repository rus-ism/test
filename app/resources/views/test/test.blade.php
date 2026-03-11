<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">

<title>Тест</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container mt-5">

<h3 class="mb-4">Тестирование</h3>

<form method="POST" action="/test/{{ $attempt->id }}/submit">

@csrf

@foreach($questions as $q)

<div class="card mb-3">

<div class="card-body">

@if($q->context)
<div class="text-muted mb-2">
{!! $q->context !!}
</div>
@endif

<h5 class="mb-3">
{{ $q->num }}. {{ $q->question }}
</h5>

{{-- SINGLE --}}
@if($q->type == 'Single')

@php
$options = explode(';', $q->options);
@endphp

@foreach($options as $opt)

@php
$value = trim(substr(trim($opt),0,1));
@endphp

<div class="form-check">

<input
class="form-check-input"
type="radio"
name="answers[{{ $q->id }}]"
value="{{ $value }}"
>

<label class="form-check-label">
{{ trim($opt) }}
</label>

</div>

@endforeach

@endif



{{-- MULTIPLE --}}
@if($q->type == 'Multiple')

@php
$options = explode(';', $q->options);
@endphp

@foreach($options as $opt)

@php
$value = trim(substr(trim($opt),0,1));
@endphp

<div class="form-check">

<input
class="form-check-input"
type="checkbox"
name="answers[{{ $q->id }}][]"
value="{{ $value }}"
>

<label class="form-check-label">
{{ trim($opt) }}
</label>

</div>

@endforeach

@endif



{{-- SHORT ANSWER --}}
@if($q->type == 'Short Answer')

<input
type="text"
class="form-control"
name="answers[{{ $q->id }}]"
>

@endif



{{-- MATCHING --}}
@if($q->type == 'Matching')

@php

$options = $q->options;

/* разделяем Row и Col */

$parts = preg_split('/Col:/', $options);

$rowPart = trim(str_replace('Row:','',$parts[0]));
$colPart = trim($parts[1]);

/* строки */

$rows = explode(',', $rowPart);

/* варианты */

$cols = explode(',', $colPart);

@endphp

<table class="table table-bordered">

<thead>
<tr>
<th>№</th>
<th>Элемент</th>
<th>Соответствие</th>
</tr>
</thead>

<tbody>

@foreach($rows as $row)

@php

$row = trim($row);

/* номер строки */

$number = substr($row,0,1);

/* текст строки */

$text = trim(substr($row,2));

@endphp

<tr>

<td>{{ $number }}</td>

<td>{{ $text }}</td>

<td>

<select
class="form-select"
name="answers[{{ $q->id }}][{{ $number }}]"
>

<option value="">--</option>

@foreach($cols as $col)

@php

$col = trim($col);

/* буква */

$letter = substr($col,0,1);

/* текст */

$colText = trim(substr($col,2));

@endphp

<option value="{{ $letter }}">

{{ $letter }}. {{ $colText }}

</option>

@endforeach

</select>

</td>

</tr>

@endforeach

</tbody>

</table>

@endif





</div>
</div>

@endforeach


<button class="btn btn-success mt-3">
Завершить тест
</button>

</form>

</div>

</body>
</html>