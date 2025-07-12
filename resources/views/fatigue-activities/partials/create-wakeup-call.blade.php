@extends('fatigue-activities.create', [
    'type' => \App\Models\FatigueActivity::TYPE_WAKEUP_CALL,
    'title' => 'Wake Up Call'
])

@section('additional-fields')
    <div class="row mb-3">
        <div class="col-md-12">
            <label for="result_file" class="form-label">Wake Up Call Form (PDF/Image)</label>
            <input type="file" class="form-control" id="result_file" name="result_file" accept=".pdf,.jpeg,.jpg,.png" required>
            <small class="text-muted">Upload form wake up call</small>
        </div>
    </div>
@endsection
