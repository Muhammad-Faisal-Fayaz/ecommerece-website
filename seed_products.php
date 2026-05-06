<?php
// Temporary product seeding script
session_start();
require_once 'includes/db.php';
require_once 'includes/unsplash.php';

$categories = ['Electronics', 'Clothing', 'Accessories', 'Kitchen', 'Sports', 'Books', 'Home', 'Other'];

// Product data for each category
$products = [
    'Electronics' => [
        ['Wireless Headphones', 'High-quality Bluetooth headphones with noise cancellation', 79.99, 50],
        ['USB-C Cable', 'Fast charging USB-C cable, 2 pack', 12.99, 100],
        ['Phone Stand', 'Adjustable phone stand for desk', 9.99, 75],
        ['Portable Charger', '20000mAh portable power bank with fast charging', 34.99, 40],
        ['Laptop Stand', 'Ergonomic aluminum laptop stand', 39.99, 30],
        ['Wireless Mouse', 'Smooth scrolling wireless mouse, 2.4GHz', 19.99, 60],
        ['Screen Protector', 'Tempered glass screen protector pack of 3', 14.99, 80],
        ['Charging Dock', 'Multi-device charging dock station', 49.99, 25],
        ['HDMI Cable', '4K HDMI cable 10ft', 11.99, 90],
        ['Webcam HD', '1080p HD webcam with microphone', 44.99, 35],
        ['USB Hub', '7-port USB 3.0 hub', 24.99, 50],
        ['Keyboard', 'Mechanical gaming keyboard RGB backlit', 89.99, 28],
        ['Screen Cleaner', 'Electronic device screen cleaner spray', 8.99, 100],
        ['Phone Case', 'Protective phone case silicone', 14.99, 120],
        ['Cable Organizer', 'Cable management organizer set', 9.99, 110],
        ['SSD 256GB', 'External SSD 256GB portable', 59.99, 45],
        ['Monitor Light', 'Monitor light bar USB powered', 64.99, 20],
        ['Cooling Pad', 'Laptop cooling pad with USB fans', 29.99, 35],
        ['Card Reader', 'Multi-card USB 3.0 reader', 15.99, 70],
        ['Stylus Pen', 'Digital stylus pen for tablets', 24.99, 55],
        ['Webcam Mount', 'Adjustable webcam mounting bracket', 12.99, 85],
        ['Surge Protector', 'Power strip surge protector 6 outlet', 22.99, 40],
    ],
    'Clothing' => [
        ['Cotton T-Shirt', 'Classic cotton t-shirt comfortable fit', 14.99, 80],
        ['Jeans', 'Dark blue slim fit jeans', 49.99, 50],
        ['Casual Shirt', 'Button-up casual shirt', 34.99, 40],
        ['Hoodie', 'Comfortable fleece hoodie', 39.99, 45],
        ['Shorts', 'Athletic shorts breathable fabric', 24.99, 60],
        ['Polo Shirt', 'Classic polo shirt multiple colors', 29.99, 55],
        ['Jacket', 'Winter windproof jacket', 69.99, 30],
        ['Sweater', 'Cozy knit sweater pullover', 44.99, 35],
        ['Vest', 'Casual denim vest', 39.99, 25],
        ['Cargo Pants', 'Durable cargo pants with pockets', 54.99, 28],
        ['Tank Top', 'Sleeveless tank top', 12.99, 100],
        ['Cardigan', 'Button-up cardigan sweater', 49.99, 32],
        ['Dress Shirt', 'Formal white dress shirt', 39.99, 40],
        ['Chinos', 'Classic chino pants', 44.99, 35],
        ['Henley Shirt', 'Long sleeve henley shirt', 24.99, 50],
        ['Swim Trunks', 'Quick-dry swim shorts', 29.99, 45],
        ['Thermal Shirt', 'Thermal base layer shirt', 19.99, 70],
        ['Blouse', 'Elegant women blouse', 34.99, 40],
        ['Leggings', 'High-waist yoga leggings', 39.99, 60],
        ['Skirt', 'Casual midi skirt', 34.99, 45],
        ['Jumpsuit', 'Casual one-piece jumpsuit', 54.99, 30],
        ['Blazer', 'Professional blazer jacket', 74.99, 25],
    ],
    'Accessories' => [
        ['Leather Belt', 'Classic leather belt brown', 24.99, 50],
        ['Wrist Watch', 'Casual analog wrist watch', 39.99, 40],
        ['Sunglasses', 'UV protection sunglasses', 34.99, 60],
        ['Scarf', 'Soft cotton scarf', 14.99, 80],
        ['Beanie', 'Warm winter beanie hat', 12.99, 100],
        ['Wallet', 'Leather bifold wallet', 29.99, 70],
        ['Backpack', 'Durable travel backpack', 49.99, 45],
        ['Messenger Bag', 'Canvas messenger bag', 44.99, 35],
        ['Briefcase', 'Professional leather briefcase', 84.99, 20],
        ['Crossbody Bag', 'Casual crossbody shoulder bag', 39.99, 50],
        ['Tote Bag', 'Large canvas tote bag', 34.99, 60],
        ['Clutch', 'Evening clutch purse', 29.99, 40],
        ['Gloves', 'Warm leather gloves', 19.99, 75],
        ['Socks', 'Comfortable cotton socks pack 6', 11.99, 120],
        ['Tie', 'Silk necktie classic colors', 19.99, 50],
        ['Scarf', 'Silk decorative scarf', 24.99, 45],
        ['Hat', 'Baseball cap cotton', 14.99, 90],
        ['Earmuffs', 'Plush earmuffs warm', 16.99, 65],
        ['Suspenders', 'Adjustable suspenders', 14.99, 60],
        ['Handkerchief', 'Cotton handkerchief', 7.99, 100],
        ['Keychain', 'Metal keychain with holder', 9.99, 150],
        ['Umbrella', 'Automatic compact umbrella', 22.99, 55],
    ],
    'Kitchen' => [
        ['Cutting Board', 'Bamboo cutting board large', 19.99, 50],
        ['Chef Knife', 'Stainless steel chef knife 8inch', 39.99, 35],
        ['Mixing Bowls', 'Ceramic mixing bowls set 3', 24.99, 45],
        ['Cookware Set', 'Non-stick cookware set 10 piece', 79.99, 20],
        ['Blender', 'High-speed blender 2L', 59.99, 25],
        ['Toaster', 'Stainless steel toaster 2-slice', 34.99, 40],
        ['Coffee Maker', 'Programmable coffee maker 12-cup', 44.99, 30],
        ['Microwave', 'Compact microwave oven', 69.99, 15],
        ['Food Storage', 'Glass container set with lids', 19.99, 60],
        ['Utensil Set', 'Stainless steel utensil set', 14.99, 80],
        ['Baking Sheet', 'Aluminum baking sheet pan', 9.99, 100],
        ['Rolling Pin', 'Marble rolling pin with handles', 12.99, 70],
        ['Measuring Cups', 'Plastic measuring cups set', 7.99, 110],
        ['Strainer', 'Stainless steel mesh strainer', 8.99, 90],
        ['Can Opener', 'Electric can opener', 16.99, 50],
        ['Vegetable Peeler', 'Sharp vegetable peeler', 5.99, 120],
        ['Whisk', 'Stainless steel whisk', 6.99, 100],
        ['Spatula', 'Silicone spatula heat resistant', 7.99, 95],
        ['Ladle', 'Stainless steel ladle', 8.99, 85],
        ['Tongs', 'Locking kitchen tongs', 9.99, 90],
        ['Colander', 'Stainless steel colander', 14.99, 65],
        ['Mortar and Pestle', 'Granite mortar and pestle', 19.99, 45],
    ],
    'Sports' => [
        ['Yoga Mat', 'Non-slip yoga mat 6mm', 19.99, 60],
        ['Dumbbells', 'Adjustable dumbbells 5-50 lbs', 89.99, 25],
        ['Resistance Bands', 'Resistance band loop set 5', 14.99, 80],
        ['Exercise Ball', 'Stability ball 65cm', 24.99, 40],
        ['Running Shoes', 'Cushioned running sneakers', 79.99, 35],
        ['Gym Bag', 'Durable gym bag with pockets', 34.99, 50],
        ['Water Bottle', 'Insulated water bottle 32oz', 24.99, 100],
        ['Jump Rope', 'Speed jump rope adjustable', 12.99, 70],
        ['Kettlebell', 'Cast iron kettlebell 25lbs', 34.99, 45],
        ['Pull-Up Bar', 'Doorway pull-up bar', 39.99, 30],
        ['Yoga Blocks', 'Cork yoga block set 2', 14.99, 75],
        ['Foam Roller', 'Muscle foam roller 12inch', 19.99, 55],
        ['Hand Weights', 'Neoprene hand weights pair', 24.99, 65],
        ['Basketball', 'Official size basketball', 29.99, 40],
        ['Soccer Ball', 'Professional soccer ball', 24.99, 50],
        ['Tennis Racket', 'Aluminum tennis racket', 44.99, 25],
        ['Baseball Glove', 'Leather baseball glove', 34.99, 35],
        ['Skateboard', 'Complete skateboard', 49.99, 20],
        ['Bicycle Helmet', 'Safety bicycle helmet', 39.99, 45],
        ['Knee Pads', 'Protective knee pads pair', 14.99, 80],
        ['Elbow Pads', 'Protective elbow pads pair', 12.99, 85],
        ['Shin Guards', 'Soccer shin guards', 16.99, 70],
    ],
    'Books' => [
        ['Fiction Novel', 'Bestselling fiction novel hardcover', 24.99, 50],
        ['Mystery Thriller', 'Page-turning mystery novel', 16.99, 70],
        ['Science Fiction', 'Epic science fiction adventure', 19.99, 60],
        ['Fantasy Epic', 'Epic fantasy novel series', 26.99, 45],
        ['Romance Novel', 'Contemporary romance', 14.99, 80],
        ['Biography', 'Inspiring biography memoir', 22.99, 35],
        ['Self-Help', 'Personal development guide', 18.99, 55],
        ['Business Book', 'Business strategy guide', 21.99, 40],
        ['History', 'World history comprehensive', 27.99, 30],
        ['Poetry', 'Poetry collection anthology', 14.99, 50],
        ['Cookbook', 'Healthy recipes cookbook', 19.99, 60],
        ['Travel Guide', 'Travel guide companion', 17.99, 45],
        ['Art Book', 'Contemporary art book', 29.99, 25],
        ['Photography', 'Photography techniques guide', 24.99, 35],
        ['Children Book', 'Popular children\'s book', 12.99, 100],
        ['Young Adult', 'Young adult adventure', 15.99, 75],
        ['Graphic Novel', 'Graphic novel comic', 18.99, 55],
        ['Technical', 'Programming technical guide', 44.99, 20],
        ['Educational', 'Educational reference book', 16.99, 60],
        ['Journal', 'Daily journal notebook', 9.99, 120],
        ['Puzzle Book', 'Puzzle and game book', 11.99, 90],
        ['Dictionary', 'Comprehensive dictionary', 19.99, 40],
    ],
    'Home' => [
        ['Bed Sheet Set', 'Cotton bed sheet set queen', 39.99, 40],
        ['Pillow', 'Memory foam pillow', 34.99, 45],
        ['Blanket', 'Cozy fleece blanket', 24.99, 60],
        ['Lamp', 'LED desk lamp adjustable', 29.99, 50],
        ['Curtain', 'Blackout curtain panel', 19.99, 70],
        ['Rug', 'Decorative area rug', 49.99, 30],
        ['Towel Set', 'Cotton towel set 3 piece', 24.99, 50],
        ['Bath Mat', 'Non-slip bath mat', 12.99, 80],
        ['Shower Curtain', 'Waterproof shower curtain', 14.99, 75],
        ['Mirror', 'Wall mounted mirror', 19.99, 45],
        ['Picture Frame', 'Photo frame 8x10', 9.99, 100],
        ['Candle', 'Scented candle jar', 12.99, 90],
        ['Plant Pot', 'Ceramic flower pot', 14.99, 65],
        ['Couch Pillow', 'Decorative throw pillow', 16.99, 60],
        ['Door Mat', 'Welcome door mat', 9.99, 85],
        ['Wall Clock', 'Modern wall clock', 22.99, 50],
        ['Magazine Holder', 'Magazine rack organizer', 14.99, 60],
        ['Shelf', 'Floating shelf white', 24.99, 40],
        ['Desk Organizer', 'Desk organization set', 18.99, 55],
        ['Room Divider', 'Decorative room divider', 34.99, 25],
        ['Air Freshener', 'Room air freshener spray', 7.99, 150],
        ['Doorbell', 'Wireless video doorbell', 49.99, 20],
    ],
    'Other' => [
        ['Notebook', 'Blank notebook journal', 8.99, 100],
        ['Pen Set', 'Ballpoint pen set 10', 9.99, 120],
        ['Pencil', 'Pencil set HB', 5.99, 150],
        ['Eraser', 'Rubber eraser pack', 4.99, 200],
        ['Sticky Notes', 'Sticky note pads', 6.99, 130],
        ['Tape', 'Scotch tape roll', 3.99, 200],
        ['Scissors', 'General purpose scissors', 7.99, 90],
        ['Glue', 'Craft glue bottle', 4.99, 150],
        ['Stapler', 'Desktop stapler', 9.99, 80],
        ['Staples', 'Staples box 5000', 5.99, 100],
        ['Paper Clips', 'Paper clips pack', 3.99, 200],
        ['Ruler', 'Plastic ruler 12inch', 2.99, 300],
        ['Calculator', 'Basic calculator', 9.99, 70],
        ['Magnifying Glass', 'Magnifying glass 3x', 7.99, 60],
        ['Flashlight', 'LED flashlight', 12.99, 80],
        ['First Aid Kit', 'Home first aid kit', 19.99, 50],
        ['Cleaning Cloth', 'Microfiber cleaning cloth', 4.99, 180],
        ['Organizer Box', 'Storage organizer box', 14.99, 70],
        ['Folder', 'File folder set', 6.99, 150],
        ['Binder', 'Plastic binder 3-ring', 7.99, 100],
        ['Highlighter', 'Highlighter marker set', 5.99, 140],
        ['Dry Erase Marker', 'Whiteboard marker pack', 6.99, 120],
    ]
];

