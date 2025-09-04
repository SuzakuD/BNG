# Single-Page Admin Dashboard - Fishing Store

## Overview
This is a modern, single-page admin dashboard that handles all CRUD operations dynamically without page reloads. It uses AJAX/Fetch API to interact with PHP backend scripts.

## Features
- **Single Page Application**: All operations happen on one page without navigation
- **Dynamic Updates**: Real-time data updates using AJAX
- **Responsive Design**: Modern UI that works on all devices
- **CRUD Operations**: Full Create, Read, Update, Delete functionality
- **Dashboard Statistics**: Overview of store performance

## Files
- `admin_dashboard.php` - Main admin interface
- `admin_api.php` - Backend API for all operations
- `db_connect.php` - Database connection (PDO)
- `update_database.sql` - Database structure updates

## Database Structure
The system works with these main tables:

### Products Table
- id (Primary Key)
- name (Product name)
- description (Product description)
- price (Product price)
- stock (Available stock)
- image (Image filename)
- created_at (Creation timestamp)

### Users Table
- id (Primary Key)
- username (Unique username)
- password (Hashed password)
- email (User email)
- created_at (Creation timestamp)

### Orders Table
- id (Primary Key)
- user_id (Foreign key to users)
- total (Order total)
- grand_total (Final total after discounts)
- status (Order status)
- created_at (Creation timestamp)

## Setup Instructions

1. **Database Setup**:
   ```sql
   -- Run the update_database.sql file to ensure proper structure
   mysql -u root -p fishing_store < update_database.sql
   ```

2. **Access Admin Dashboard**:
   - Navigate to `admin_dashboard.php` in your browser
   - The system will automatically load all data

3. **Features Available**:
   - **Dashboard**: View statistics and recent orders
   - **Products**: Add, edit, delete products
   - **Users**: Manage user accounts
   - **Orders**: View order history and details

## API Endpoints

### Dashboard
- `GET admin_api.php?action=getStats` - Get dashboard statistics

### Products
- `GET admin_api.php?action=getProducts` - Get all products
- `POST admin_api.php` - Add/Update product (action: addProduct/updateProduct)
- `POST admin_api.php` - Delete product (action: deleteProduct)

### Users
- `GET admin_api.php?action=getUsers` - Get all users
- `POST admin_api.php` - Add/Update user (action: addUser/updateUser)
- `POST admin_api.php` - Delete user (action: deleteUser)

### Orders
- `GET admin_api.php?action=getOrders` - Get all orders

## Security Features
- Input sanitization
- Password hashing using PHP's password_hash()
- Prepared statements to prevent SQL injection
- CORS headers for API access

## Browser Compatibility
- Modern browsers with ES6+ support
- Responsive design for mobile devices
- Font Awesome icons for better UX

## Usage Examples

### Adding a Product
1. Click "Add Product" button
2. Fill in product details
3. Click "Save" - data is sent via AJAX
4. Product list updates automatically

### Editing a Product
1. Click "Edit" button on any product
2. Modal opens with pre-filled data
3. Make changes and save
4. Changes reflect immediately

### Deleting Items
1. Click "Delete" button
2. Confirm deletion
3. Item is removed and list updates

## Technical Details
- **Frontend**: Vanilla JavaScript with Fetch API
- **Backend**: PHP with PDO database access
- **Database**: MySQL/MariaDB
- **Styling**: CSS3 with modern features
- **Icons**: Font Awesome 6.0

## Troubleshooting
- Ensure database connection is working
- Check browser console for JavaScript errors
- Verify PHP has PDO extension enabled
- Make sure all files have proper permissions
