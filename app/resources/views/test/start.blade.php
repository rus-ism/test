<!DOCTYPE html>
<html>
<head>

<title>Начало тестирования</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container mt-5">

<h2 class="mb-4">Начало тестирования</h2>

<form method="POST" action="/test/start">

    @csrf

    <div class="mb-3">
        <label class="form-label">Язык</label>
        <select name="lang" class="form-select" required>

        @foreach($langs as $lang)
        <option value="{{ $lang->lang }}">{{ $lang->lang }}</option>
        @endforeach

        </select>
    </div>


    <div class="mb-3">
    <label class="form-label">Район</label>

    <select id="district" name="district_id" class="form-select" required>

    <option value="">Выберите район</option>

    @foreach($districts as $district)

    <option value="{{ $district->id }}">
    {{ $district->name_ru }}
    </option>

    @endforeach

    </select>

    </div>


    <div class="mb-3">

    <label class="form-label">Школа</label>

    <select id="school" name="school_id" class="form-select" required>

    <option value="">Сначала выберите район</option>

    </select>

    </div>



    <div class="mb-3">
    <label class="form-label">Класс</label>
    <select name="grade" class="form-select" required>

    @foreach($grades as $grade)
    <option value="{{ $grade->grade }}">{{ $grade->grade }}</option>
    @endforeach

    </select>
    </div>

    <div class="mb-3">
    <label class="form-label">Предмет</label>
    <select name="subject" class="form-select" required>

    @foreach($subjects as $subject)
    <option value="{{ $subject->subject }}">{{ $subject->subject }}</option>
    @endforeach

    </select>
    </div>

    <div class="mb-3">
    <label class="form-label">Вариант</label>
    <select name="variant" class="form-select" required>

    
    <option disabled selected value="{{ $variants}}">{{ $variants }}</option>
  

    </select>
    </div>

    <div class="mb-3">
    <label class="form-label">ФИО</label>
    <input type="text" name="student_name" class="form-control" required>
    </div>

    <button class="btn btn-primary">
    Начать тест
    </button>

</form>

</div>



<script>

document.getElementById('district').addEventListener('change', function(){

    let districtId = this.value;

    fetch('/api/schools/' + districtId)

    .then(response => response.json())

    .then(data => {

        let schoolSelect = document.getElementById('school');

        schoolSelect.innerHTML = '';

        data.forEach(function(school){

            let option = document.createElement('option');

            option.value = school.id;
            option.text = school.name_ru;

            schoolSelect.appendChild(option);

        });

    });

});

</script>
</body>
</html>