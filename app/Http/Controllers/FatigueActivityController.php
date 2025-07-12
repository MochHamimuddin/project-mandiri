<?php

namespace App\Http\Controllers;

use App\Models\FatigueActivity;
use App\Models\User;
use App\Models\Mitra;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FatigueActivityController extends Controller
{
    // Dashboard Fatigue Preventive
    public function dashboard()
    {
        $counts = [];
        foreach (FatigueActivity::$typeLabels as $type => $label) {
            $counts[$type] = FatigueActivity::ofType($type)->active()->count();
        }

        return view('fatigue-activities.dashboard', [
            'activityTypes' => FatigueActivity::$typeLabels,
            'counts' => $counts
        ]);
    }

    public function index(Request $request)
{
    $query = FatigueActivity::with(['user', 'supervisor', 'mitra', 'shift'])
                ->active()
                ->latest();

    if (auth()->user()->code_role == '002') {
        $query->where('user_id', auth()->id());
    }

    if ($request->has('type') && in_array($request->type, [
        FatigueActivity::TYPE_FTW,
        FatigueActivity::TYPE_DFIT,
        FatigueActivity::TYPE_FATIGUE_CHECK,
        FatigueActivity::TYPE_WAKEUP_CALL,
        FatigueActivity::TYPE_SAGA,
        FatigueActivity::TYPE_SIDAK
    ])) {
        $query->where('activity_type', $request->type);
    }

    return view('fatigue-activities.index', [
        'activities' => $query->paginate(10),
        'activityTypes' => \App\Models\FatigueActivity::$typeLabels,
        'typeFilter' => $request->type ?? null
    ]);
}

    // Form Create Generik
   public function create($type)
{
    if (!in_array($type, FatigueActivity::$validActivityTypes)) {
        abort(404, 'Jenis aktivitas tidak valid');
    }

    // Gunakan view khusus jika ada, fallback ke view generik
    $viewName = "fatigue-activities.create-".str_replace('_', '-', $type);
    $view = view()->exists($viewName) ? $viewName : 'fatigue-activities.create';

    return view($view, [
        'type' => $type,
        'title' => FatigueActivity::$typeLabels[$type] ?? ucfirst(str_replace('_', ' ', $type)),
        'supervisors' => User::where('code_role', '001')->get(),
        'users' => User::where('code_role', '002')->get(),
        'mitras' => Mitra::active()->get(), // This will now work
        'shifts' => Shift::all()
    ]);
}

    // Simpan Aktivitas
public function store(Request $request)
{
    \Log::info('Store method called', $request->all());

    $validator = $this->validateActivity($request);

    if ($validator->fails()) {
        \Log::error('Validation failed', [
            'errors' => $validator->errors()->toArray(),
            'input' => $request->all()
        ]);
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    try {
        \DB::beginTransaction();

        $activityData = $this->prepareActivityData($request);
        \Log::debug('Prepared activity data:', $activityData);

        $activity = FatigueActivity::create($activityData);
        \Log::info('Activity created successfully', ['id' => $activity->id]);

        \DB::commit();

        return redirect()->route('fatigue-activities.show', $activity->id)
            ->with('success', 'Aktivitas berhasil dicatat');

    } catch (\Exception $e) {
        \DB::rollBack();
        \Log::error('Activity creation failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return redirect()->back()
            ->with('error', 'Gagal menyimpan data: ' . $e->getMessage())
            ->withInput();
    }
}
    // Detail Aktivitas
    public function show($id)
    {
        $activity = FatigueActivity::with(['user', 'supervisor', 'mitra', 'shift'])
                      ->findOrFail($id);

        return view('fatigue-activities.show', [
            'activity' => $activity,
            'typeLabels' => FatigueActivity::$typeLabels
        ]);
    }

    // Form Edit
    public function edit($id)
    {
        $activity = FatigueActivity::findOrFail($id);

        return view('fatigue-activities.edit', [
            'activity' => $activity,
            'supervisors' => User::where('code_role', '001')->get(),
            'users' => User::where('code_role', '002')->get(),
            'mitras' => Mitra::active()->get(),
            'shifts' => Shift::all(),
            'typeLabels' => FatigueActivity::$typeLabels
        ]);
    }

    // Update Aktivitas
    public function update(Request $request, $id)
    {
        $activity = FatigueActivity::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:data_users,id',
            'supervisor_id' => 'required|exists:data_users,id',
            'description' => 'required|string|max:500',
        ]);

        if ($activity->activity_type === FatigueActivity::TYPE_SIDAK) {
            $validator->addRules(['location' => 'required|string|max:255']);
        }

        if ($activity->activity_type === FatigueActivity::TYPE_SAGA) {
            $validator->addRules(['mitra_id' => 'required|string|max:255']);
        }

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $activity->update([
            'user_id' => $request->user_id,
            'supervisor_id' => $request->supervisor_id,
            'shift_id' => $request->shift_id,
            'mitra_id' => $request->mitra_id,
            'description' => $request->description,
            'location' => $request->location,
            'mitra_id' => $request->mitra_id,
            'updated_by' => auth()->id()
        ]);

        return redirect()->route('fatigue-activities.show', $activity->id)
            ->with('success', 'Aktivitas berhasil diperbarui');
    }

    // Hapus Aktivitas
    public function destroy($id)
    {
        $activity = FatigueActivity::findOrFail($id);
        $activity->update(['deleted_by' => auth()->id()]);
        $activity->delete();

        return redirect()->route('fatigue-activities.index')
            ->with('success', 'Aktivitas berhasil dihapus');
    }

    // Method khusus untuk setiap jenis aktivitas
    public function createFtw()
    {
        return $this->create(FatigueActivity::TYPE_FTW);
    }

    public function createDfit()
    {
        return $this->create(FatigueActivity::TYPE_DFIT);
    }

    public function createFatigueCheck()
    {
        return $this->create(FatigueActivity::TYPE_FATIGUE_CHECK);
    }

    public function createWakeupCall()
    {
        return $this->create(FatigueActivity::TYPE_WAKEUP_CALL);
    }

    public function createSaga()
    {
        return $this->create(FatigueActivity::TYPE_SAGA);
    }

    public function createSidak()
    {
        return $this->create(FatigueActivity::TYPE_SIDAK);
    }

    // Helper methods
    protected function validateActivity(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'activity_type' => 'required|in:' . implode(',', FatigueActivity::$validActivityTypes),
            'user_id' => 'required|exists:data_users,id',
            'supervisor_id' => 'required|exists:data_users,id',
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'description' => 'required|string|max:500',
        ]);

        switch ($request->activity_type) {
            case FatigueActivity::TYPE_SIDAK:
                $validator->addRules(['location' => 'required|string|max:255']);
                break;

            case FatigueActivity::TYPE_SAGA:
                $validator->addRules(['mitra_id' => 'required|string|max:255']);
                break;

            default:
                if (in_array($request->activity_type, [
                    FatigueActivity::TYPE_FTW,
                    FatigueActivity::TYPE_DFIT,
                    FatigueActivity::TYPE_FATIGUE_CHECK,
                    FatigueActivity::TYPE_WAKEUP_CALL
                ])) {
                    $validator->addRules(['result_file' => 'required|file|mimes:pdf,jpeg,png,jpg|max:2048']);
                }
        }

        return $validator;
    }
    protected function prepareActivityData(Request $request)
{
    // Pastikan folder tujuan ada
    $photoDir = 'fatigue_activities/photos';
    Storage::disk('public')->makeDirectory($photoDir);

    $resultDir = 'fatigue_activities/results';
    Storage::disk('public')->makeDirectory($resultDir);

    // Simpan file
    $photoPath = $request->file('photo')->store($photoDir, 'public');

    $resultPath = null;
    if ($request->hasFile('result_file')) {
        $resultPath = $request->file('result_file')->store($resultDir, 'public');
    }

    return [
        'activity_type' => $request->activity_type,
        'user_id' => $request->user_id,
        'supervisor_id' => $request->supervisor_id,
        'shift_id' => $request->shift_id,
        'mitra_id' => $request->mitra_id,
        'photo_path' => $photoPath,
        'result_path' => $resultPath,
        'description' => $request->description,
        'location' => $request->location,
        'created_by' => auth()->id()
    ];
}
public function toggleApproval($id)
{
    try {
        // Validasi hanya supervisor yang bisa akses
        if (auth()->user()->code_role != '001') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Only supervisors can update status'
            ], 403);
        }

        $activity = FatigueActivity::findOrFail($id);
        $newStatus = $activity->is_approved ? 0 : 1;
        $activity->is_approved = $newStatus;
        $activity->save();

        return response()->json([
            'success' => true,
            'new_status' => $newStatus,
            'status_text' => $newStatus ? 'Closed' : 'Open'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}
}
