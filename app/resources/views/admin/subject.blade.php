<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title>Статистика</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">

<h3>{{ $subject }} | {{ $grade }} класс</h3>

<table class="table table-bordered mt-3">

<thead class="table-dark">
<tr>
    <th>ФИО</th>
    <th>Язык</th>
    <th>Вариант</th>
    <th>Балл</th>
</tr>
</thead>

<tbody>

@foreach($attempts as $a)

<tr>
    <td><a href="http://127.0.0.1:8000/test/result/{{$a->id}}">{{ $a->student_name }}</td>
    <td>{{ $a->lang }}</td>
    <td>{{ $a->variant }}</td>
    <td>{{ $a->score }}</td>
</tr>

@endforeach

</tbody>

</table>

</div>

</body>
</html>