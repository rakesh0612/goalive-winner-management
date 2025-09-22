# GO ALIVE MEDIA Winner Management System

A comprehensive web-based winner management system for radio stations with features for managing contests, winners, vouchers, and prize distribution.

## 🚀 Features

- **User Authentication**: Role-based access (Admin, Sales, RJ, Collection)
- **Winner Management**: Winner declaration with 90-day cooldown system
- **Voucher System**: Create and allocate vouchers/prizes to winners
- **Real-time Clock**: Server-synchronized Bahrain timezone display
- **Responsive Design**: Modern, mobile-friendly interface
- **Analytics Dashboard**: Comprehensive reporting and statistics

## 📋 Requirements

- **PHP**: 7.4+ (8.0+ recommended)
- **MySQL**: 5.7+ or 8.0+
- **Web Server**: Apache/Nginx with PHP support
- **Browser**: Modern browser with JavaScript enabled

## 🛠️ Installation

### 1. Clone the Repository
```bash
git clone [your-repo-url]
cd livefm_winner_fullpack
```

### 2. Database Setup
1. Create a MySQL database
2. Import the schema:
   ```bash
   mysql -u username -p database_name < schema.sql
   ```

### 3. Configuration
1. Copy the config template:
   ```bash
   cp public_html/ga/api/config.php.template public_html/ga/api/config.php
   ```
2. Edit `config.php` with your database credentials:
   ```php
   $DB_HOST = 'your_db_host';
   $DB_NAME = 'your_db_name';
   $DB_USER = 'your_db_user';
   $DB_PASS = 'your_db_password';
   ```

### 4. Create Admin User
1. Upload files to your web server
2. Visit: `https://yourdomain.com/simple_setup.php`
3. Create your admin user
4. **Delete the setup file** for security

## 🔧 Deployment to Cloudways

### Automated Deployment Setup
1. **Connect GitHub to Cloudways**:
   - Go to your Cloudways application
   - Navigate to "Deployment via Git"
   - Connect your GitHub repository
   - Set deployment branch to `main`

2. **Environment Configuration**:
   - Set up your database credentials in Cloudways
   - Update `config.php` with production values
   - Run database migrations

### Manual Deployment
1. Upload files via SFTP to `/public_html/`
2. Set up database using Cloudways Database Manager
3. Configure domain and SSL

## 🏗️ Project Structure

```
├── public_html/
│   ├── index.html              # Main application
│   ├── import.html             # Data import utility
│   ├── simple_setup.php        # Initial admin setup (delete after use)
│   ├── api/
│   │   └── server_time.php     # Server time API
│   └── ga/
│       └── api/                # Main API endpoints
│           ├── config.php      # Database configuration
│           ├── login.php       # Authentication
│           ├── me.php          # User session info
│           ├── setup_admin.php # Admin setup (delete after use)
│           └── ...
├── schema.sql                  # Database schema
├── README.md                   # This file
└── .gitignore                  # Git ignore rules
```

## 👤 User Roles

- **Admin**: Full system access, user management, system settings
- **Sales**: Create and allocate vouchers
- **RJ**: Declare winners during shows
- **Collection**: Manage prize collection and distribution

## 🔒 Security Notes

- Delete setup files (`simple_setup.php`, `test_db.php`) after initial configuration
- Use strong passwords for database and admin accounts
- Enable HTTPS in production
- Regularly backup your database
- Keep PHP and MySQL updated

## 📱 Browser Compatibility

- Chrome 60+
- Firefox 60+
- Safari 12+
- Edge 79+

## 🐛 Troubleshooting

### Common Issues

1. **Login modal stuck**: Clear browser cache and cookies
2. **Clock not showing**: Check `/api/server_time.php` endpoint
3. **Database errors**: Verify credentials in `config.php`
4. **PHP errors**: Check server error logs

### Debug Mode
Add to `config.php` for development:
```php
ini_set('display_errors', '1');
error_reporting(E_ALL);
```

## 📄 License

This project is proprietary software for GO ALIVE MEDIA.

## 🔄 Version History

- **v1.8.0**: Current version with enhanced UI and voucher system
- **v1.7.x**: Previous versions with basic winner management

---

For support, contact the development team or create an issue in this repository.