<?php

namespace App\Http\Controllers;

use App\Http\Requests\PatientRequest;
use App\Models\AuditLog;
use App\Models\Patient;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PatientController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Patient::class);

        $search = trim((string) $request->query('search'));

        $patients = Patient::visibleTo($request->user())
            ->when($search !== '', fn ($q) => $q->where(fn ($q) => $q
                ->where('patient_number', 'like', "%{$search}%")
                ->orWhere('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")))
            ->orderBy('last_name')->orderBy('first_name')
            ->paginate(15)
            ->withQueryString();

        return view('patients.index', compact('patients', 'search'));
    }

    public function create(): View
    {
        Gate::authorize('create', Patient::class);

        return view('patients.create');
    }

    public function store(PatientRequest $request): RedirectResponse
    {
        Gate::authorize('create', Patient::class);

        $patient = new Patient($request->validated());
        $patient->registered_by = $request->user()->id;
        $patient->save();

        // A doctor or nurse who registers a patient is put on that patient's care team,
        // otherwise they could not open the record they just created.
        if ($request->user()->hasRole(Role::DOCTOR, Role::NURSE)) {
            $patient->assignedStaff()->attach($request->user()->id);
        }

        AuditLog::record('created', $patient);

        return redirect()->route('patients.show', $patient)
            ->with('status', "Patient {$patient->patient_number} registered.");
    }

    public function show(Patient $patient): View
    {
        Gate::authorize('view', $patient);

        AuditLog::record('viewed', $patient);   // FR7: who opened which record

        $patient->load('assignedStaff.role', 'registeredBy');

        // Doctors and nurses who are active and not yet on the care team (admin's assign form).
        $assignable = Gate::allows('assignStaff', $patient)
            ? User::whereHas('role', fn ($q) => $q->whereIn('name', [Role::DOCTOR, Role::NURSE]))
                ->where('status', User::STATUS_ACTIVE)
                ->whereNotIn('id', $patient->assignedStaff->pluck('id'))
                ->with('role')->orderBy('name')->get()
            : collect();

        return view('patients.show', compact('patient', 'assignable'));
    }

    public function edit(Patient $patient): View
    {
        Gate::authorize('update', $patient);

        return view('patients.edit', compact('patient'));
    }

    public function update(PatientRequest $request, Patient $patient): RedirectResponse
    {
        Gate::authorize('update', $patient);

        $patient->update($request->validated());

        AuditLog::record('updated', $patient);

        return redirect()->route('patients.show', $patient)->with('status', 'Patient details updated.');
    }

    /** Admin adds a doctor or nurse to the patient's care team. */
    public function assign(Request $request, Patient $patient): RedirectResponse
    {
        Gate::authorize('assignStaff', $patient);

        $data = $request->validate(['user_id' => ['required', 'integer', 'exists:users,id']]);
        $staff = User::with('role')->findOrFail($data['user_id']);

        if (! $staff->hasRole(Role::DOCTOR, Role::NURSE) || ! $staff->isActive()) {
            return back()->with('error', 'Only active doctors and nurses can be assigned to patients.');
        }

        $patient->assignedStaff()->syncWithoutDetaching([$staff->id]);
        AuditLog::record('staff_assigned', $patient);

        return back()->with('status', "{$staff->name} assigned to this patient.");
    }

    /** Admin removes a doctor or nurse from the care team. */
    public function unassign(Patient $patient, User $user): RedirectResponse
    {
        Gate::authorize('assignStaff', $patient);

        $patient->assignedStaff()->detach($user->id);
        AuditLog::record('staff_unassigned', $patient);

        return back()->with('status', "{$user->name} removed from this patient.");
    }
}
