@extends('fatigue-activities.create', [
    'type' => \App\Models\FatigueActivity::TYPE_DFIT,
    'title' => 'Evaluasi D-Fit'
])

@section('additional-fields')
    <div class="row mb-3">
        <div class="col-md-12">
            <label for="result_file" class="form-label">D-Fit Result File (PDF/Image)</label>
            <input type="file" class="form-control" id="result_file" name="result_file" accept=".pdf,.jpeg,.jpg,.png" required>
            <small class="text-muted">Upload hasil evaluasi D-Fit</small>
        </div>
    </div>
@endsection
