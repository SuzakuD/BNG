<?php
/**
 * Seed script for fishing equipment data
 */

require_once __DIR__ . '/../config/database.php';

try {
    $pdo = getDB();
    
    // Clear existing data (except users)
    $pdo->exec("DELETE FROM order_items");
    $pdo->exec("DELETE FROM orders");
    $pdo->exec("DELETE FROM receipts");
    $pdo->exec("DELETE FROM products");
    $pdo->exec("DELETE FROM categories");
    $pdo->exec("DELETE FROM promotions");
    
    // Reset auto increment
    $pdo->exec("UPDATE sqlite_sequence SET seq = 0 WHERE name IN ('categories', 'products', 'orders', 'order_items', 'receipts', 'promotions')");
    
    echo "Cleared existing data...\n";
    
    // Insert categories
    $categories = [
        'Rods',
        'Reels',
        'Lures & Baits',
        'Lines',
        'Hooks',
        'Nets',
        'Accessories',
        'Tackle Storage',
        'Apparel',
        'Safety Gear'
    ];
    
    $categoryIds = [];
    foreach ($categories as $category) {
        $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->execute([$category]);
        $categoryIds[] = $pdo->lastInsertId();
    }
    
    echo "Inserted categories...\n";
    
    // Insert products
    $products = [
        // Rods
        [
            'name' => 'Sea Fishing Rod Professional Series',
            'description' => 'Saltwater fishing rod, 2.1m length, carbon fiber material',
            'price' => 2500,
            'stock' => 15,
            'category_id' => $categoryIds[0],
            'image' => 'https://via.placeholder.com/300x200?text=Sea+Fishing+Rod'
        ],
        [
            'name' => 'Freshwater Rod Ultra Light',
            'description' => 'Lightweight freshwater rod, 1.8m length',
            'price' => 1200,
            'stock' => 25,
            'category_id' => $categoryIds[0],
            'image' => 'https://via.placeholder.com/300x200?text=Fresh+Water+Rod'
        ],
        [
            'name' => 'Carp Rod Heavy Action',
            'description' => 'Strong carp rod, 3.6m length',
            'price' => 3500,
            'stock' => 8,
            'category_id' => $categoryIds[0],
            'image' => 'https://via.placeholder.com/300x200?text=Carp+Fishing+Rod'
        ],
        
        // Reels
        [
            'name' => 'Spinning Reel Shimano FX 2500',
            'description' => 'Quality spinning reel, 5 bearings, great for beginners',
            'price' => 1800,
            'stock' => 20,
            'category_id' => $categoryIds[1],
            'image' => 'https://via.placeholder.com/300x200?text=Spinning+Reel'
        ],
        [
            'name' => 'Baitcasting Reel Daiwa Tatula CT',
            'description' => 'Pro-level baitcasting reel with magnetic braking',
            'price' => 4500,
            'stock' => 12,
            'category_id' => $categoryIds[1],
            'image' => 'https://via.placeholder.com/300x200?text=Baitcasting+Reel'
        ],
        [
            'name' => 'Saltwater Reel Penn Battle III',
            'description' => 'Saltwater-ready reel, corrosion resistant, size 4000',
            'price' => 6500,
            'stock' => 6,
            'category_id' => $categoryIds[1],
            'image' => 'https://via.placeholder.com/300x200?text=Saltwater+Reel'
        ],
        
        // Lures & Baits
        [
            'name' => 'Rapala Countdown 7cm',
            'description' => 'Sinking hard lure for seabass and predators',
            'price' => 350,
            'stock' => 50,
            'category_id' => $categoryIds[2],
            'image' => 'https://via.placeholder.com/300x200?text=Rapala+Lure'
        ],
        [
            'name' => 'Berkley PowerBait Soft Plastics',
            'description' => 'Scented soft baits ideal for trout',
            'price' => 180,
            'stock' => 80,
            'category_id' => $categoryIds[2],
            'image' => 'https://via.placeholder.com/300x200?text=Soft+Bait'
        ],
        [
            'name' => 'Yo-Zuri 3D Popper',
            'description' => 'Topwater popping lure',
            'price' => 420,
            'stock' => 30,
            'category_id' => $categoryIds[2],
            'image' => 'https://via.placeholder.com/300x200?text=Popper+Lure'
        ],
        
        // Lines
        [
            'name' => 'Seaguar Fluorocarbon 0.35mm',
            'description' => 'Clear, durable fluorocarbon, 12 lb test',
            'price' => 280,
            'stock' => 40,
            'category_id' => $categoryIds[3],
            'image' => 'https://via.placeholder.com/300x200?text=Fluorocarbon+Line'
        ],
        [
            'name' => 'PowerPro Braided Line 20lb',
            'description' => '4-strand braid, strong with small diameter',
            'price' => 650,
            'stock' => 25,
            'category_id' => $categoryIds[3],
            'image' => 'https://via.placeholder.com/300x200?text=Braided+Line'
        ],
        
        // Hooks
        [
            'name' => 'Owner 5130 Hook Size 1/0',
            'description' => 'Japanese quality hooks, sharp and strong, pack of 10',
            'price' => 120,
            'stock' => 100,
            'category_id' => $categoryIds[4],
            'image' => 'https://via.placeholder.com/300x200?text=Fishing+Hooks'
        ],
        [
            'name' => 'Fox Edges Curve Shank Carp Hooks',
            'description' => 'Curved carp hooks, pack of 10',
            'price' => 250,
            'stock' => 60,
            'category_id' => $categoryIds[4],
            'image' => 'https://via.placeholder.com/300x200?text=Carp+Hooks'
        ],
        
        // Nets
        [
            'name' => 'Foldable Landing Net 60cm',
            'description' => 'Foldable landing net, 1.5m handle',
            'price' => 450,
            'stock' => 15,
            'category_id' => $categoryIds[5],
            'image' => 'https://via.placeholder.com/300x200?text=Landing+Net'
        ],
        [
            'name' => 'Keep Net 3m',
            'description' => 'Keep net for holding caught fish, 3m length',
            'price' => 680,
            'stock' => 10,
            'category_id' => $categoryIds[5],
            'image' => 'https://via.placeholder.com/300x200?text=Keep+Net'
        ],
        
        // Accessories
        [
            'name' => 'Rod Pod 3 Legs',
            'description' => 'Adjustable 3-leg rod pod, aluminum',
            'price' => 1200,
            'stock' => 12,
            'category_id' => $categoryIds[6],
            'image' => 'https://via.placeholder.com/300x200?text=Rod+Pod'
        ],
        [
            'name' => 'Digital Fish Scale 40kg',
            'description' => 'Accurate digital scale, up to 40kg',
            'price' => 350,
            'stock' => 25,
            'category_id' => $categoryIds[6],
            'image' => 'https://via.placeholder.com/300x200?text=Digital+Scale'
        ],
        [
            'name' => 'Waterproof LED Flashlight',
            'description' => 'Bright LED flashlight, IPX6 waterproof',
            'price' => 280,
            'stock' => 35,
            'category_id' => $categoryIds[6],
            'image' => 'https://via.placeholder.com/300x200?text=LED+Flashlight'
        ],
        
        // Tackle Storage
        [
            'name' => 'Plano 3700 Tackle Box',
            'description' => 'Adjustable compartments, water resistant',
            'price' => 320,
            'stock' => 30,
            'category_id' => $categoryIds[7],
            'image' => 'https://via.placeholder.com/300x200?text=Tackle+Box'
        ],
        [
            'name' => 'Shimano Tribal Fishing Bag',
            'description' => 'Large fishing bag with many compartments, water resistant',
            'price' => 1800,
            'stock' => 8,
            'category_id' => $categoryIds[7],
            'image' => 'https://via.placeholder.com/300x200?text=Fishing+Bag'
        ],
        
        // Apparel
        [
            'name' => 'UV Protection Fishing Shirt',
            'description' => 'Long-sleeve sun protection, breathable, size L',
            'price' => 450,
            'stock' => 20,
            'category_id' => $categoryIds[8],
            'image' => 'https://via.placeholder.com/300x200?text=Fishing+Shirt'
        ],
        [
            'name' => 'Sun Protection Fishing Hat',
            'description' => 'Wide brim sun hat with chin strap',
            'price' => 250,
            'stock' => 40,
            'category_id' => $categoryIds[8],
            'image' => 'https://via.placeholder.com/300x200?text=Fishing+Hat'
        ],
        
        // Safety Gear
        [
            'name' => 'Inflatable Life Jacket',
            'description' => 'Automatic inflatable life jacket, lightweight',
            'price' => 2200,
            'stock' => 15,
            'category_id' => $categoryIds[9],
            'image' => 'https://via.placeholder.com/300x200?text=Life+Jacket'
        ],
        [
            'name' => 'Emergency Whistle',
            'description' => 'Loud waterproof whistle with lanyard',
            'price' => 80,
            'stock' => 50,
            'category_id' => $categoryIds[9],
            'image' => 'https://via.placeholder.com/300x200?text=Emergency+Whistle'
        ]
    ];
    
    foreach ($products as $product) {
        $stmt = $pdo->prepare("INSERT INTO products (name, description, price, stock, category_id, image) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $product['name'],
            $product['description'],
            $product['price'],
            $product['stock'],
            $product['category_id'],
            $product['image']
        ]);
    }
    
    echo "Inserted " . count($products) . " products...\n";
    
    // Insert sample promotions
    $promotions = [
        [
            'code' => 'NEWBIE10',
            'discount' => 10,
            'expire_date' => date('Y-m-d', strtotime('+30 days'))
        ],
        [
            'code' => 'FISHING20',
            'discount' => 20,
            'expire_date' => date('Y-m-d', strtotime('+60 days'))
        ],
        [
            'code' => 'WEEKEND15',
            'discount' => 15,
            'expire_date' => date('Y-m-d', strtotime('+14 days'))
        ]
    ];
    
    foreach ($promotions as $promo) {
        $stmt = $pdo->prepare("INSERT INTO promotions (code, discount, expire_date) VALUES (?, ?, ?)");
        $stmt->execute([$promo['code'], $promo['discount'], $promo['expire_date']]);
    }
    
    echo "Inserted " . count($promotions) . " promotions...\n";
    
    echo "Seed data inserted successfully!\n";
    echo "\nDefault admin login:\n";
    echo "Username: admin\n";
    echo "Password: admin123\n";
    echo "\nPromotion codes:\n";
    foreach ($promotions as $promo) {
        echo "- {$promo['code']}: {$promo['discount']}% off (expires: {$promo['expire_date']})\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>