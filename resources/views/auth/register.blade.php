<x-guest-layout>
    <div class="form-header">
        <div class="form-badge">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            New Registration
        </div>
        <h2 class="form-title">Create Account</h2>
        <p class="form-subtitle">Register to start managing child growth and health records.</p>
    </div>

    <x-auth-session-status class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 p-3 text-sm text-emerald-700" :status="session('status')" />

    <!-- Role Selection Tabs -->
    <div class="role-tabs">
        <button type="button" data-role="parent" class="role-tab active" onclick="switchTab(this, 'parent')">
            <span class="emoji">👨‍👩‍👧</span>
            Parent
        </button>
        <button type="button" data-role="nurse" class="role-tab" onclick="switchTab(this, 'nurse')">
            <span class="emoji">👩‍⚕️</span>
            Nurse
        </button>
        <button type="button" data-role="doctor" class="role-tab" onclick="switchTab(this, 'doctor')">
            <span class="emoji">👨‍⚕️</span>
            Doctor
        </button>
    </div>

    <!-- Role Info -->
    <div id="parent-info" class="role-info-box active">
        <div class="role-info-title">👨‍👩‍👧 Parent / Guardian</div>
        <div class="role-info-text">Track your child's growth, immunizations, and health records.</div>
    </div>
    <div id="nurse-info" class="role-info-box">
        <div class="role-info-title">👩‍⚕️ Nurse</div>
        <div class="role-info-text">Manage child records, measurements, and immunization schedules as a healthcare worker.</div>
    </div>
    <div id="doctor-info" class="role-info-box">
        <div class="role-info-title">👨‍⚕️ Doctor</div>
        <div class="role-info-text">Review patient health data, approve immunization plans, and provide medical insights.</div>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Hidden role input -->
        <input type="hidden" name="role" id="role-input" value="parent">

        <!-- ===== ACCOUNT INFORMATION ===== -->
        <div class="section-label">Account Information</div>

        <div class="field-row">
            <div class="form-group">
                <label for="name" class="form-label">Full Name</label>
                <input id="name" class="form-input" type="text" name="name" value="{{ old('name') }}" placeholder="John Doe" required autofocus autocomplete="name" />
                @error('name') <p class="form-error">{{ $message }}</p> @enderror
            </div>
            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input id="email" class="form-input" type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required autocomplete="username" />
                @error('email') <p class="form-error">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="field-row">
            <div class="form-group">
                <label for="location" class="form-label">Location / City</label>
                <input id="location" class="form-input" type="text" name="location" value="{{ old('location') }}" placeholder="Your city" />
                @error('location') <p class="form-error">{{ $message }}</p> @enderror
            </div>
            <div class="form-group" id="parent-phone-container">
                <label for="phone_parent" class="form-label">Phone Number <span class="text-xs text-gray-400 font-normal">(must start with 06 or 07, 10 digits)</span></label>
                <input id="phone_parent" class="form-input" type="text" name="phone" value="{{ old('phone') }}" placeholder="07XXXXXXXX or 06XXXXXXXX" maxlength="10" pattern="^(07|06)[0-9]{8}$" autocomplete="tel" />
                <p class="text-xs text-gray-400 mt-1">Example: 0712345678 or 0612345678</p>
                @error('phone') <p class="form-error">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- ===== HEALTHCARE FIELDS ===== -->
        <div id="healthcareFields" class="hidden">
            <div class="section-label" style="margin-top:4px;">Professional Information</div>

            <div class="field-row">
                <div class="form-group">
                    <label for="phone_hc" class="form-label">Phone Number <span class="text-xs text-gray-400 font-normal">(must start with 06 or 07, 10 digits)</span></label>
                    <input id="phone_hc" class="form-input" type="text" name="phone_hc" value="{{ old('phone') }}" placeholder="07XXXXXXXX or 06XXXXXXXX" maxlength="10" pattern="^(07|06)[0-9]{8}$" autocomplete="tel" />
                    <p class="text-xs text-gray-400 mt-1">Example: 0712345678 or 0612345678</p>
                    @error('phone') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-group">
                    <label for="facility_name" class="form-label">Health Facility</label>
                    <input id="facility_name" class="form-input" type="text" name="facility_name" value="{{ old('facility_name') }}" placeholder="Hospital / Clinic name" />
                    @error('facility_name') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="form-group" id="licenseField">
                <label for="license_number" class="form-label">License / Registration Number</label>
                <input id="license_number" class="form-input" type="text" name="license_number" value="{{ old('license_number') }}" placeholder="e.g. LRN-12345-X" />
                <p id="licenseHint" class="license-hint" style="display:none;">🔑 License must include letters, numbers, and special characters</p>
                @error('license_number') <p class="form-error">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- ===== SECURITY ===== -->
        <div class="section-label" style="margin-top:4px;">Security</div>

        <div class="field-row">
            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <div class="password-wrapper">
                    <input id="password" class="form-input pr-10" type="password" name="password" placeholder="Create a strong password" required autocomplete="new-password" />
                    <button type="button" class="password-toggle" onclick="togglePass('password', 'pass-icon')" aria-label="Toggle password visibility">
                        <svg id="pass-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
                @error('password') <p class="form-error">{{ $message }}</p> @enderror
            </div>
            <div class="form-group">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <div class="password-wrapper">
                    <input id="password_confirmation" class="form-input pr-10" type="password" name="password_confirmation" placeholder="Repeat your password" required autocomplete="new-password" />
                    <button type="button" class="password-toggle" onclick="togglePass('password_confirmation', 'confirm-icon')" aria-label="Toggle password visibility">
                        <svg id="confirm-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
                @error('password_confirmation') <p class="form-error">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- Security Note -->
        <div class="security-box">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/>
            </svg>
            <span>Your credentials are encrypted and securely transmitted. Never share your password with anyone.</span>
        </div>

        <!-- Buttons -->
        <div class="btn-row">
            <a href="{{ route('login') }}" class="btn-outline">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                Sign In
            </a>
            <button type="submit" class="btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                Create Account
            </button>
        </div>
    </form>

    <script>
        function switchTab(btn, role) {
            document.querySelectorAll('.role-tab').forEach(function(b) {
                b.classList.toggle('active', b === btn);
            });
            document.getElementById('role-input').value = role;

            document.getElementById('parent-info').classList.toggle('active', role === 'parent');
            document.getElementById('nurse-info').classList.toggle('active', role === 'nurse');
            document.getElementById('doctor-info').classList.toggle('active', role === 'doctor');

            toggleFields(role);
        }

        function toggleFields(role) {
            const hc = document.getElementById('healthcareFields');
            const parentPhone = document.getElementById('parent-phone-container');
            const licField = document.getElementById('licenseField');
            const licHint = document.getElementById('licenseHint');

            if (role === 'nurse' || role === 'doctor') {
                hc.classList.remove('hidden');
                parentPhone.classList.add('hidden');

                // Sync the healthcare phone value from parent phone on first switch
                var hcPhone = document.getElementById('phone_hc');
                var parentPhoneVal = document.getElementById('phone_parent');
                if (!hcPhone.value) hcPhone.value = parentPhoneVal.value;

                licField.style.display = 'block';
                licHint.style.display = (role === 'nurse') ? 'block' : 'none';
            } else {
                hc.classList.add('hidden');
                parentPhone.classList.remove('hidden');
                licHint.style.display = 'none';
            }
        }

        function togglePass(inputId, iconId) {
            var input = document.getElementById(inputId);
            var icon = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = '<path d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>';
            } else {
                input.type = 'password';
                icon.innerHTML = '<path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
            }
        }

        // Initialize on load - phone_hc name should sync to 'phone' for form submission
        document.addEventListener('DOMContentLoaded', function() {
            // On form submit, if healthcare fields are visible, copy phone_hc value to the actual phone input
            var form = document.querySelector('form');
            form.addEventListener('submit', function() {
                var role = document.getElementById('role-input').value;
                if (role === 'nurse' || role === 'doctor') {
                    var hcPhone = document.getElementById('phone_hc');
                    var hiddenPhone = document.getElementById('phone_parent');
                    if (hcPhone && hiddenPhone) {
                        hiddenPhone.value = hcPhone.value;
                    }
                }
            });
        });
    </script>
</x-guest-layout>