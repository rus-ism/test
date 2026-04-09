<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Статистика по районам</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">

    <h2 class="mb-4">Статистика по районам</h2>

    <table class="table table-bordered">

        <thead class="table-dark">
        <tr>
            <th>Район</th>
            <th>Кол-во тестов</th>
            <th>Средний балл</th>
        </tr>
        </thead>

        <tbody>
                @php 
                    //dd($GeneralStat);
                @endphp
            <tr>
                <td><b>По области</b></td>
                <td>{{ $GeneralStat[0]->total ?? 0}}</td>
                <td>{{ round($GeneralStat[0]->avg_score, 2) ?? 0}}</td>
            </tr>
        @foreach($districts as $district)

            @php
                $stat = $districtStats[$district->id] ?? null;
            @endphp

            <!-- РАЙОН -->
            <tr data-bs-toggle="collapse"
                data-bs-target="#schools-{{ $district->id }}"
                style="cursor:pointer">
                @php 
                   // dd($district);
                @endphp
                <td>
                    <span class="arrow">▶</span>
                    {{ $district->name_ru }}
                </td>

                <td>{{ $stat->total ?? 0 }}</td>

                <td>
                    {{ isset($stat->avg_score) ? round($stat->avg_score,2) : 0 }}
                </td>

            </tr>

            <!-- ШКОЛЫ -->
            <tr class="collapse" id="schools-{{ $district->id }}">
                <td colspan="3">

                    <table class="table table-sm table-bordered mb-0">

                        <thead class="table-light">
                        <tr>
                            <th>Школа</th>
                            <th>Кол-во</th>
                            <th>Средний балл</th>
                        </tr>
                        </thead>

                        <tbody>

                        @foreach($district->schools as $school)

                            @php
                                $s = $schoolStats[$school->id] ?? null;
                            @endphp

                            <tr>

                                <td>
                                    <a href="/admin/school/{{ $school->id }}">
                                        {{ $school->name_ru }}
                                    </a>
                                </td>

                                <td>{{ $s->total ?? 0 }}</td>

                                <td>
                                    {{ isset($s->avg_score) ? round($s->avg_score,2) : 0 }}
                                </td>

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

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- стрелка -->
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