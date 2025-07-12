@extends('fatigue-activities.create', [
    'type' => \App\Models\FatigueActivity::TYPE_SIDAK,
    'title' => 'Sidak Napping'
])

@section('additional-fields')
    <div class="row mb-3">
        <div class="col-md-12">
            <label for="location" class="form-label">Lokasi Sidak</label>
            <input type="text" class="form-control" id="location" name="location" required>
            <small class="text-muted">Lokasi dimana sidak dilakukan</small>
        </div>
    </div>
@endsection
