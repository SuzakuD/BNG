<?php
require_once 'config/database.php';

// Check if database exists, if not initialize it
if (!file_exists(__DIR__ . '/data/app.db')) {
    include 'data/init_db.php';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fishing Equipment Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="public/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Top Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#" onclick="loadPage('home')">
                <i class="fas fa-fish"></i> Fishing Equipment Store
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="loadPage('home')">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="showCart()">
                            <i class="fas fa-shopping-cart"></i> Cart 
                            <span id="cart-count" class="badge bg-danger">0</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="loadPage('contact')">Contact</a>
                    </li>
                </ul>
                
                <ul class="navbar-nav" id="auth-nav">
                    <!-- Authentication menu will be loaded here -->
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Left Sidebar -->
            <div class="col-md-3 bg-light p-3">
                <!-- Search Box -->
                <div class="mb-4">
                    <h5>Search Products</h5>
                    <div class="input-group">
                        <input type="text" id="search-input" class="form-control" placeholder="Search...">
                        <button class="btn btn-outline-secondary" onclick="searchProducts()">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>

                <!-- Categories -->
                <div class="mb-4">
                    <h5>Categories</h5>
                    <div id="categories-list">
                        <!-- Categories will be loaded here -->
                    </div>
                </div>

                <!-- Admin Panel (only visible for admin users) -->
                <div id="admin-panel" class="mb-4" style="display: none;">
                    <h5 class="text-danger">Admin Panel</h5>
                    <div class="list-group">
                        <button class="list-group-item list-group-item-action" onclick="showAdminTab('products')">
                            <i class="fas fa-box"></i> Manage Products
                        </button>
                        <button class="list-group-item list-group-item-action" onclick="showAdminTab('categories')">
                            <i class="fas fa-tags"></i> Manage Categories
                        </button>
                        <button class="list-group-item list-group-item-action" onclick="showAdminTab('users')">
                            <i class="fas fa-users"></i> Manage Users
                        </button>
                        <button class="list-group-item list-group-item-action" onclick="showAdminTab('orders')">
                            <i class="fas fa-shopping-bag"></i> Manage Orders
                        </button>
                        <button class="list-group-item list-group-item-action" onclick="showAdminTab('promotions')">
                            <i class="fas fa-percent"></i> Manage Promotions
                        </button>
                        <button class="list-group-item list-group-item-action" onclick="showAdminTab('reports')">
                            <i class="fas fa-chart-bar"></i> Reports
                        </button>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="col-md-9">
                <div id="main-content">
                    <!-- Main content will be loaded here -->
                </div>

                <!-- Admin Content Area -->
                <div id="admin-content" style="display: none;">
                    <!-- Admin content will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Login</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="login-form">
                        <div class="mb-3">
                            <label for="login-username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="login-username" required>
                        </div>
                        <div class="mb-3">
                            <label for="login-password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="login-password" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Sign in</button>
                    </form>
                    <div id="login-message" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Register Modal -->
    <div class="modal fade" id="registerModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Register</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="register-form">
                        <div class="mb-3">
                            <label for="register-username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="register-username" required>
                        </div>
                        <div class="mb-3">
                            <label for="register-password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="register-password" required>
                        </div>
                        <div class="mb-3">
                            <label for="register-confirm-password" class="form-label">Confirm password</label>
                            <input type="password" class="form-control" id="register-confirm-password" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Create account</button>
                    </form>
                    <div id="register-message" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cart Modal -->
    <div class="modal fade" id="cartModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Shopping Cart</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="cart-items">
                        <!-- Cart items will be loaded here -->
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <h5>Total: <span id="cart-total">0</span> THB</h5>
                        <button class="btn btn-success" onclick="showCheckout()" id="checkout-btn">Checkout</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Checkout Modal -->
    <div class="modal fade" id="checkoutModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Checkout</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="checkout-content">
                        <!-- Checkout content will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Confirmation Modal -->
    <div class="modal fade" id="orderConfirmModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Order Confirmation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="order-confirmation-content">
                        <!-- Order confirmation content will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Receipt Modal -->
    <div class="modal fade" id="receiptModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Receipt</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="receipt-content">
                        <!-- Receipt content will be loaded here -->
                    </div>
                    <div class="text-center mt-3">
                        <button class="btn btn-primary" onclick="printReceipt()">
                            <i class="fas fa-print"></i> Print receipt
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="public/js/app.js"></script>
</body>
</html>