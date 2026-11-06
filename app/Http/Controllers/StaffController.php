<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStaffRequest;
use App\Http\Requests\UpdateStaffRequest;
use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * FR2: administrators create, suspend and assign roles to staff accounts.
 * All routes are protected by ->middleware('role:admin') in routes/web.php.
 */
class StaffController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $roleId = $request->query('role');
        $status = $request->query('status');

        $staff = User::with('role')
            ->when($search !== '', fn ($q) => $q->where(fn ($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")))
            ->when($roleId, fn ($q) => $q->where('role_id', $roleId))
            ->when(in_array($status, [User::STATUS_ACTIVE, User::STATUS_SUSPENDED], true),
                   fn ($q) => $q->where('status', $status))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('staff.index', [
            'staff'  => $staff,
            'roles'  => Role::orderBy('label')->get(),
            'search' => $search,
            'roleId' => $roleId,
            'status' => $status,
        ]);
    }

    public function create(): View
    {
        return view('staff.create', ['roles' => Role::orderBy('label')->get()]);
    }

    public function store(StoreStaffRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $user = new User(['name' => $data['name'], 'email' => $data['email'], 'password' => $data['password']]);
        $user->role_id = $data['role_id'];          // set explicitly (not mass-assignable)
        $user->status = User::STATUS_ACTIVE;
        $user->save();

        AuditLog::record('staff_created', $user);

        return redirect()->route('staff.index')->with('status', "Staff account for {$user->name} created.");
    }

    public function edit(User $user): View
    {
        return view('staff.edit', ['staffMember' => $user, 'roles' => Role::orderBy('label')->get()]);
    }

    public function update(UpdateStaffRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        // An admin cannot change their own role (prevents locking everyone out).
        if ($user->is($request->user()) && (int) $data['role_id'] !== $user->role_id) {
            return back()->withInput()->withErrors(['role_id' => 'You cannot change your own role.']);
        }

        $user->fill(['name' => $data['name'], 'email' => $data['email']]);
        $user->role_id = $data['role_id'];

        $passwordChanged = filled($data['password'] ?? null);
        if ($passwordChanged) {
            $user->password = $data['password'];
        }

        $user->save();

        AuditLog::record('staff_updated', $user);
        if ($passwordChanged) {
            AuditLog::record('staff_password_reset', $user);
        }

        return redirect()->route('staff.index')->with('status', "{$user->name} updated.");
    }

    public function toggleStatus(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->with('error', 'You cannot suspend your own account.');
        }

        $user->status = $user->isActive() ? User::STATUS_SUSPENDED : User::STATUS_ACTIVE;
        $user->save();

        AuditLog::record($user->isActive() ? 'staff_activated' : 'staff_suspended', $user);

        return back()->with('status', $user->isActive()
            ? "{$user->name} can sign in again."
            : "{$user->name} has been suspended and signed out.");
    }
}
