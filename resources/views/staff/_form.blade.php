{{-- Shared by create and edit. $staffMember is null on create. --}}
<div class="row g-3">
    <div class="col-md-6">
        <label for="name" class="form-label">Full name</label>
        <input id="name" name="name" value="{{ old('name', $staffMember?->name) }}"
               class="form-control @error('name') is-invalid @enderror" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label for="email" class="form-label">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email', $staffMember?->email) }}"
               class="form-control @error('email') is-invalid @enderror" required autocomplete="off">
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label for="role_id" class="form-label">Role</label>
        <select id="role_id" name="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
            <option value="">Select role…</option>
            @foreach ($roles as $role)
                <option value="{{ $role->id }}" @selected((string) old('role_id', $staffMember?->role_id) === (string) $role->id)>
                    {{ $role->label }}
                </option>
            @endforeach
        </select>
        @error('role_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6"></div>
    <div class="col-md-6">
        <label for="password" class="form-label">
            Password @if ($staffMember)<span class="text-muted small">(leave blank to keep current)</span>@endif
        </label>
        <input id="password" type="password" name="password"
               class="form-control @error('password') is-invalid @enderror"
               @unless ($staffMember) required @endunless autocomplete="new-password">
        <div class="form-text">At least 10 characters with letters and numbers.</div>
        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label for="password_confirmation" class="form-label">Confirm password</label>
        <input id="password_confirmation" type="password" name="password_confirmation" class="form-control"
               @unless ($staffMember) required @endunless autocomplete="new-password">
    </div>
</div>
