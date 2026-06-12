# Digital Child Growth Monitoring and Immunization Tracking System

A comprehensive web-based system for monitoring child growth and tracking immunizations using WHO growth standards, with digital device integration capabilities.

## 🚀 Features

### Core Modules
1. **Child Registration** - Digital registration with unique IDs, medical history, and family information
2. **Growth Monitoring** - Automated weight, height, head circumference, and MUAC measurements with WHO Z-score calculations
3. **Immunization Tracking** - Complete vaccine schedule management with automatic reminders
4. **Digital Device Integration** - API for connecting digital scales and measuring devices
5. **Reports & Analytics** - Growth charts, statistics, and exportable reports

### User Roles
- **Administrator** - Full system access and management
- **Nurse/Healthcare Worker** - Professional growth monitoring and immunization administration
- **Guardian** - Care for children under legal guardianship
- **Parent** - Track own children's growth and health

### Key Capabilities
- ✅ WHO growth standard Z-score calculations (WAZ, HAZ, WHZ, BMI-for-age)
- ✅ Nutritional status classification (underweight, stunting, wasting)
- ✅ Automated immunization schedule generation
- ✅ Growth alerts for alarming patterns
- ✅ Real-time device data integration
- ✅ Multi-user support with role-based access
- ✅ Growth velocity tracking
- ✅ Exportable reports (JSON/PDF)

## 🛠️ Technology Stack

- **Backend**: Laravel 12 (PHP 8.2)
- **Frontend**: Blade Templates, Tailwind CSS, JavaScript
- **Database**: SQLite (development) / MySQL (production)
- **Authentication**: Laravel Breeze
- **Charts**: Chart.js (ready for integration)
- **Device Integration**: Web Serial API, REST API

## 📋 Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js & NPM
- SQLite or MySQL

### Setup Instructions

1. **Clone or navigate to project directory**
```bash
cd child-growth-monitoring
```

2. **Install PHP dependencies**
```bash
composer install
```

3. **Install JavaScript dependencies**
```bash
npm install
```

4. **Configure environment**
```bash
cp .env.example .env
php artisan key:generate
```

5. **Run migrations and seeders**
```bash
php artisan migrate
php artisan db:seed
```

6. **Build assets**
```bash
npm run build
```

7. **Start development server**
```bash
php artisan serve
```

8. **Access the application**
- URL: http://127.0.0.1:8000
- Default user: `test@example.com` / `password`

## 🗄️ Database Schema

### Tables
1. **users** - User accounts with roles (admin, nurse, guardian, parent)
2. **children** - Child profiles with medical and family information
3. **growth_measurements** - Weight, height, and other measurements with Z-scores
4. **immunizations** - Vaccine records and schedules
5. **immunization_schedules** - Standard vaccination schedule templates
6. **who_growth_standards** - WHO growth reference data
7. **device_connections** - Digital measuring device configurations

## 🎯 Usage Guide

### For Healthcare Workers (Admin/Nurse)
1. Register/login with healthcare worker credentials
2. Register new children or select existing patients
3. Record growth measurements (manual or via connected devices)
4. Generate and manage immunization schedules
5. View growth charts and generate reports
6. Monitor multiple children across facilities

### For Parents/Guardians
1. Register as parent/guardian
2. Add your child's information
3. Track growth measurements over time
4. View immunization schedules and due dates
5. Export growth reports for school/medical records

## 🔌 Device Integration

### Supported Devices
- Digital weight scales (serial/USB/Bluetooth)
- Digital height rods/infantometers
- MUAC tapes
- Multi-function measuring devices

### API Endpoints
```
POST /api/devices/receive-data
{
    "device_serial": "SCALE001",
    "child_unique_id": "CHD-XXXXXXXX",
    "measurement_type": "weight|height|head_circumference|muac",
    "value": 12.5,
    "unit": "kg|cm",
    "timestamp": "2026-05-18 14:30:00"
}
```

## 📊 WHO Growth Standards

The system implements WHO 2006 growth standards using the LMS method:
- **Weight-for-age (WAZ)** - Identifies underweight children
- **Height-for-age (HAZ)** - Identifies stunting (chronic malnutrition)
- **Weight-for-height (WHZ)** - Identifies wasting (acute malnutrition)
- **BMI-for-age (BAZ)** - Identifies overweight/obesity

### Z-score Interpretation
- **Z < -3**: Severe malnutrition
- **-3 ≤ Z < -2**: Moderate malnutrition
- **-2 ≤ Z < 1**: Normal
- **1 ≤ Z < 2**: Possible risk of overweight
- **2 ≤ Z < 3**: Overweight
- **Z ≥ 3**: Obese

## 🔒 Security Features

- Role-based access control
- CSRF protection
- Password hashing
- SQL injection prevention
- XSS protection
- User authentication and authorization

## 🚧 Future Enhancements

1. **SMS/Email Reminders** - Automated immunization appointment reminders
2. **Mobile App** - React Native/Flutter mobile application
3. **Offline Mode** - PWA support for offline data entry
4. **Advanced Analytics** - Population health trends and reporting
5. **Multi-language Support** - Swahili, English, and other languages
6. **PDF Report Generation** - Professional printable reports
7. **Growth Chart Visualization** - Interactive WHO growth charts with Chart.js
8. **Telemedicine Integration** - Remote consultations with healthcare providers

## 🤝 Contributing

This is an academic/educational project. Contributions are welcome for:
- Bug fixes
- Feature enhancements
- Documentation improvements
- UI/UX improvements
- Additional language support

## 📄 License

This project is open-source software for educational and healthcare purposes.

## 👥 Credits

Developed for digital healthcare improvement in child growth monitoring and immunization tracking.

### WHO References
- WHO Child Growth Standards: https://www.who.int/tools/child-growth-standards
- WHO Immunization Schedules: https://www.who.int/health-topics/immunization-vaccines-and-biologicals

## 📞 Support

For technical support or questions:
- Email: support@childgrowth.local
- Documentation: /docs folder

---

**Note**: This system is designed to assist healthcare workers and parents in monitoring child growth. It should not replace professional medical advice. Always consult qualified healthcare providers for medical decisions.