$totalInserted = 0;

foreach ($categories as $category) {
    if (!isset($products[$category])) continue;
    
    foreach ($products[$category] as $product) {
        $stmt = $pdo->prepare("
            INSERT INTO products (name, description, price, stock, category, image) 
            VALUES (?, ?, ?, ?, ?, 'default.jpg')
        ");
        
        $stmt->execute([
            $product[0],           // name
            $product[1],           // description
            $product[2],           // price
            $product[3],           // stock
            $category              // category
        ]);
        
        $totalInserted++;
    }
}

echo "<h2> Successfully inserted $totalInserted products into the database!</h2>";
echo "<p style='background:#e8f5e9;padding:12px;border-radius:4px;margin:16px 0;border-left:4px solid #4caf50;'>";
echo "✓ <strong>Unsplash Images Enabled:</strong> All products now display beautiful images from Unsplash based on their category and name. ";
echo "Images are loaded dynamically without storing them locally.";
echo "</p>";
echo "<p>Added 20+ products to each of the 8 categories:</p>";
echo "<ul>";
foreach ($categories as $cat) {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM products WHERE category = ?");
    $stmt->execute([$cat]);
    $result = $stmt->fetch();
    echo "<li><strong>$cat:</strong> " . $result['count'] . " products</li>";
}
echo "</ul>";
echo "<p><a href='index.php'>← Back to Store</a></p>";
?>
