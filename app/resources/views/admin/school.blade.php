<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title>Школа</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">

<h2>{{ $school->name_ru }}</h2>

<table class="table table-bordered mt-3">

<thead class="table-dark">
<tr>
    <th>Класс</th>
    <th>Кол-во</th>
    <th>Средний балл</th>
</tr>
</thead>

<tbody>

@foreach($classes as $class)
@php
    $safeGrade = Str::slug($class->grade);
@endphp
<tr data-bs-toggle="collapse"
    data-bs-target="#class-{{ $safeGrade }}"
    style="cursor:pointer">

    <td><span class="arrow">▶</span> {{ $class->grade }}</td>
    <td>{{ $class->total }}</td>
    <td>{{ round($class->avg_score,2) }}</td>

</tr>

<tr class="collapse" id="class-{{ $safeGrade }}">
<td colspan="3">

    <table class="table table-sm table-bordered">

        <thead class="table-light">
        <tr>
            <th>Предмет</th>
            <th>Кол-во</th>
            <th>Средний балл</th>
        </tr>
        </thead>

        <tbody>

        @foreach($subjects[$class->grade] ?? [] as $subject)

        <tr>

            <td>
                <a href="/admin/school/{{ $school->id }}/class/{{ $class->grade }}/subject/{{ urlencode($subject->subject) }}">
                    {{ $subject->subject }}
                </a>
            </td>

            <td>{{ $subject->total }}</td>

            <td>{{ round($subject->avg_score,2) }}</td>

        </tr>

        @endforeach

        </tbody>

    </table>

</td>
</tr>

@endforeach

</tbody>

</table>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(row => {
    row.addEventListener('click', function(){
        let arrow = this.querySelector('.arrow');
        if(arrow){
            arrow.innerHTML = arrow.innerHTML === '▶' ? '▼' : '▶';
        }
    });
});
</script>

</body>
</html>