-- Sample Data for Bank Simulation System
-- Note: All passwords are hashed using PHP's password_hash() with PASSWORD_DEFAULT
-- Default password for all users: 'password123'

-- Insert Admin Users
-- Password: admin123
INSERT INTO admins (username, email, password_hash, full_name) VALUES
('admin1', 'admin1@banksim.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin One'),
('admin2', 'admin2@banksim.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin Two');

-- Insert Client Users
-- Password: client123
INSERT INTO clients (username, email, password_hash, full_name, phone) VALUES
('john_doe', 'john.doe@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John Doe', '+1234567890'),
('jane_smith', 'jane.smith@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jane Smith', '+1234567891'),
('bob_johnson', 'bob.johnson@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Bob Johnson', '+1234567892');

-- Insert Accounts for Clients
INSERT INTO accounts (client_id, account_number, balance, account_type, status) VALUES
(1, 'ACC1001234567', 5000.00, 'SAVINGS', 'ACTIVE'),
(2, 'ACC1001234568', 7500.50, 'CHECKING', 'ACTIVE'),
(3, 'ACC1001234569', 12000.00, 'BUSINESS', 'ACTIVE');

-- Insert Sample Transactions
-- Pending Deposit
INSERT INTO transactions (account_id, transaction_type, amount, description, status) VALUES
(1, 'DEPOSIT', 1000.00, 'Cash deposit at branch', 'PENDING');

-- Pending Withdrawal
INSERT INTO transactions (account_id, transaction_type, amount, description, status) VALUES
(2, 'WITHDRAWAL', 500.00, 'ATM withdrawal request', 'PENDING');

-- Pending Internal Transfer
INSERT INTO transactions (account_id, transaction_type, amount, recipient_account, recipient_name, description, status) VALUES
(1, 'INTERNAL_TRANSFER', 250.00, 'ACC1001234568', 'Jane Smith', 'Payment for services', 'PENDING');

-- Pending External Transfer
INSERT INTO transactions (account_id, transaction_type, amount, recipient_account, recipient_name, recipient_bank, description, status) VALUES
(3, 'EXTERNAL_TRANSFER', 1500.00, 'EXT9876543210', 'ABC Company', 'First National Bank', 'Vendor payment', 'PENDING');

-- Completed Deposit (already approved)
INSERT INTO transactions (account_id, transaction_type, amount, description, status, processed_at, processed_by) VALUES
(1, 'DEPOSIT', 2000.00, 'Initial deposit', 'COMPLETED', NOW(), 1);

-- Completed Withdrawal (already approved)
INSERT INTO transactions (account_id, transaction_type, amount, description, status, processed_at, processed_by) VALUES
(2, 'WITHDRAWAL', 200.00, 'Cash withdrawal', 'COMPLETED', NOW(), 1);

-- Rejected Transaction
INSERT INTO transactions (account_id, transaction_type, amount, description, status, processed_at, processed_by) VALUES
(3, 'WITHDRAWAL', 15000.00, 'Large withdrawal request', 'REJECTED', NOW(), 2);

-- Insert Approval Records for Completed/Rejected Transactions
INSERT INTO transaction_approvals (transaction_id, admin_id, action, notes) VALUES
(5, 1, 'APPROVED', 'Verified deposit slip'),
(6, 1, 'APPROVED', 'Standard withdrawal'),
(7, 2, 'REJECTED', 'Amount exceeds daily withdrawal limit');
