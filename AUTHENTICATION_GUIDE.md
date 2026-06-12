# Child Growth Monitoring - Authentication System Guide

## Overview

The application has a complete role-based authentication system with separate dashboards for different user types. Each user has personal login credentials that cannot be shared.

## User Types & Roles

### 1. **Parent/Guardian** 👨‍👩‍👧
- **Access**: Track their own children's growth and immunization records
- **Login**: Yes (via registration or invitation)
- **Password**: Personal, non-shareable
- **Dashboard**: `parent.dashboard` - Shows only their children

### 2. **Nurse** 👩‍⚕️
- **Access**: Healthcare professional features
- **Login**: Yes (via registration)
- **Requirements**: License number with special characters (e.g., RN-2024#001)
- **Password**: Personal, non-shareable
- **Dashboard**: `nurse.dashboard` - Manage children and measurements

### 3. **Doctor** 👨‍⚕️
- **Access**: Medical professional features
- **Login**: Yes (via registration)
- **Requirements**: License number with special characters
- **Password**: Personal, non-shareable
- **Dashboard**: `doctor.dashboard` - Review ALL children's health data

### 4. **Admin** ⚙️
- **Access**: Full system administration
- **Login**: Yes (via registration - currently disabled, DB creation only)
- **Password**: Personal, non-shareable
- **Dashboard**: `admin.dashboard` - Manage users, system settings, reports

## Authentication Flow

### Login Process
1. User goes to `/login`
2. Login page shows role information with tabs
3. User enters email and password
4. System authenticates credentials
5. User is automatically redirected to their role-specific dashboard

### Registration Process
1. User goes to `/register`
2. Selects their role (Parent, Nurse, or Doctor)
3. Fills in required information:
   - **All roles**: Name, Email, Password, Location
   - **Healthcare workers**: Phone, Facility Name, License Number
4. System validates:
   - Email is unique
   - Password is unique (not used by another user)
   - Healthcare license meets format requirements
5. User is automatically logged in and sent to their dashboard

## Security Features

### Password Protection
- ✅ Each user has their own unique password
- ✅ Passwords are never shared between users
- ✅ System validates password uniqueness
- ✅ Passwords are hashed before storage
- ✅ Show/Hide toggle for visibility during login/registration

### Access Control
- ✅ Role-based middleware protects routes
- ✅ Users can only access their own resources (parents see only their children)
- ✅ Healthcare workers (nurse/doctor) see allowed children
- ✅ Admins see system-wide data
- ✅ Unauthorized access redirects to user's dashboard with error message

### Data Privacy
- 🔒 Parent accounts: Only see their registered children
- 🔒 Nurse/Doctor accounts: See assigned children data
- 🔒 Admin accounts: Full system visibility
- 🔒 Passwords hidden from serialization (User model)

## Login Page Features

The improved login page (`resources/views/auth/login.blade.php`) includes:

- **Role Tabs**: Visual tabs for each user type (Parent, Nurse, Doctor, Admin)
- **Role-Specific Information**: Description of each role
- **Security Notice**: "Each user has their own unique login credentials"
- **Password Toggle**: Show/hide password visibility
- **Remember Me**: Optional session persistence
- **Account Recovery**: Forgot password link

## Registration Page Features

The updated registration page (`resources/views/auth/register.blade.php`) includes:

- **Clear Role Labels**: With emojis for easy identification
- **Dynamic Fields**: Show healthcare fields only when nurse/doctor selected
- **License Validation**: 
  - For nurses: Must contain special characters + numbers + letters
  - Examples: `RN-2024#001`, `NMC*TZ*12345`, `NURSE/REG-001`
- **Unique Credentials**: Email and password uniqueness validation
- **Security Notice**: Password sharing warning
- **Password Toggle**: Show/hide password visibility

## Redirect Behavior

After successful login, users are automatically redirected:

- **Parents** → `/parent/dashboard`
- **Nurses** → `/nurse/dashboard`
- **Doctors** → `/doctor/dashboard`
- **Admins** → `/admin/dashboard`

If user tries to access unauthorized route, they're redirected to their dashboard with error message.

## Database Structure

### Users Table
```
- id
- name (required)
- email (unique)
- password (unique)
- role (enum: admin, nurse, doctor, parent)
- phone (optional)
- facility_name (optional - for healthcare workers)
- license_number (optional - for healthcare workers)
- location (optional)
- email_verified_at
- remember_token
```

## Key Files

### Authentication Controllers
- `app/Http/Controllers/Auth/AuthenticatedSessionController.php` - Login handling
- `app/Http/Controllers/Auth/RegisteredUserController.php` - Registration and validation

### Middleware
- `app/Http/Middleware/RoleMiddleware.php` - Role-based access control

### Views
- `resources/views/auth/login.blade.php` - Login form (improved)
- `resources/views/auth/register.blade.php` - Registration form (updated)
- `resources/views/parent-dashboard/dashboard.blade.php` - Parent dashboard
- `resources/views/nurse/dashboard.blade.php` - Nurse dashboard
- `resources/views/doctor/dashboard.blade.php` - Doctor dashboard
- `resources/views/admin/dashboard.blade.php` - Admin dashboard

### Models
- `app/Models/User.php` - User model with role attributes and methods

## Testing Credentials

To test the system, you can create test users through the registration page or via database:

```php
// Example in tinker or migration
User::create([
    'name' => 'Test Parent',
    'email' => 'parent@test.com',
    'password' => bcrypt('password123'),
    'role' => 'parent',
    'location' => 'Dar es Salaam'
]);

User::create([
    'name' => 'Dr. Test',
    'email' => 'doctor@test.com',
    'password' => bcrypt('password456'),
    'role' => 'doctor',
    'facility_name' => 'City Hospital',
    'license_number' => 'DOC-2024#001'
]);
```

## Important Notes

1. **No Shared Passwords**: The system enforces password uniqueness - no two users can have the same password
2. **Email Verification**: Users can opt to verify email address before full access
3. **Rate Limiting**: Login attempts are rate-limited (5 attempts before throttle)
4. **Session Management**: Users can choose "Remember me" for persistent sessions
5. **Role Labels**: User model has `getRoleLabelAttribute()` for displaying friendly role names

## Troubleshooting

### User sees "You do not have permission to access this page"
- Check user's role matches the required role for that page
- User tried to access route they don't have permission for
- Verify middleware is properly applied to the route

### Email already exists error
- Each email must be unique in the system
- User may have already registered
- Suggest using forgot password if they can't remember their account

### Password already in use
- System enforces unique passwords
- User should choose a different password that no one else is using

### License number validation fails
- For nurses: Must include special characters + numbers + letters
- Examples that work: `RN-2024#001`, `NMC*TZ*12345`, `NURSE/REG-001`

---

**System Status**: ✅ Fully Functional
**Last Updated**: May 21, 2026
