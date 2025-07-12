@extends('fatigue-activities.create', [
    'type' => \App\Models\FatigueActivity::TYPE_FATIGUE_CHECK,
    'title' => 'Fatigue Check'
])

@section('additional-fields')
    <div class="row mb-3">
        <div class="col-md-12">
            <label for="result_file" class="form-label">Fatigue Check Result (PDF/Image)</label>
            <input type="file" class="form-control" id="result_file" name="result_file" accept=".pdf,.jpeg,.jpg,.png" required>
            <small class="text-muted">Upload hasil fatigue check</small>
        </div>
    </div>
@endsection
