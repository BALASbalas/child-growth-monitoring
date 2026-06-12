<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Child Growth Monitor') }} - Child Growth Monitoring & Immunization Tracking System</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #F8FAFC;
            color: #0F172A;
            overflow-x: hidden;
        }

        /* ===== STICKY NAVIGATION ===== */
        .site-nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.97);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
        }
        .site-nav.scrolled {
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        }
        .nav-inner {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 72px;
        }
        .nav-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }
        .nav-logo-icon {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, #2563EB, #1D4ED8);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }
        .nav-logo-text {
            font-size: 1.25rem;
            font-weight: 800;
            color: #0F172A;
            letter-spacing: -0.02em;
        }
        .nav-logo-text span { color: #2563EB; font-weight: 300; }
        .nav-logo-sub {
            font-size: 0.65rem;
            color: #64748B;
            font-weight: 500;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            display: block;
            margin-top: -2px;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .nav-link {
            padding: 8px 16px;
            border-radius: 10px;
            font-size: 0.875rem;
            font-weight: 500;
            color: #475569;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .nav-link:hover {
            background: #F1F5F9;
            color: #2563EB;
        }
        .nav-link.active {
            color: #2563EB;
            background: #EFF6FF;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .nav-btn {
            padding: 9px 20px;
            border-radius: 10px;
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .nav-btn-login {
            color: #475569;
            border: 1.5px solid #E2E8F0;
            background: #fff;
        }
        .nav-btn-login:hover {
            border-color: #2563EB;
            color: #2563EB;
            background: #F8FAFC;
        }
        .nav-btn-register {
            background: linear-gradient(135deg, #2563EB, #1D4ED8);
            color: #fff;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.25);
        }
        .nav-btn-register:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.35);
        }

        /* Mobile Menu Toggle */
        .mobile-toggle {
            display: none;
            background: none;
            border: none;
            cursor: pointer;
            padding: 8px;
            color: #475569;
        }
        .mobile-toggle svg { width: 24px; height: 24px; }

        .mobile-menu {
            display: none;
            position: fixed;
            top: 72px;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid #E2E8F0;
            padding: 16px 24px;
            z-index: 999;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.1);
            transform: translateY(-10px);
            opacity: 0;
            transition: all 0.3s ease;
        }
        .mobile-menu.open {
            display: block;
            transform: translateY(0);
            opacity: 1;
        }
        .mobile-menu a {
            display: block;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 0.95rem;
            font-weight: 500;
            color: #475569;
            text-decoration: none;
            transition: all 0.2s;
        }
        .mobile-menu a:hover {
            background: #F1F5F9;
            color: #2563EB;
        }
        .mobile-menu hr {
            margin: 8px 0;
            border: none;
            border-top: 1px solid #E2E8F0;
        }
        .mobile-menu .btn-mobile-register {
            background: linear-gradient(135deg, #2563EB, #1D4ED8);
            color: #fff;
            text-align: center;
            font-weight: 600;
            margin-top: 8px;
        }
        .mobile-menu .btn-mobile-register:hover {
            background: linear-gradient(135deg, #1D4ED8, #1E40AF);
            color: #fff;
        }

        /* ===== HERO SECTION ===== */
        .hero-section {
            position: relative;
            padding: 140px 0 100px;
            background: linear-gradient(135deg, #1E3A8A 0%, #1D4ED8 30%, #2563EB 60%, #3B82F6 100%);
            overflow: hidden;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .hero-section::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 80% 60% at 10% 20%, rgba(255,255,255,0.08) 0%, transparent 60%),
                radial-gradient(ellipse 60% 50% at 90% 80%, rgba(255,255,255,0.05) 0%, transparent 50%),
                radial-gradient(ellipse 50% 40% at 50% 30%, rgba(255,255,255,0.03) 0%, transparent 40%);
            pointer-events: none;
        }

        .hero-pattern {
            position: absolute;
            inset: 0;
            opacity: 0.04;
            background-image:
                radial-gradient(circle at 20% 50%, #fff 1px, transparent 1px),
                radial-gradient(circle at 80% 20%, #fff 1px, transparent 1px),
                radial-gradient(circle at 40% 80%, #fff 1px, transparent 1px);
            background-size: 60px 60px;
            pointer-events: none;
        }

        .hero-grid-lines {
            position: absolute;
            inset: 0;
            opacity: 0.03;
            background-image:
                linear-gradient(rgba(255,255,255,0.3) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.3) 1px, transparent 1px);
            background-size: 80px 80px;
            pointer-events: none;
        }

        .hero-floating-elements {
            position: absolute;
            inset: 0;
            pointer-events: none;
            overflow: hidden;
        }
        .hero-float-circle {
            position: absolute;
            border-radius: 50%;
            border: 1.5px solid rgba(255,255,255,0.1);
        }
        .hero-float-circle:nth-child(1) {
            width: 400px;
            height: 400px;
            top: -100px;
            right: -80px;
            border-width: 2px;
        }
        .hero-float-circle:nth-child(2) {
            width: 250px;
            height: 250px;
            bottom: 10%;
            left: -50px;
            opacity: 0.6;
        }
        .hero-float-circle:nth-child(3) {
            width: 150px;
            height: 150px;
            top: 30%;
            right: 30%;
            opacity: 0.4;
            border-color: rgba(255,255,255,0.15);
        }

        .hero-inner {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 24px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
            position: relative;
            z-index: 2;
        }

        .hero-content { position: relative; }
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.15);
            padding: 6px 16px 6px 8px;
            border-radius: 50px;
            font-size: 0.78rem;
            font-weight: 600;
            color: rgba(255,255,255,0.9);
            margin-bottom: 24px;
        }
        .hero-badge-dot {
            width: 8px;
            height: 8px;
            background: #60A5FA;
            border-radius: 50%;
            animation: pulse-soft 2s ease-in-out infinite;
        }
        .hero-title {
            font-size: 3.2rem;
            font-weight: 800;
            color: #fff;
            line-height: 1.15;
            letter-spacing: -0.03em;
            margin-bottom: 20px;
        }
        .hero-title .highlight {
            background: linear-gradient(135deg, #93C5FD, #60A5FA);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .hero-description {
            font-size: 1.1rem;
            color: rgba(255,255,255,0.75);
            line-height: 1.7;
            max-width: 540px;
            margin-bottom: 32px;
        }
        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            margin-bottom: 48px;
        }
        .hero-btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 14px 32px;
            border-radius: 14px;
            background: #fff;
            color: #2563EB;
            font-weight: 700;
            font-size: 0.95rem;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        }
        .hero-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.2);
        }
        .hero-btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 14px 32px;
            border-radius: 14px;
            border: 1.5px solid rgba(255,255,255,0.2);
            color: #fff;
            font-weight: 600;
            font-size: 0.95rem;
            text-decoration: none;
            transition: all 0.3s ease;
            background: rgba(255,255,255,0.05);
        }
        .hero-btn-secondary:hover {
            background: rgba(255,255,255,0.1);
            border-color: rgba(255,255,255,0.3);
        }

        .hero-stats-row {
            display: flex;
            gap: 40px;
        }
        .hero-stat h3 {
            font-size: 2rem;
            font-weight: 800;
            color: #fff;
            line-height: 1;
        }
        .hero-stat p {
            font-size: 0.82rem;
            color: rgba(255,255,255,0.55);
            margin-top: 4px;
        }
        .hero-stat-divider {
            width: 1px;
            background: rgba(255,255,255,0.12);
        }

        /* Hero Image */
        .hero-visual {
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }
        .hero-image-wrapper {
            position: relative;
            width: 100%;
            max-width: 520px;
        }
        .hero-main-image {
            width: 100%;
            border-radius: 24px;
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.2);
            position: relative;
            z-index: 2;
        }

        .hero-floating-card {
            position: absolute;
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(12px);
            border-radius: 16px;
            padding: 14px 18px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
            z-index: 3;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .hero-floating-card.card-1 {
            top: 20px;
            left: -30px;
            animation: float 6s ease-in-out infinite;
        }
        .hero-floating-card.card-2 {
            bottom: 40px;
            right: -25px;
            animation: float 6s ease-in-out 2s infinite;
        }
        .hero-floating-card .fc-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .hero-floating-card .fc-text h4 {
            font-size: 0.85rem;
            font-weight: 700;
            color: #0F172A;
        }
        .hero-floating-card .fc-text p {
            font-size: 0.72rem;
            color: #64748B;
        }

        /* ===== SECTION COMMON ===== */
        .section {
            padding: 80px 0;
        }
        .section-inner {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 24px;
        }
        .section-header {
            text-align: center;
            margin-bottom: 56px;
        }
        .section-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #EFF6FF;
            color: #2563EB;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 5px 14px;
            border-radius: 20px;
            margin-bottom: 12px;
        }
        .section-title {
            font-size: 2.2rem;
            font-weight: 800;
            color: #0F172A;
            letter-spacing: -0.02em;
            line-height: 1.2;
        }
        .section-subtitle {
            font-size: 1rem;
            color: #64748B;
            max-width: 600px;
            margin: 12px auto 0;
            line-height: 1.6;
        }

        /* ===== STATISTICS CARDS ===== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }
        .stat-card-modern {
            background: #fff;
            border-radius: 20px;
            padding: 28px 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(226, 232, 240, 0.6);
            transition: all 0.3s ease;
            text-align: center;
        }
        .stat-card-modern:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.1);
            border-color: rgba(37, 99, 235, 0.15);
        }
        .stat-card-icon {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
        }
        .stat-card-modern h3 {
            font-size: 2rem;
            font-weight: 800;
            color: #0F172A;
            line-height: 1;
            margin-bottom: 4px;
        }
        .stat-card-modern p {
            font-size: 0.85rem;
            color: #64748B;
            font-weight: 500;
        }

        /* ===== FEATURE CARDS ===== */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }
        .feature-card {
            background: #fff;
            border-radius: 20px;
            padding: 32px 28px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(226, 232, 240, 0.6);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #2563EB, #60A5FA);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .feature-card:hover::before { opacity: 1; }
        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.1);
        }
        .feature-card-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }
        .feature-card h3 {
            font-size: 1.15rem;
            font-weight: 700;
            color: #0F172A;
            margin-bottom: 8px;
        }
        .feature-card p {
            font-size: 0.88rem;
            color: #64748B;
            line-height: 1.65;
        }
        .feature-card .feature-link {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 0.85rem;
            font-weight: 600;
            color: #2563EB;
            text-decoration: none;
            margin-top: 16px;
            transition: gap 0.2s;
        }
        .feature-card .feature-link:hover { gap: 8px; }

        /* ===== SERVICE MODULES ===== */
        .services-section {
            background: #fff;
        }
        .services-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
        }
        .service-module {
            display: flex;
            gap: 20px;
            padding: 28px 24px;
            border-radius: 20px;
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            transition: all 0.3s ease;
        }
        .service-module:hover {
            background: #fff;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            border-color: rgba(37, 99, 235, 0.15);
        }
        .service-module-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .service-module-content h4 {
            font-size: 1rem;
            font-weight: 700;
            color: #0F172A;
            margin-bottom: 4px;
        }
        .service-module-content p {
            font-size: 0.84rem;
            color: #64748B;
            line-height: 1.6;
        }

        /* ===== HIGHLIGHTS ===== */
        .highlights-section {
            background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);
            position: relative;
            overflow: hidden;
        }
        .highlights-section::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 60% 50% at 20% 50%, rgba(37, 99, 235, 0.08) 0%, transparent 60%),
                radial-gradient(ellipse 50% 40% at 80% 30%, rgba(59, 130, 246, 0.06) 0%, transparent 50%);
            pointer-events: none;
        }
        .highlights-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
            position: relative;
            z-index: 2;
        }
        .highlight-item {
            text-align: center;
            padding: 32px 20px;
            background: rgba(255,255,255,0.03);
            border-radius: 20px;
            border: 1px solid rgba(255,255,255,0.06);
            transition: all 0.3s ease;
        }
        .highlight-item:hover {
            background: rgba(255,255,255,0.06);
            transform: translateY(-2px);
        }
        .highlight-item .hi-icon {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
        }
        .highlight-item h4 {
            font-size: 1rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 6px;
        }
        .highlight-item p {
            font-size: 0.82rem;
            color: rgba(255,255,255,0.5);
            line-height: 1.5;
        }

        /* ===== ANNOUNCEMENTS / TESTIMONIALS ===== */
        .testimonial-card {
            background: #fff;
            border-radius: 20px;
            padding: 32px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(226, 232, 240, 0.6);
            text-align: center;
            max-width: 700px;
            margin: 0 auto;
        }
        .testimonial-card .quote {
            font-size: 1rem;
            color: #475569;
            line-height: 1.7;
            font-style: italic;
        }
        .testimonial-card .author {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-top: 20px;
        }
        .testimonial-card .author img {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            object-fit: cover;
        }
        .testimonial-card .author h5 {
            font-size: 0.9rem;
            font-weight: 700;
            color: #0F172A;
        }
        .testimonial-card .author p {
            font-size: 0.78rem;
            color: #64748B;
        }

        /* ===== CTA SECTION ===== */
        .cta-section {
            background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 50%, #1E40AF 100%);
            position: relative;
            overflow: hidden;
        }
        .cta-section::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 60% 50% at 30% 20%, rgba(255,255,255,0.08) 0%, transparent 60%),
                radial-gradient(ellipse 50% 40% at 70% 80%, rgba(255,255,255,0.05) 0%, transparent 50%);
        }
        .cta-inner {
            position: relative;
            z-index: 2;
            text-align: center;
            padding: 60px 24px;
            max-width: 700px;
            margin: 0 auto;
        }
        .cta-inner h2 {
            font-size: 2.4rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.02em;
            margin-bottom: 16px;
        }
        .cta-inner p {
            font-size: 1.05rem;
            color: rgba(255,255,255,0.75);
            line-height: 1.6;
            margin-bottom: 32px;
        }
        .cta-btns {
            display: flex;
            justify-content: center;
            gap: 14px;
            flex-wrap: wrap;
        }
        .cta-btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 14px 32px;
            border-radius: 14px;
            background: #fff;
            color: #2563EB;
            font-weight: 700;
            font-size: 0.95rem;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        }
        .cta-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.2);
        }
        .cta-btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 14px 32px;
            border-radius: 14px;
            border: 1.5px solid rgba(255,255,255,0.25);
            color: #fff;
            font-weight: 600;
            font-size: 0.95rem;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .cta-btn-secondary:hover {
            background: rgba(255,255,255,0.1);
            border-color: rgba(255,255,255,0.4);
        }

        /* ===== FOOTER ===== */
        .site-footer {
            background: #0F172A;
            color: rgba(255,255,255,0.6);
            padding: 60px 0 30px;
        }
        .footer-inner {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 24px;
        }
        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }
        .footer-brand h3 {
            font-size: 1.2rem;
            font-weight: 800;
            color: #fff;
            margin-bottom: 8px;
        }
        .footer-brand h3 span { color: #60A5FA; }
        .footer-brand p {
            font-size: 0.85rem;
            line-height: 1.6;
            max-width: 320px;
        }
        .footer-col h4 {
            font-size: 0.8rem;
            font-weight: 700;
            color: #fff;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 16px;
        }
        .footer-col a {
            display: block;
            font-size: 0.85rem;
            color: rgba(255,255,255,0.5);
            text-decoration: none;
            padding: 5px 0;
            transition: color 0.2s;
        }
        .footer-col a:hover { color: #60A5FA; }
        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.06);
            padding-top: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.8rem;
        }
        .footer-social {
            display: flex;
            gap: 12px;
        }
        .footer-social a {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: rgba(255,255,255,0.05);
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgba(255,255,255,0.4);
            transition: all 0.2s;
        }
        .footer-social a:hover {
            background: rgba(255,255,255,0.1);
            color: #60A5FA;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 1024px) {
            .hero-inner { grid-template-columns: 1fr; gap: 40px; }
            .hero-title { font-size: 2.6rem; }
            .hero-visual { display: none; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .features-grid { grid-template-columns: repeat(2, 1fr); }
            .highlights-grid { grid-template-columns: repeat(2, 1fr); }
            .footer-grid { grid-template-columns: 1fr 1fr; }
            .services-grid { grid-template-columns: 1fr; }
        }
        @media (max-width: 768px) {
            .nav-links { display: none; }
            .nav-actions .nav-btn-login { display: none; }
            .mobile-toggle { display: flex; }
            .hero-section { padding: 120px 0 60px; min-height: auto; }
            .hero-title { font-size: 2rem; }
            .hero-description { font-size: 0.95rem; }
            .hero-stats-row { gap: 24px; flex-wrap: wrap; }
            .hero-stat h3 { font-size: 1.5rem; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 14px; }
            .features-grid { grid-template-columns: 1fr; }
            .highlights-grid { grid-template-columns: 1fr; }
            .footer-grid { grid-template-columns: 1fr; }
            .section-title { font-size: 1.6rem; }
            .section { padding: 50px 0; }
            .cta-inner h2 { font-size: 1.8rem; }
            .footer-bottom { flex-direction: column; gap: 16px; text-align: center; }
        }
        @media (max-width: 480px) {
            .stats-grid { grid-template-columns: 1fr; }
            .hero-actions { flex-direction: column; }
            .hero-btn-primary, .hero-btn-secondary { width: 100%; justify-content: center; }
            .nav-inner { padding: 0 16px; height: 64px; }
        }
    </style>
</head>
<body>
    <!-- ===== STICKY NAVIGATION ===== -->
    <nav class="site-nav" id="siteNav">
        <div class="nav-inner">
            <a href="{{ url('/') }}" class="nav-logo">
                <div class="nav-logo-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                    </svg>
                </div>
                <div>
                    <div class="nav-logo-text">Child<span>Growth</span></div>
                    <span class="nav-logo-sub">Monitoring System</span>
                </div>
            </a>

            <div class="nav-links">
                <a href="#features" class="nav-link">Features</a>
                <a href="#services" class="nav-link">Services</a>
                <a href="#highlights" class="nav-link">Highlights</a>
                <a href="#about" class="nav-link">About</a>
            </div>

            <div class="nav-actions">
                @auth
                    <a href="{{ url('/dashboard') }}" class="nav-btn nav-btn-register">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4M10 17l5-5-5-5M13 12H3"/></svg>
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="nav-btn nav-btn-login">Sign In</a>
                    <a href="{{ route('register') }}" class="nav-btn nav-btn-register">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                        Get Started
                    </a>
                @endauth
                <button class="mobile-toggle" id="mobileToggle" onclick="toggleMobileMenu()">
                    <svg id="menuIconOpen" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg id="menuIconClose" style="display:none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div class="mobile-menu" id="mobileMenu">
            <a href="#features" onclick="closeMobileMenu()">Features</a>
            <a href="#services" onclick="closeMobileMenu()">Services</a>
            <a href="#highlights" onclick="closeMobileMenu()">Highlights</a>
            <a href="#about" onclick="closeMobileMenu()">About</a>
            <hr>
            @auth
                <a href="{{ url('/dashboard') }}" onclick="closeMobileMenu()">Dashboard</a>
            @else
                <a href="{{ route('login') }}" onclick="closeMobileMenu()">Sign In</a>
                <a href="{{ route('register') }}" class="btn-mobile-register" onclick="closeMobileMenu()">Get Started</a>
            @endauth
        </div>
    </nav>

    <!-- ===== HERO SECTION ===== -->
    <section class="hero-section" id="hero">
        <div class="hero-pattern"></div>
        <div class="hero-grid-lines"></div>
        <div class="hero-floating-elements">
            <div class="hero-float-circle"></div>
            <div class="hero-float-circle"></div>
            <div class="hero-float-circle"></div>
        </div>

        <div class="hero-inner">
            <div class="hero-content">
                <div class="hero-badge">
                    <span class="hero-badge-dot"></span>
                    Digital Health Management Platform
                </div>
                <h1 class="hero-title">
                    Monitor & Track<br>
                    <span class="highlight">Child Development</span><br>
                    Every Step of the Way
                </h1>
                <p class="hero-description">
                    A comprehensive digital platform for child growth monitoring, immunization tracking, 
                    and health record management. Powered by WHO growth standards for accurate assessment.
                </p>
                <div class="hero-actions">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="hero-btn-primary">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            Go to Dashboard
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="hero-btn-primary">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                            Create Free Account
                        </a>
                        <a href="{{ route('login') }}" class="hero-btn-secondary">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4M10 17l5-5-5-5M13 12H3"/></svg>
                            Sign In
                        </a>
                    @endauth
                </div>
                <div class="hero-stats-row">
                    <div class="hero-stat">
                        <h3>5,000+</h3>
                        <p>Children Tracked</p>
                    </div>
                    <div class="hero-stat-divider"></div>
                    <div class="hero-stat">
                        <h3>WHO</h3>
                        <p>Standard Charts</p>
                    </div>
                    <div class="hero-stat-divider"></div>
                    <div class="hero-stat">
                        <h3>99.9%</h3>
                        <p>Data Accuracy</p>
                    </div>
                </div>
            </div>

            <div class="hero-visual">
                <div class="hero-image-wrapper">
                    <img 
                        src="https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                        alt="Child healthcare" 
                        class="hero-main-image"
                        loading="lazy"
                    >
                    <div class="hero-floating-card card-1">
                        <div class="fc-icon" style="background: #EFF6FF;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="fc-text">
                            <h4>Growth Verified</h4>
                            <p>WHO standards met</p>
                        </div>
                    </div>
                    <div class="hero-floating-card card-2">
                        <div class="fc-icon" style="background: #F0FDF4;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#22C55E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        </div>
                        <div class="fc-text">
                            <h4>Up to Date</h4>
                            <p>Immunization on track</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== STATISTICS CARDS ===== -->
    <section class="section" style="padding-top: 60px;">
        <div class="section-inner">
            <div class="stats-grid">
                <div class="stat-card-modern">
                    <div class="stat-card-icon" style="background: #EFF6FF;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <h3>5,000+</h3>
                    <p>Children Registered</p>
                </div>
                <div class="stat-card-modern">
                    <div class="stat-card-icon" style="background: #F0FDF4;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#22C55E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3>45,000+</h3>
                    <p>Growth Measurements</p>
                </div>
                <div class="stat-card-modern">
                    <div class="stat-card-icon" style="background: #FEF3C7;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                    <h3>12,000+</h3>
                    <p>Vaccines Tracked</p>
                </div>
                <div class="stat-card-modern">
                    <div class="stat-card-icon" style="background: #F3E8FF;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#A855F7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <h3>500+</h3>
                    <p>Healthcare Workers</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== FEATURES SECTION ===== -->
    <section class="section" id="features">
        <div class="section-inner">
            <div class="section-header">
                <div class="section-badge">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    Platform Features
                </div>
                <h2 class="section-title">Everything You Need for<br>Child Growth Management</h2>
                <p class="section-subtitle">Comprehensive tools designed for healthcare professionals and parents to monitor, track, and manage child development.</p>
            </div>

            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-card-icon" style="background: #EFF6FF;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <h3>WHO Growth Charts</h3>
                    <p>Standard growth charts with Z-score calculations for accurate assessment of child development against WHO benchmarks.</p>
                    <a href="#learn-more" class="feature-link">Explore Charts <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg></a>
                </div>

                <div class="feature-card">
                    <div class="feature-card-icon" style="background: #F0FDF4;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#22C55E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                    <h3>Immunization Tracking</h3>
                    <p>Complete vaccine schedule management with automatic reminders, overdue alerts, and comprehensive immunization history.</p>
                    <a href="#learn-more" class="feature-link">View Schedules <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg></a>
                </div>

                <div class="feature-card">
                    <div class="feature-card-icon" style="background: #FEF3C7;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
                    </div>
                    <h3>Device Integration</h3>
                    <p>Connect digital scales and measuring devices for automatic data capture. Eliminate manual entry errors.</p>
                    <a href="#learn-more" class="feature-link">Connect Devices <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg></a>
                </div>

                <div class="feature-card">
                    <div class="feature-card-icon" style="background: #F3E8FF;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#A855F7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <h3>Health Alerts</h3>
                    <p>Automatic alerts for abnormal growth patterns, overdue vaccinations, and important health milestones.</p>
                    <a href="#learn-more" class="feature-link">Set Alerts <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg></a>
                </div>

                <div class="feature-card">
                    <div class="feature-card-icon" style="background: #E0F2FE;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#0EA5E9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
                    </div>
                    <h3>Reports & Analytics</h3>
                    <p>Generate detailed reports, view population health statistics, and export data for further analysis.</p>
                    <a href="#learn-more" class="feature-link">View Reports <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg></a>
                </div>

                <div class="feature-card">
                    <div class="feature-card-icon" style="background: #FCE7F3;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#EC4899" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <h3>Multi-User Roles</h3>
                    <p>Role-based access control for administrators, healthcare workers, doctors, and parents with tailored dashboards.</p>
                    <a href="#learn-more" class="feature-link">Learn More <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg></a>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== SERVICES / MODULES ===== -->
    <section class="section services-section" id="services">
        <div class="section-inner">
            <div class="section-header">
                <div class="section-badge">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    Our Services
                </div>
                <h2 class="section-title">Comprehensive Child Health<br>Management Modules</h2>
                <p class="section-subtitle">Integrated modules designed to provide complete care from birth through adolescence.</p>
            </div>

            <div class="services-grid">
                <div class="service-module">
                    <div class="service-module-icon" style="background: #EFF6FF;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <div class="service-module-content">
                        <h4>Child Registration & Records</h4>
                        <p>Secure digital records for every child including demographics, medical history, and growth tracking.</p>
                    </div>
                </div>
                <div class="service-module">
                    <div class="service-module-icon" style="background: #F0FDF4;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#22C55E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                    </div>
                    <div class="service-module-content">
                        <h4>Growth Measurement Tracking</h4>
                        <p>Record weight, height, head circumference, and BMI with automatic WHO Z-score calculations.</p>
                    </div>
                </div>
                <div class="service-module">
                    <div class="service-module-icon" style="background: #FEF3C7;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                    <div class="service-module-content">
                        <h4>Immunization Management</h4>
                        <p>Complete vaccine schedule with age-based recommendations, administration tracking, and compliance monitoring.</p>
                    </div>
                </div>
                <div class="service-module">
                    <div class="service-module-icon" style="background: #F3E8FF;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#A855F7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
                    </div>
                    <div class="service-module-content">
                        <h4>Device & Equipment Sync</h4>
                        <p>Seamless integration with digital scales, height meters, and other medical devices for automatic data capture.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== SYSTEM HIGHLIGHTS ===== -->
    <section class="section highlights-section" id="highlights">
        <div class="section-inner">
            <div class="section-header">
                <div class="section-badge" style="background: rgba(255,255,255,0.1); color: #93C5FD;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    System Highlights
                </div>
                <h2 class="section-title" style="color: #fff;">Why Healthcare Providers<br>Choose Our Platform</h2>
                <p class="section-subtitle" style="color: rgba(255,255,255,0.5);">Built for accuracy, reliability, and ease of use in child healthcare management.</p>
            </div>

            <div class="highlights-grid">
                <div class="highlight-item">
                    <div class="hi-icon" style="background: rgba(37,99,235,0.15);">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#60A5FA" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <h4>WHO Standards Compliant</h4>
                    <p>All growth charts and assessments follow the latest WHO child growth standards for accuracy.</p>
                </div>
                <div class="highlight-item">
                    <div class="hi-icon" style="background: rgba(34,197,94,0.15);">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#4ADE80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <h4>Enterprise-Grade Security</h4>
                    <p>End-to-end encryption, secure authentication, and role-based access control for data protection.</p>
                </div>
                <div class="highlight-item">
                    <div class="hi-icon" style="background: rgba(168,85,247,0.15);">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#C084FC" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <h4>Real-Time Analytics</h4>
                    <p>Instant access to growth trends, immunization coverage, and population health statistics.</p>
                </div>
                <div class="highlight-item">
                    <div class="hi-icon" style="background: rgba(251,191,36,0.15);">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#FBBF24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                    </div>
                    <h4>Multi-User Collaboration</h4>
                    <p>Healthcare teams can collaborate seamlessly with role-specific dashboards and permissions.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== TESTIMONIAL / ANNOUNCEMENT ===== -->
    <section class="section" id="about">
        <div class="section-inner">
            <div class="section-header">
                <div class="section-badge">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    Testimonials
                </div>
                <h2 class="section-title">Trusted by Healthcare<br>Professionals</h2>
            </div>

            <div class="testimonial-card">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto 16px; opacity: 0.3;">
                    <path d="M3 21c3 0 7-1 7-8V5c0-1.25-.756-2.017-2-2H4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2 1 0 1 0 1 1v1c0 1-1 2-2 2s-1 .008-1 1.031V20c0 1 0 1 1 1z"/>
                    <path d="M15 21c3 0 7-1 7-8V5c0-1.25-.757-2.017-2-2h-4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2h.75c0 2.25.25 4-2.75 4v3c0 1 0 1 1 1z"/>
                </svg>
                <p class="quote">"This system has transformed how we track child growth in our clinic. The WHO-standard charts and automated immunization reminders have significantly improved our quality of care. Parents love the transparency and easy access to their children's health records."</p>
                <div class="author">
                    <img src="https://images.unsplash.com/photo-1559839734-2b71ea197ec2?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80" alt="Dr. Sarah">
                    <div>
                        <h5>Dr. Sarah Mitchell</h5>
                        <p>Pediatrician, Nairobi Children's Hospital</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== CTA SECTION ===== -->
    <section class="section cta-section" style="padding: 0;">
        <div class="cta-inner">
            <h2>Ready to Transform<br>Child Healthcare?</h2>
            <p>Join thousands of healthcare professionals and parents who trust our platform for comprehensive child growth monitoring and immunization tracking.</p>
            <div class="cta-btns">
                @auth
                    <a href="{{ url('/dashboard') }}" class="cta-btn-primary">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        Go to Dashboard
                    </a>
                @else
                    <a href="{{ route('register') }}" class="cta-btn-primary">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                        Get Started Free
                    </a>
                    <a href="{{ route('login') }}" class="cta-btn-secondary">Sign In</a>
                @endauth
            </div>
        </div>
    </section>

    <!-- ===== FOOTER ===== -->
    <footer class="site-footer">
        <div class="footer-inner">
            <div class="footer-grid">
                <div class="footer-brand">
                    <h3>Child<span>Growth</span> Monitor</h3>
                    <p>A comprehensive digital platform for child growth monitoring, immunization tracking, and health record management. Powered by WHO growth standards.</p>
                </div>
                <div class="footer-col">
                    <h4>Platform</h4>
                    <a href="#features">Features</a>
                    <a href="#services">Services</a>
                    <a href="#highlights">Highlights</a>
                    <a href="#about">About</a>
                </div>
                <div class="footer-col">
                    <h4>Resources</h4>
                    <a href="{{ route('login') }}">Sign In</a>
                    <a href="{{ route('register') }}">Register</a>
                    <a href="#">Documentation</a>
                    <a href="#">Support</a>
                </div>
                <div class="footer-col">
                    <h4>Legal</h4>
                    <a href="#">Privacy Policy</a>
                    <a href="#">Terms of Service</a>
                    <a href="#">Security</a>
                    <a href="#">Contact Us</a>
                </div>
            </div>
            <div class="footer-bottom">
                <span>&copy; {{ date('Y') }} Child Growth Monitoring System. All rights reserved.</span>
                <div class="footer-social">
                    <a href="#" aria-label="Twitter">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    </a>
                    <a href="#" aria-label="GitHub">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
                    </a>
                    <a href="#" aria-label="LinkedIn">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        function toggleMobileMenu() {
            var menu = document.getElementById('mobileMenu');
            var iconOpen = document.getElementById('menuIconOpen');
            var iconClose = document.getElementById('menuIconClose');
            menu.classList.toggle('open');
            if (menu.classList.contains('open')) {
                iconOpen.style.display = 'none';
                iconClose.style.display = 'flex';
            } else {
                iconOpen.style.display = 'flex';
                iconClose.style.display = 'none';
            }
        }
        function closeMobileMenu() {
            var menu = document.getElementById('mobileMenu');
            var iconOpen = document.getElementById('menuIconOpen');
            var iconClose = document.getElementById('menuIconClose');
            menu.classList.remove('open');
            iconOpen.style.display = 'flex';
            iconClose.style.display = 'none';
        }

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            var nav = document.getElementById('siteNav');
            if (window.scrollY > 20) {
                nav.classList.add('scrolled');
            } else {
                nav.classList.remove('scrolled');
            }
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                var target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });

        // Close mobile menu on resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                closeMobileMenu();
            }
        });
    </script>
</body>
</html>