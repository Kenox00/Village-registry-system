# Village Birth and Death Register System

A comprehensive PHP-based web application for managing birth and death registrations in villages, built with HTML, CSS, MySQL, and Bootstrap.

## 🚀 Features

### Core Functionality
- **User Authentication**: Secure login/logout system with PHP sessions
- **Role-based Access Control**: Admin and Officer roles with different permissions
- **Birth Registration**: Complete birth record management with detailed forms
- **Death Registration**: Comprehensive death record tracking
- **Record Viewing**: Paginated lists with search functionality
- **PDF Certificate Generation**: Professional birth and death certificates
- **Responsive Design**: Mobile-friendly interface using Bootstrap 5

### Technical Features
- **Security**: Input validation, SQL injection prevention using prepared statements
- **Database**: MySQL with proper foreign key relationships
- **Search**: Advanced search across multiple fields
- **Pagination**: Efficient record browsing
- **Print-friendly PDFs**: Certificate generation with print optimization

## 📋 Requirements

- **Web Server**: Apache (XAMPP/WAMP/LAMP)
- **PHP**: Version 7.4 or higher
- **MySQL**: Version 5.7 or higher
- **Web Browser**: Modern browser with JavaScript enabled

## 🛠️ Installation

### 1. Setup Environment
```bash
# If using XAMPP, start Apache and MySQL services
# Place project in htdocs folder: C:\xampp\htdocs\finalproject
```

### 2. Database Setup
1. Access the setup page: `http://localhost/finalproject/setup.php`
2. This will automatically:
   - Create the `village_register` database
   - Create all necessary tables
   - Insert default admin and officer users

### 3. Default Login Credentials
- **Administrator**: 
  - Email: `admin@village.com`
  - Password: `admin123`
- **Officer**: 
  - Email: `officer@village.com`
  - Password: `officer123`

## 📁 Project Structure

```
finalproject/
├── includes/
│   ├── db.php              # Database connection
│   ├── auth.php            # Authentication functions
│   ├── header.php          # Common header
│   └── footer.php          # Common footer
├── pdf/
│   └── SimplePDF.php       # PDF generation class
├── setup.php               # Database setup script
├── login.php               # User login
├── logout.php              # User logout
├── index.php               # Dashboard
├── register_birth.php      # Birth registration form
├── register_death.php      # Death registration form
├── view_births.php         # Birth records listing
├── view_deaths.php         # Death records listing
├── generate_birth_cert.php # Birth certificate PDF
├── generate_death_cert.php # Death certificate PDF
└── README.md              # This file
```

## 🗄️ Database Schema

### Users Table
```sql
users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'officer') DEFAULT 'officer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)
```

### Birth Records Table
```sql
birth_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    child_name VARCHAR(100) NOT NULL,
    dob DATE NOT NULL,
    gender ENUM('Male', 'Female') NOT NULL,
    mother_name VARCHAR(100) NOT NULL,
    father_name VARCHAR(100) NOT NULL,
    village VARCHAR(100) NOT NULL,
    sector VARCHAR(100) NOT NULL,
    registered_by INT NOT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (registered_by) REFERENCES users(id)
)
```

### Death Records Table
```sql
death_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    deceased_name VARCHAR(100) NOT NULL,
    dod DATE NOT NULL,
    cause_of_death TEXT NOT NULL,
    family_contact VARCHAR(100) NOT NULL,
    village VARCHAR(100) NOT NULL,
    sector VARCHAR(100) NOT NULL,
    registered_by INT NOT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (registered_by) REFERENCES users(id)
)
```

## 🔧 Configuration

### Database Configuration
Edit `includes/db.php` to modify database settings:

```php
$host = 'localhost';        // Database host
$username = 'root';         // Database username
$password = '';             // Database password
$database = 'village_register'; // Database name
```

### Security Settings
- Passwords are hashed using PHP's `password_hash()` function
- All database queries use prepared statements
- Input validation and sanitization implemented
- Session-based authentication with secure logout

## 📱 Usage Guide

### 1. Login
- Access `http://localhost/finalproject/`
- Use the provided credentials or create new users via database

### 2. Dashboard
- View system statistics
- Quick access to registration forms
- Recent activities overview

### 3. Register Birth
- Fill out comprehensive birth registration form
- All fields are required and validated
- Automatic registration date timestamp

### 4. Register Death
- Complete death registration with cause details
- Family contact information required
- Location tracking (village/sector)

### 5. View Records
- Paginated listing of all records
- Search functionality across multiple fields
- Detailed view modals for each record

### 6. Generate Certificates
- Professional PDF certificates
- Print-optimized layout
- Official formatting with certificate numbers

## 🎨 Customization

### Styling
- Bootstrap 5 for responsive design
- Custom CSS in `includes/header.php`
- Color scheme can be modified in CSS variables

### PDF Certificates
- Customize layout in certificate generation files
- Modify `pdf/SimplePDF.php` for advanced PDF features
- For production, consider integrating with dompdf or TCPDF

### Adding Features
- User management system
- Advanced reporting
- Data export functionality
- Email notifications
- Audit trails

## 🔒 Security Considerations

### Implemented Security Measures
- **SQL Injection Prevention**: Prepared statements for all queries
- **Password Security**: Bcrypt hashing for password storage
- **Session Management**: Secure session handling with proper logout
- **Input Validation**: Server-side validation for all forms
- **XSS Prevention**: HTML escaping for all output

### Additional Recommendations for Production
- Enable HTTPS/SSL
- Implement rate limiting for login attempts
- Add CSRF protection
- Regular security updates
- Database backup strategy
- Error logging and monitoring

## 🚀 Deployment

### For Production Environment
1. **Web Server**: Configure Apache with proper security headers
2. **Database**: Use dedicated MySQL server with proper user privileges
3. **PHP Configuration**: Disable debug mode, enable error logging
4. **Backup Strategy**: Implement regular database backups
5. **SSL Certificate**: Enable HTTPS for secure communications

### Environment Variables
For production, consider moving sensitive configuration to environment variables:
```php
$host = $_ENV['DB_HOST'] ?? 'localhost';
$username = $_ENV['DB_USER'] ?? 'root';
$password = $_ENV['DB_PASS'] ?? '';
$database = $_ENV['DB_NAME'] ?? 'village_register';
```

## 🐛 Troubleshooting

### Common Issues

**Database Connection Failed**
- Verify MySQL service is running
- Check database credentials in `includes/db.php`
- Ensure database exists (run setup.php)

**Login Issues**
- Verify user credentials
- Check if session is properly started
- Clear browser cookies and try again

**PDF Generation Issues**
- Ensure proper file permissions
- Check if PDF directory is writable
- For advanced PDF features, install dompdf via Composer

**Permission Denied**
- Verify user roles in database
- Check authentication middleware in each page

## 📞 Support

For technical support or feature requests:
- Check the troubleshooting section
- Review the code comments for implementation details
- Modify the system according to your specific requirements

## 📝 License

This project is developed for educational and administrative purposes. Feel free to modify and adapt according to your needs.

## 🤝 Contributing

Contributions are welcome! Please consider:
- Code documentation
- Security improvements
- Feature enhancements
- Bug fixes
- UI/UX improvements

---

**Developed for efficient village record management with modern web technologies.**
