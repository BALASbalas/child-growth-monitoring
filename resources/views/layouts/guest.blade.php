<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Child Growth Monitor') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #F8FAFC;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .split-container {
            display: flex;
            width: 100%;
            max-width: 1200px;
            min-height: 600px;
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.08), 0 10px 30px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }

        /* Left Panel - Branding */
        .brand-panel {
            width: 40%;
            background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 50%, #1E40AF 100%);
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }
        .brand-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 70% 60% at 20% 20%, rgba(255,255,255,0.08) 0%, transparent 60%),
                radial-gradient(ellipse 50% 50% at 80% 80%, rgba(255,255,255,0.05) 0%, transparent 50%);
            pointer-events: none;
        }
        .brand-panel * { position: relative; z-index: 1; }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .brand-logo-icon {
            width: 44px;
            height: 44px;
            background: rgba(255,255,255,0.15);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(4px);
        }
        .brand-logo-text {
            font-size: 1.4rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.02em;
        }
        .brand-logo-text span { color: rgba(255,255,255,0.7); font-weight: 400; }

        .brand-content { flex: 1; display: flex; flex-direction: column; justify-content: center; padding: 40px 0; }
        .brand-title {
            font-size: 2rem;
            font-weight: 800;
            color: #fff;
            line-height: 1.2;
            letter-spacing: -0.03em;
            margin-bottom: 16px;
        }
        .brand-description {
            font-size: 0.95rem;
            color: rgba(255,255,255,0.75);
            line-height: 1.7;
            max-width: 360px;
        }

        .brand-features {
            display: flex;
            flex-direction: column;
            gap: 16px;
            margin-top: 10px;
        }
        .brand-feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
            color: rgba(255,255,255,0.8);
            font-size: 0.9rem;
            font-weight: 500;
        }
        .brand-feature-item svg {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
        }
        .brand-feature-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: rgba(255,255,255,0.3);
            flex-shrink: 0;
        }
        .brand-feature-dot.active { background: #60A5FA; }

        .brand-footer {
            font-size: 0.8rem;
            color: rgba(255,255,255,0.4);
        }

        /* Right Panel - Form */
        .form-panel {
            width: 60%;
            padding: 50px 48px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #fff;
            overflow-y: auto;
            max-height: 100vh;
        }

        .form-header { margin-bottom: 28px; }
        .form-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #EFF6FF;
            color: #2563EB;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 5px 12px;
            border-radius: 20px;
            margin-bottom: 10px;
        }
        .form-title {
            font-size: 1.6rem;
            font-weight: 800;
            color: #0F172A;
            letter-spacing: -0.02em;
        }
        .form-subtitle {
            font-size: 0.88rem;
            color: #64748B;
            margin-top: 4px;
        }

        /* === Form Elements === */
        .form-group { margin-bottom: 18px; }
        .form-label {
            display: block;
            font-size: 0.78rem;
            font-weight: 600;
            color: #0F172A;
            margin-bottom: 5px;
        }
        .form-input {
            width: 100%;
            padding: 11px 14px;
            border-radius: 12px;
            border: 1.5px solid #E2E8F0;
            background: #fff;
            color: #0F172A;
            font-size: 0.9rem;
            font-family: 'Inter', sans-serif;
            transition: all 0.2s ease;
            outline: none;
        }
        .form-input::placeholder { color: #94A3B8; }
        .form-input:focus {
            border-color: #2563EB;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            background: #fff;
        }
        .form-input.error { border-color: #EF4444; }

        select.form-input {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2364748B' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
            padding-right: 36px;
        }

        .form-error {
            font-size: 0.72rem;
            color: #EF4444;
            margin-top: 4px;
        }

        .password-wrapper { position: relative; }
        .password-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #94A3B8;
            z-index: 2;
            transition: color 0.2s;
            background: none;
            border: none;
            padding: 0;
            display: flex;
        }
        .password-toggle:hover { color: #2563EB; }

        /* === Buttons === */
        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 12px 24px;
            border-radius: 12px;
            border: none;
            background: linear-gradient(135deg, #2563EB, #1D4ED8);
            color: #fff;
            font-weight: 700;
            font-size: 0.9rem;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.25s ease;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.4);
        }
        .btn-primary:active { transform: translateY(0); }

        .btn-outline {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 12px;
            border: 1.5px solid #E2E8F0;
            background: #fff;
            color: #475569;
            font-weight: 600;
            font-size: 0.88rem;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
        }
        .btn-outline:hover {
            border-color: #2563EB;
            color: #2563EB;
            background: #F8FAFC;
        }

        .btn-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-top: 8px;
        }

        /* === Role Tabs === */
        .role-tabs {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            margin-bottom: 6px;
        }
        .role-tab {
            padding: 10px 8px;
            border-radius: 12px;
            border: 1.5px solid #E2E8F0;
            background: #fff;
            color: #64748B;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.8rem;
            font-family: 'Inter', sans-serif;
            transition: all 0.2s ease;
            text-align: center;
        }
        .role-tab:hover {
            border-color: #93C5FD;
            background: #EFF6FF;
            color: #2563EB;
        }
        .role-tab.active {
            border-color: #2563EB;
            background: #EFF6FF;
            color: #1D4ED8;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.08);
        }
        .role-tab .emoji { font-size: 1.1rem; display: block; margin-bottom: 2px; }

        .role-info-box {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            padding: 12px 14px;
            margin-bottom: 18px;
            display: none;
        }
        .role-info-box.active { display: block; }
        .role-info-title {
            font-size: 0.82rem;
            font-weight: 700;
            color: #0F172A;
            margin-bottom: 2px;
        }
        .role-info-text {
            font-size: 0.78rem;
            color: #64748B;
            line-height: 1.4;
        }

        /* === Field groups === */
        .field-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }
        .section-divider {
            height: 1px;
            background: #E2E8F0;
            margin: 6px 0 14px;
        }
        .section-label {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #94A3B8;
            margin-bottom: 12px;
        }

        /* === Checkbox / Remember === */
        .checkbox-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 4px 0 10px;
        }
        .checkbox-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-size: 0.85rem;
            color: #475569;
        }
        .checkbox-label input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #2563EB;
            border-radius: 4px;
            cursor: pointer;
        }
        .forgot-link {
            font-size: 0.85rem;
            font-weight: 600;
            color: #2563EB;
            text-decoration: none;
        }
        .forgot-link:hover { text-decoration: underline; }

        /* === Security note === */
        .security-box {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            padding: 12px 14px;
            margin: 2px 0 6px;
        }
        .security-box svg {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
            margin-top: 2px;
            color: #64748B;
        }
        .security-box span {
            font-size: 0.78rem;
            color: #64748B;
            line-height: 1.4;
        }

        /* === Back link === */
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.82rem;
            color: #94A3B8;
            text-decoration: none;
            margin-top: 16px;
            transition: color 0.2s;
        }
        .back-link:hover { color: #475569; }

        /* === License hint === */
        .license-hint {
            font-size: 0.72rem;
            color: #D97706;
            margin-top: 4px;
        }

        /* === Responsive === */
        @media (max-width: 900px) {
            body { padding: 0; }
            .split-container {
                flex-direction: column;
                border-radius: 0;
                min-height: 100vh;
                box-shadow: none;
            }
            .brand-panel {
                width: 100%;
                padding: 32px 28px;
                min-height: auto;
                border-radius: 0;
            }
            .brand-content { padding: 20px 0; }
            .brand-title { font-size: 1.5rem; }
            .form-panel {
                width: 100%;
                padding: 32px 24px;
                max-height: none;
            }
            .form-title { font-size: 1.3rem; }
            .field-row { grid-template-columns: 1fr; }
        }
        @media (max-width: 480px) {
            .brand-panel { padding: 24px 20px; }
            .form-panel { padding: 24px 18px; }
            .btn-row { grid-template-columns: 1fr; }
            .role-tabs { gap: 5px; }
            .role-tab { font-size: 0.72rem; padding: 8px 4px; }
        }
    </style>
</head>
<body>
    <div class="split-container">
        <!-- Left Panel - Brand -->
        <div class="brand-panel">
            <div class="brand-logo">
                <div class="brand-logo-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                    </svg>
                </div>
                <div class="brand-logo-text">Child<span>Growth</span></div>
            </div>

            <div class="brand-content">
                <h1 class="brand-title">Monitor & Track<br>Child Development</h1>
                <p class="brand-description">Comprehensive growth monitoring, immunization tracking, and health record management for healthier futures.</p>
                <div class="brand-features">
                    <div class="brand-feature-item">
                        <svg fill="none" stroke="rgba(255,255,255,0.7)" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <span>Secure & encrypted health records</span>
                    </div>
                    <div class="brand-feature-item">
                        <svg fill="none" stroke="rgba(255,255,255,0.7)" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        <span>WHO-standard growth charts</span>
                    </div>
                    <div class="brand-feature-item">
                        <svg fill="none" stroke="rgba(255,255,255,0.7)" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>Immunization scheduling & alerts</span>
                    </div>
                </div>
            </div>

            <div class="brand-footer">© {{ date('Y') }} Child Growth Monitoring System</div>
        </div>

        <!-- Right Panel - Form -->
        <div class="form-panel">
            {{ $slot }}
        </div>
    </div>
</body>
</html>