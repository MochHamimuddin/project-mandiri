@extends('fatigue-activities.create', [
    'type' => \App\Models\FatigueActivity::TYPE_SAGA,
    'title' => 'Inspeksi SAGA'
])

@section('additional-fields')
    <div class="row mb-3">
        <div class="col-md-12">
            <label for="employee_name" class="form-label">Nama Karyawan SAGA</label>
            <input type="text" class="form-control" id="employee_name" name="employee_name" required>
            <small class="text-muted">Nama karyawan yang diinspeksi</small>
        </div>
    </div>
@endsection
