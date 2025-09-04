<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Fishing Store</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 1rem 2rem; }
        .nav-tabs { background: white; padding: 0 2rem; border-bottom: 1px solid #e0e0e0; display: flex; }
        .nav-tab { padding: 1rem 2rem; background: none; border: none; cursor: pointer; color: #666; border-bottom: 3px solid transparent; }
        .nav-tab.active { color: #667eea; border-bottom-color: #667eea; background: #f8f9ff; }
        .content { padding: 2rem; max-width: 1400px; margin: 0 auto; }
        .section { display: none; }
        .section.active { display: block; }
        .btn { padding: 0.75rem 1.5rem; border: none; border-radius: 5px; cursor: pointer; margin: 0.25rem; }
        .btn-primary { background: #667eea; color: white; }
        .btn-success { background: #28a745; color: white; }
        .btn-warning { background: #ffc107; color: #212529; }
        .btn-danger { background: #dc3545; color: white; }
        .table { width: 100%; border-collapse: collapse; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .table th, .table td { padding: 1rem; text-align: left; border-bottom: 1px solid #e0e0e0; }
        .table th { background: #f8f9fa; font-weight: 600; }
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); }
        .modal-content { background-color: white; margin: 5% auto; padding: 2rem; border-radius: 10px; width: 90%; max-width: 600px; }
        .form-group { margin-bottom: 1rem; }
        .form-label { display: block; margin-bottom: 0.5rem; font-weight: 500; }
        .form-control { width: 100%; padding: 0.75rem; border: 1px solid #ced4da; border-radius: 5px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
        .stat-card { background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center; }
        .stat-number { font-size: 2rem; font-weight: bold; color: #667eea; }
    </style>
</head>
<body>
    <div class="header">
        <h1><i class="fas fa-fish"></i> Fishing Store Admin</h1>
        <p>Single-page admin dashboard</p>
    </div>

    <div class="nav-tabs">
        <button class="nav-tab active" onclick="showSection('dashboard')">Dashboard</button>
        <button class="nav-tab" onclick="showSection('products')">Products</button>
        <button class="nav-tab" onclick="showSection('users')">Users</button>
        <button class="nav-tab" onclick="showSection('orders')">Orders</button>
    </div>

    <div class="content">
        <div id="dashboard" class="section active">
            <h2>Dashboard Overview</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number" id="totalProducts">-</div>
                    <div>Total Products</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="totalUsers">-</div>
                    <div>Total Users</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="totalOrders">-</div>
                    <div>Total Orders</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="totalRevenue">-</div>
                    <div>Total Revenue</div>
                </div>
            </div>
            <div id="recentOrders"></div>
        </div>

        <div id="products" class="section">
            <h2>Products Management</h2>
            <button class="btn btn-primary" onclick="showProductModal()">Add Product</button>
            <div id="productsTable"></div>
        </div>

        <div id="users" class="section">
            <h2>Users Management</h2>
            <button class="btn btn-primary" onclick="showUserModal()">Add User</button>
            <div id="usersTable"></div>
        </div>

        <div id="orders" class="section">
            <h2>Orders Management</h2>
            <div id="ordersTable"></div>
        </div>
    </div>

    <div id="productModal" class="modal">
        <div class="modal-content">
            <h3 id="productModalTitle">Add Product</h3>
            <form id="productForm">
                <input type="hidden" id="productId">
                <div class="form-group">
                    <label class="form-label">Name:</label>
                    <input type="text" id="productName" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Price:</label>
                    <input type="number" id="productPrice" class="form-control" step="0.01" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Stock:</label>
                    <input type="number" id="productStock" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Description:</label>
                    <textarea id="productDescription" class="form-control" rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('productModal')">Cancel</button>
            </form>
        </div>
    </div>

    <div id="userModal" class="modal">
        <div class="modal-content">
            <h3 id="userModalTitle">Add User</h3>
            <form id="userForm">
                <input type="hidden" id="userId">
                <div class="form-group">
                    <label class="form-label">Username:</label>
                    <input type="text" id="username" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email:</label>
                    <input type="email" id="userEmail" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Password:</label>
                    <input type="password" id="userPassword" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('userModal')">Cancel</button>
            </form>
        </div>
    </div>

    <script>
        let currentSection = 'dashboard';
        let products = [];
        let users = [];
        let orders = [];

        document.addEventListener('DOMContentLoaded', function() {
            loadDashboard();
            loadProducts();
            loadUsers();
            loadOrders();
        });

        function showSection(sectionName) {
            document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
            document.querySelectorAll('.nav-tab').forEach(t => t.classList.remove('active'));
            document.getElementById(sectionName).classList.add('active');
            event.target.classList.add('active');
            currentSection = sectionName;
        }

        async function loadDashboard() {
            try {
                const response = await fetch('admin_api.php?action=getStats');
                const data = await response.json();
                if (data.success) {
                    document.getElementById('totalProducts').textContent = data.stats.totalProducts;
                    document.getElementById('totalUsers').textContent = data.stats.totalUsers;
                    document.getElementById('totalOrders').textContent = data.stats.totalOrders;
                    document.getElementById('totalRevenue').textContent = '฿' + data.stats.totalRevenue.toLocaleString();
                    displayRecentOrders(data.recentOrders);
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        async function loadProducts() {
            try {
                const response = await fetch('admin_api.php?action=getProducts');
                const data = await response.json();
                if (data.success) {
                    products = data.products;
                    displayProducts();
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        async function loadUsers() {
            try {
                const response = await fetch('admin_api.php?action=getUsers');
                const data = await response.json();
                if (data.success) {
                    users = data.users;
                    displayUsers();
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        async function loadOrders() {
            try {
                const response = await fetch('admin_api.php?action=getOrders');
                const data = await response.json();
                if (data.success) {
                    orders = data.orders;
                    displayOrders();
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        function displayProducts() {
            const container = document.getElementById('productsTable');
            if (!products.length) {
                container.innerHTML = '<p>No products found</p>';
                return;
            }

            let html = '<table class="table"><thead><tr><th>ID</th><th>Name</th><th>Price</th><th>Stock</th><th>Actions</th></tr></thead><tbody>';
            products.forEach(product => {
                html += `<tr>
                    <td>${product.id}</td>
                    <td>${product.name}</td>
                    <td>฿${product.price}</td>
                    <td>${product.stock}</td>
                    <td>
                        <button class="btn btn-warning" onclick="editProduct(${product.id})">Edit</button>
                        <button class="btn btn-danger" onclick="deleteProduct(${product.id})">Delete</button>
                    </td>
                </tr>`;
            });
            html += '</tbody></table>';
            container.innerHTML = html;
        }

        function displayUsers() {
            const container = document.getElementById('usersTable');
            if (!users.length) {
                container.innerHTML = '<p>No users found</p>';
                return;
            }

            let html = '<table class="table"><thead><tr><th>ID</th><th>Username</th><th>Email</th><th>Actions</th></tr></thead><tbody>';
            users.forEach(user => {
                html += `<tr>
                    <td>${user.id}</td>
                    <td>${user.username}</td>
                    <td>${user.email || 'N/A'}</td>
                    <td>
                        <button class="btn btn-warning" onclick="editUser(${user.id})">Edit</button>
                        <button class="btn btn-danger" onclick="deleteUser(${user.id})">Delete</button>
                    </td>
                </tr>`;
            });
            html += '</tbody></table>';
            container.innerHTML = html;
        }

        function displayOrders() {
            const container = document.getElementById('ordersTable');
            if (!orders.length) {
                container.innerHTML = '<p>No orders found</p>';
                return;
            }

            let html = '<table class="table"><thead><tr><th>ID</th><th>Customer</th><th>Total</th><th>Status</th><th>Date</th></tr></thead><tbody>';
            orders.forEach(order => {
                html += `<tr>
                    <td>#${order.id}</td>
                    <td>${order.username}</td>
                    <td>฿${order.grand_total}</td>
                    <td>${order.status}</td>
                    <td>${new Date(order.created_at).toLocaleDateString()}</td>
                </tr>`;
            });
            html += '</tbody></table>';
            container.innerHTML = html;
        }

        function displayRecentOrders(orders) {
            const container = document.getElementById('recentOrders');
            if (!orders || !orders.length) {
                container.innerHTML = '<p>No recent orders</p>';
                return;
            }

            let html = '<h3>Recent Orders</h3><table class="table"><thead><tr><th>ID</th><th>Customer</th><th>Total</th><th>Status</th></tr></thead><tbody>';
            orders.forEach(order => {
                html += `<tr>
                    <td>#${order.id}</td>
                    <td>${order.username}</td>
                    <td>฿${order.grand_total}</td>
                    <td>${order.status}</td>
                </tr>`;
            });
            html += '</tbody></table>';
            container.innerHTML = html;
        }

        function showProductModal(productId = null) {
            const modal = document.getElementById('productModal');
            const title = document.getElementById('productModalTitle');
            
            if (productId) {
                const product = products.find(p => p.id == productId);
                if (product) {
                    title.textContent = 'Edit Product';
                    document.getElementById('productId').value = product.id;
                    document.getElementById('productName').value = product.name;
                    document.getElementById('productPrice').value = product.price;
                    document.getElementById('productStock').value = product.stock;
                    document.getElementById('productDescription').value = product.description || '';
                }
            } else {
                title.textContent = 'Add Product';
                document.getElementById('productForm').reset();
                document.getElementById('productId').value = '';
            }
            modal.style.display = 'block';
        }

        function showUserModal(userId = null) {
            const modal = document.getElementById('userModal');
            const title = document.getElementById('userModalTitle');
            
            if (userId) {
                const user = users.find(u => u.id == userId);
                if (user) {
                    title.textContent = 'Edit User';
                    document.getElementById('userId').value = user.id;
                    document.getElementById('username').value = user.username;
                    document.getElementById('userEmail').value = user.email || '';
                    document.getElementById('userPassword').value = '';
                }
            } else {
                title.textContent = 'Add User';
                document.getElementById('userForm').reset();
                document.getElementById('userId').value = '';
            }
            modal.style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        function editProduct(id) {
            showProductModal(id);
        }

        function editUser(id) {
            showUserModal(id);
        }

        async function deleteProduct(id) {
            if (confirm('Delete this product?')) {
                try {
                    const response = await fetch('admin_api.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        body: `action=deleteProduct&id=${id}`
                    });
                    const data = await response.json();
                    if (data.success) {
                        loadProducts();
                        loadDashboard();
                    }
                } catch (error) {
                    console.error('Error:', error);
                }
            }
        }

        async function deleteUser(id) {
            if (confirm('Delete this user?')) {
                try {
                    const response = await fetch('admin_api.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        body: `action=deleteUser&id=${id}`
                    });
                    const data = await response.json();
                    if (data.success) {
                        loadUsers();
                        loadDashboard();
                    }
                } catch (error) {
                    console.error('Error:', error);
                }
            }
        }

        document.getElementById('productForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const productId = document.getElementById('productId').value;
            formData.append('action', productId ? 'updateProduct' : 'addProduct');
            
            try {
                const response = await fetch('admin_api.php', {method: 'POST', body: formData});
                const data = await response.json();
                if (data.success) {
                    closeModal('productModal');
                    loadProducts();
                    loadDashboard();
                }
            } catch (error) {
                console.error('Error:', error);
            }
        });

        document.getElementById('userForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const userId = document.getElementById('userId').value;
            formData.append('action', userId ? 'updateUser' : 'addUser');
            
            try {
                const response = await fetch('admin_api.php', {method: 'POST', body: formData});
                const data = await response.json();
                if (data.success) {
                    closeModal('userModal');
                    loadUsers();
                    loadDashboard();
                }
            } catch (error) {
                console.error('Error:', error);
            }
        });

        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>
</html>
