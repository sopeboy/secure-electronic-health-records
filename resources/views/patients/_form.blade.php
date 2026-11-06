{{-- Shared by create and edit. $patient is null on create. --}}
<div class="row g-3">
    <div class="col-md-6">
        <label for="first_name" class="form-label">First name</label>
        <input id="first_name" name="first_name" value="{{ old('first_name', $patient?->first_name) }}"
               class="form-control @error('first_name') is-invalid @enderror" required maxlength="100">
        @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label for="last_name" class="form-label">Last name</label>
        <input id="last_name" name="last_name" value="{{ old('last_name', $patient?->last_name) }}"
               class="form-control @error('last_name') is-invalid @enderror" required maxlength="100">
        @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label for="gender" class="form-label">Gender</label>
        <select id="gender" name="gender" class="form-select @error('gender') is-invalid @enderror" required>
            <option value="">Select…</option>
            <option value="male" @selected(old('gender', $patient?->gender) === 'male')>Male</option>
            <option value="female" @selected(old('gender', $patient?->gender) === 'female')>Female</option>
        </select>
        @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label for="date_of_birth" class="form-label">Date of birth</label>
        <input id="date_of_birth" type="date" name="date_of_birth" max="{{ now()->toDateString() }}"
               value="{{ old('date_of_birth', $patient?->date_of_birth?->toDateString()) }}"
               class="form-control @error('date_of_birth') is-invalid @enderror" required>
        @error('date_of_birth')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label for="phone" class="form-label">Phone <span class="text-muted small">(optional)</span></label>
        <input id="phone" name="phone" value="{{ old('phone', $patient?->phone) }}"
               class="form-control @error('phone') is-invalid @enderror" maxlength="20">
        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <label for="address" class="form-label">Address <span class="text-muted small">(optional)</span></label>
        <input id="address" name="address" value="{{ old('address', $patient?->address) }}"
               class="form-control @error('address') is-invalid @enderror" maxlength="255">
        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>
