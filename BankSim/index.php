<?php
require_once __DIR__ . '/config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .landing-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 3rem;
            max-width: 600px;
            width: 100%;
        }
        .landing-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 1rem;
        }
        .landing-subtitle {
            color: #666;
            margin-bottom: 2rem;
        }
        .portal-btn {
            padding: 1rem 2rem;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s;
            border: none;
            width: 100%;
            margin-bottom: 1rem;
        }
        .portal-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        .admin-btn {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: white;
        }
        .client-btn {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }
        .feature-list {
            text-align: left;
            margin-top: 2rem;
        }
        .feature-item {
            padding: 0.5rem 0;
            color: #555;
        }
        .feature-item i {
            color: #667eea;
            margin-right: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="landing-card">
                    <div class="text-center">
                        <i class="bi bi-bank2" style="font-size: 4rem; color: #667eea;"></i>
                        <h1 class="landing-title">Bank Simulation System</h1>
                        <p class="landing-subtitle">Secure Banking Management Platform</p>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <a href="/BankSim/admin/login.php" class="btn portal-btn admin-btn">
                                <i class="bi bi-shield-lock"></i> Admin Portal
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="/BankSim/client/login.php" class="btn portal-btn client-btn">
                                <i class="bi bi-person-circle"></i> Client Portal
                            </a>
                        </div>
                    </div>
                    
                    <div class="feature-list">
                        <h5 class="mb-3">Features:</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="feature-item">
                                    <i class="bi bi-check-circle-fill"></i> Secure Authentication
                                </div>
                                <div class="feature-item">
                                    <i class="bi bi-check-circle-fill"></i> Transaction Management
                                </div>
                                <div class="feature-item">
                                    <i class="bi bi-check-circle-fill"></i> Internal Transfers
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="feature-item">
                                    <i class="bi bi-check-circle-fill"></i> External Transfers
                                </div>
                                <div class="feature-item">
                                    <i class="bi bi-check-circle-fill"></i> Approval Workflow
                                </div>
                                <div class="feature-item">
                                    <i class="bi bi-check-circle-fill"></i> Real-time Balance Updates
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
