-- Update database structure for admin system
-- Add missing columns if they don't exist

-- Update products table
ALTER TABLE products 
ADD COLUMN IF NOT EXISTS description TEXT DEFAULT NULL,
ADD COLUMN IF NOT EXISTS stock INT DEFAULT 0,
ADD COLUMN IF NOT EXISTS created_at DATETIME DEFAULT CURRENT_TIMESTAMP;

-- Update users table  
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS email VARCHAR(255) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS created_at DATETIME DEFAULT CURRENT_TIMESTAMP;

-- Update orders table
ALTER TABLE orders 
ADD COLUMN IF NOT EXISTS created_at DATETIME DEFAULT CURRENT_TIMESTAMP;

-- Ensure proper indexes
CREATE INDEX IF NOT EXISTS idx_products_created ON products(created_at);
CREATE INDEX IF NOT EXISTS idx_users_created ON users(created_at);
CREATE INDEX IF NOT EXISTS idx_orders_created ON orders(created_at);
