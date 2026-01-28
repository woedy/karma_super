# Bank Simulation System

A comprehensive bank simulation system built with vanilla PHP and MySQL, featuring separate admin and client interfaces with transaction approval workflows.

## Features

### Admin Portal
- Secure authentication for multiple admins
- Dashboard with statistics and insights
- Pending transaction approval system
- Complete transaction history with filtering
- Client management interface
- Real-time balance updates

### Client Portal
- Secure client authentication
- Account balance overview
- Transaction history with filtering
- Deposit requests
- Withdrawal requests
- Internal transfers (between accounts in the system)
- External transfers (to external banks)
- All transactions require admin approval

## Technology Stack

- **Backend**: Vanilla PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, Bootstrap 5
- **JavaScript**: Vanilla JS

## Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- phpMyAdmin (optional, for database management)

### Setup Instructions

1. **Clone or Download the Project**
   ```bash
   # Place the BankSim folder in your web server's document root
   # For XAMPP: C:\xampp\htdocs\BankSim
   # For WAMP: C:\wamp64\www\BankSim
   ```

2. **Create Database**
   - Open phpMyAdmin or MySQL command line
   - Create a new database named `banksim`
   ```sql
   CREATE DATABASE banksim CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

3. **Import Database Schema**
   - Import `database/schema.sql` to create tables
   - Import `database/seed_data.sql` to add sample data
   
   **Using phpMyAdmin:**
   - Select the `banksim` database
   - Go to Import tab
   - Choose `schema.sql` and click Go
   - Repeat for `seed_data.sql`
   
   **Using MySQL command line:**
   ```bash
   mysql -u root -p banksim < database/schema.sql
   mysql -u root -p banksim < database/seed_data.sql
   ```

4. **Configure Database Connection**
   - Open `config/config.php`
   - Update database credentials if needed:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'banksim');
   define('DB_USER', 'root');
   define('DB_PASS', ''); // Your MySQL password
   ```

5. **Access the Application**
   - Open your browser and navigate to: `http://localhost/BankSim`

## Default Credentials

### Admin Accounts
- **Username**: admin1 | **Password**: admin123
- **Username**: admin2 | **Password**: admin123

### Client Accounts
- **Username**: john_doe | **Password**: client123 | **Balance**: $5,000.00
- **Username**: jane_smith | **Password**: client123 | **Balance**: $7,500.50
- **Username**: bob_johnson | **Password**: client123 | **Balance**: $12,000.00

## Project Structure

```
BankSim/
├── admin/                  # Admin interface
│   ├── includes/          # Admin partials (header, sidebar)
│   ├── login.php
│   ├── dashboard.php
│   ├── pending_transactions.php
│   ├── approve_transaction.php
│   ├── transactions.php
│   ├── clients.php
│   └── logout.php
├── client/                # Client interface
│   ├── includes/         # Client partials (header)
│   ├── login.php
│   ├── dashboard.php
│   ├── deposit.php
│   ├── withdraw.php
│   ├── transfer.php
│   ├── transactions.php
│   └── logout.php
├── assets/               # Static assets
│   ├── css/
│   │   ├── admin.css
│   │   ├── client.css
│   │   └── common.css
│   └── js/
│       └── main.js
├── config/              # Configuration files
│   └── config.php
├── database/            # Database files
│   ├── schema.sql
│   └── seed_data.sql
├── includes/            # Core PHP classes
│   ├── Auth.php
│   ├── Database.php
│   └── functions.php
├── index.php           # Landing page
└── README.md
```

## Usage Guide

### For Clients

1. **Login**: Use your credentials to access the client portal
2. **View Balance**: See your current balance on the dashboard
3. **Make Deposit**: Submit a deposit request (requires admin approval)
4. **Make Withdrawal**: Submit a withdrawal request (requires admin approval)
5. **Transfer Money**:
   - **Internal**: Transfer to another account in the system
   - **External**: Transfer to an external bank account
6. **View Transactions**: Check your transaction history with filters

### For Admins

1. **Login**: Use admin credentials to access the admin portal
2. **Dashboard**: View statistics and recent transactions
3. **Approve Transactions**:
   - Go to "Pending Transactions"
   - Review transaction details
   - Approve or reject each transaction
4. **View All Transactions**: Access complete transaction history
5. **Manage Clients**: View all registered clients and their accounts

## Security Features

- Password hashing using PHP's `password_hash()`
- SQL injection prevention using prepared statements
- Session-based authentication
- Session timeout after 1 hour of inactivity
- Separate authentication for admin and client roles
- Input sanitization and validation

## Transaction Flow

1. Client submits a transaction request (deposit, withdrawal, or transfer)
2. Transaction is created with `PENDING` status
3. Admin reviews the pending transaction
4. Admin approves or rejects the transaction
5. If approved:
   - Account balances are updated
   - Transaction status changes to `COMPLETED`
   - For internal transfers, recipient account is credited
6. If rejected:
   - Transaction status changes to `REJECTED`
   - No balance changes occur

## Browser Compatibility

- Chrome (recommended)
- Firefox
- Edge
- Safari

## Troubleshooting

### Database Connection Error
- Verify MySQL is running
- Check database credentials in `config/config.php`
- Ensure database `banksim` exists

### Login Issues
- Clear browser cache and cookies
- Verify credentials match the seed data
- Check if session is enabled in PHP

### Transaction Not Processing
- Check if admin has approved the transaction
- Verify sufficient balance for withdrawals/transfers
- Check error logs in browser console

## Future Enhancements

- Email notifications for transaction status
- Transaction receipts (PDF generation)
- Multi-currency support
- Account statements
- Password reset functionality
- Two-factor authentication
- API for mobile applications

## License

This project is for educational purposes.

## Support

For issues or questions, please check the code comments or review the implementation plan.
