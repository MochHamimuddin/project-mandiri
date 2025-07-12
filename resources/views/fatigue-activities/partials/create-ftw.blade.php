@extends('fatigue-activities.create', [
    'type' => \App\Models\FatigueActivity::TYPE_FTW,
    'title' => 'First Time Work (FTW)'
])

@section('additional-fields')
    <div class="row mb-3">
        <div class="col-md-12">
            <label for="result_file" class="form-label">FTW Result File (PDF/Image)</label>
            <input type="file" class="form-control" id="result_file" name="result_file" accept=".pdf,.jpeg,.jpg,.png" required>
            <small class="text-muted">Upload hasil pemeriksaan FTW</small>
        </div>
    </div>
@endsection
