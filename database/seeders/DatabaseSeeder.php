<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Coupon;
use App\Models\Review;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Permissions ──
        $perms = [
            'products.view','products.create','products.edit','products.delete',
            'categories.view','categories.create','categories.edit','categories.delete',
            'orders.view','orders.edit','orders.delete',
            'users.view','users.create','users.edit','users.delete',
            'coupons.view','coupons.create','coupons.edit','coupons.delete',
            'reports.view','settings.manage','banners.manage','reviews.manage',
        ];
        foreach ($perms as $p) Permission::firstOrCreate(['name'=>$p,'guard_name'=>'web']);

        // ── Roles ──
        $superAdmin = Role::firstOrCreate(['name'=>'super-admin','guard_name'=>'web']);
        $superAdmin->syncPermissions(Permission::all());

        $admin = Role::firstOrCreate(['name'=>'admin','guard_name'=>'web']);
        $admin->syncPermissions(Permission::whereNotIn('name',['settings.manage','users.delete'])->get());

        $manager = Role::firstOrCreate(['name'=>'manager','guard_name'=>'web']);
        $manager->syncPermissions(['products.view','products.create','products.edit','categories.view','orders.view','orders.edit','coupons.view','reports.view','banners.manage']);

        $staff = Role::firstOrCreate(['name'=>'staff','guard_name'=>'web']);
        $staff->syncPermissions(['products.view','orders.view','orders.edit']);

        Role::firstOrCreate(['name'=>'customer','guard_name'=>'web']);

        // ── Admin Users ──
        $su = User::firstOrCreate(['email'=>'admin@shopbd.com'],[
            'name'=>'Super Admin','password'=>Hash::make('admin123'),'is_active'=>true
        ]);
        $su->syncRoles(['super-admin']);

        $mg = User::firstOrCreate(['email'=>'manager@shopbd.com'],[
            'name'=>'Shop Manager','password'=>Hash::make('manager123'),'is_active'=>true
        ]);
        $mg->syncRoles(['manager']);

        // ── Demo Customer ──
        $cu = User::firstOrCreate(['email'=>'customer@shopbd.com'],[
            'name'=>'Demo Customer','password'=>Hash::make('customer123'),'is_active'=>true
        ]);
        $cu->syncRoles(['customer']);

        // ── Categories (with sub-categories for Clothing) ──
        $catData = [
            ['name'=>'Electronics',   'emoji'=>'📱','children'=>['Smartphones','Laptops','Accessories','Audio','Smart Home']],
            ['name'=>'Clothing',      'emoji'=>'👕','children'=>["Men's Fashion","Women's Fashion","Kids' Wear","Traditional Wear","Sportswear"]],
            ['name'=>'Home & Garden', 'emoji'=>'🏠','children'=>['Furniture','Kitchen','Bedding','Garden','Lighting']],
            ['name'=>'Sports',        'emoji'=>'⚽','children'=>['Cricket','Football','Fitness','Outdoor','Swimming']],
            ['name'=>'Books',         'emoji'=>'📚','children'=>['Fiction','Non-Fiction','Textbooks','Comics','Religious']],
            ['name'=>'Beauty',        'emoji'=>'💄','children'=>['Skincare','Makeup','Hair Care','Fragrances','Men Grooming']],
            ['name'=>'Toys & Games',  'emoji'=>'🧸','children'=>['Action Figures','Board Games','Educational','Outdoor Toys','Puzzles']],
            ['name'=>'Food & Grocery','emoji'=>'🛒','children'=>['Fresh Produce','Snacks','Beverages','Organic','Dairy']],
        ];

        $categoryMap = [];
        foreach ($catData as $i => $cd) {
            $slug = Str::slug($cd['name']).'-'.Str::random(4);
            $parent = Category::firstOrCreate(['slug'=>$slug],[
                'name'=>$cd['name'],'slug'=>$slug,'description'=>$cd['name'].' category',
                'is_active'=>true,'sort_order'=>$i,
            ]);
            $categoryMap[$cd['name']] = $parent;

            foreach ($cd['children'] as $j => $child) {
                $cslug = Str::slug($child).'-'.Str::random(4);
                $sub = Category::firstOrCreate(['slug'=>$cslug],[
                    'name'=>$child,'slug'=>$cslug,'parent_id'=>$parent->id,
                    'description'=>$child.' products','is_active'=>true,'sort_order'=>$j,
                ]);
                $categoryMap[$child] = $sub;
            }
        }

        // ── 24 Products ──
        $products = [
            // Electronics
            ['name'=>'Samsung Galaxy A54 5G','cat'=>'Smartphones','price'=>42999,'sale'=>37999,'stock'=>45,'brand'=>'Samsung','desc'=>'6.4" Super AMOLED display, 50MP triple camera, 5000mAh battery. Fast 5G connectivity with Exynos 1380 processor.','short'=>'6.4" AMOLED · 50MP Camera · 5G · 5000mAh','tags'=>['smartphone','5g','samsung','android'],'featured'=>true],
            ['name'=>'Xiaomi Redmi Note 13 Pro','cat'=>'Smartphones','price'=>32999,'sale'=>28999,'stock'=>60,'brand'=>'Xiaomi','desc'=>'200MP camera with optical image stabilization. 120Hz curved AMOLED display. 67W turbo charging.','short'=>'200MP Camera · 120Hz AMOLED · 67W Fast Charge','tags'=>['xiaomi','redmi','smartphone'],'featured'=>true],
            ['name'=>'Apple AirPods Pro 2nd Gen','cat'=>'Audio','price'=>29999,'sale'=>null,'stock'=>20,'brand'=>'Apple','desc'=>'Active Noise Cancellation, Transparency Mode, Adaptive Audio. H2 chip delivers industry-leading audio performance.','short'=>'ANC · Transparency Mode · H2 Chip · MagSafe Case','tags'=>['airpods','apple','earbuds','wireless'],'featured'=>true],
            ['name'=>'Sony WH-1000XM5 Headphones','cat'=>'Audio','price'=>32499,'sale'=>27999,'stock'=>15,'brand'=>'Sony','desc'=>'Industry-leading noise canceling with two processors. 30-hour battery. Crystal clear hands-free calling.','short'=>'Best-in-class ANC · 30hr battery · Hi-Res Audio','tags'=>['sony','headphones','anc','wireless'],'featured'=>false],
            ['name'=>'Anker 65W USB-C Charger','cat'=>'Accessories','price'=>2499,'sale'=>1899,'stock'=>200,'brand'=>'Anker','desc'=>'65W fast charging for laptops, tablets and phones. GaN technology for compact design. Universal compatibility.','short'=>'65W GaN · Universal · Fast Charging','tags'=>['charger','usbc','anker','fast-charge'],'featured'=>false],

            // Clothing
            ['name'=>'Premium Cotton Polo Shirt','cat'=>"Men's Fashion",'price'=>1299,'sale'=>899,'stock'=>150,'brand'=>'StyleBD','desc'=>'100% combed cotton polo shirt. Pre-shrunk, color-fast fabric. Available in multiple colors. Perfect for casual and semi-formal occasions.','short'=>'100% Cotton · Polo Collar · Multiple Colors','tags'=>['polo','men','shirt','cotton'],'featured'=>false],
            ['name'=>'Slim Fit Chino Pants','cat'=>"Men's Fashion",'price'=>2199,'sale'=>1699,'stock'=>80,'brand'=>'FashionHouse','desc'=>'Stretch cotton chino trousers with slim fit silhouette. Wrinkle-resistant fabric. Perfect for office and casual wear.','short'=>'Stretch Cotton · Slim Fit · Wrinkle Resistant','tags'=>['chino','men','pants','office'],'featured'=>false],
            ['name'=>'Floral Wrap Dress','cat'=>"Women's Fashion",'price'=>1899,'sale'=>1399,'stock'=>95,'brand'=>'GraceWear','desc'=>'Elegant floral print wrap dress in soft chiffon fabric. V-neckline, adjustable tie waist. Perfect for parties and casual outings.','short'=>'Chiffon · Wrap Style · Floral Print','tags'=>['dress','women','floral','casual'],'featured'=>true],
            ['name'=>'Embroidered Kurti Set','cat'=>"Women's Fashion",'price'=>2499,'sale'=>1999,'stock'=>70,'brand'=>'DeshiStyle','desc'=>'Beautiful cotton kurti with hand embroidery work. Comes with matching palazzo pants. Ideal for Eid and festive occasions.','short'=>'Cotton · Hand Embroidery · Festive Wear','tags'=>['kurti','women','embroidery','eid'],'featured'=>true],
            ['name'=>"Kids' Cartoon T-Shirt Pack",'cat'=>"Kids' Wear",'price'=>799,'sale'=>599,'stock'=>200,'brand'=>'KidZone','desc'=>'Pack of 3 soft cotton t-shirts with fun cartoon prints. Machine washable, color-fast. Sizes available for ages 2-12.','short'=>'Pack of 3 · Cartoon Prints · Ages 2-12','tags'=>['kids','tshirt','cartoon','pack'],'featured'=>false],
            ['name'=>'School Uniform Set','cat'=>"Kids' Wear",'price'=>1499,'sale'=>1199,'stock'=>120,'brand'=>'SchoolMart','desc'=>'Complete school uniform set including shirt, trouser/skirt and tie. Durable fabric, easy to wash. Available in multiple school color combinations.','short'=>'Complete Set · Durable · Easy Wash','tags'=>['kids','school','uniform'],'featured'=>false],
            ['name'=>'Panjabi - Jamdani Design','cat'=>'Traditional Wear','price'=>3999,'sale'=>2999,'stock'=>40,'brand'=>'BengalCraft','desc'=>'Authentic Jamdani motif panjabi made from fine cotton. Hand-woven with traditional patterns. Perfect for Eid, weddings and cultural events.','short'=>'Jamdani Motif · Fine Cotton · Traditional','tags'=>['panjabi','traditional','jamdani','eid'],'featured'=>true],

            // Home & Garden
            ['name'=>'Memory Foam Pillow Pair','cat'=>'Bedding','price'=>1899,'sale'=>1399,'stock'=>85,'brand'=>'SleepWell','desc'=>'Ergonomic cervical support memory foam pillows. Hypoallergenic cover, temperature-regulating foam. Pack of 2.','short'=>'Memory Foam · Ergonomic · Hypoallergenic · 2 Pack','tags'=>['pillow','bedding','memory-foam'],'featured'=>false],
            ['name'=>'Non-Stick Cookware Set 5pc','cat'=>'Kitchen','price'=>4499,'sale'=>3299,'stock'=>35,'brand'=>'HomeChef','desc'=>'5-piece non-stick cookware set. PFOA-free granite coating, heat-resistant handles. Induction compatible. Includes frying pan, 2 saucepans, deep pan and lid.','short'=>'5pc Set · Granite Non-Stick · Induction Ready','tags'=>['cookware','kitchen','non-stick','set'],'featured'=>false],
            ['name'=>'LED Desk Lamp with USB Charging','cat'=>'Lighting','price'=>1599,'sale'=>1199,'stock'=>110,'brand'=>'LumaTech','desc'=>'Modern LED desk lamp with 5 brightness levels and 3 color temperatures. Built-in USB-A and USB-C charging ports. Touch control, auto-off timer.','short'=>'5 Brightness · Touch Control · USB Charging','tags'=>['lamp','led','desk','lighting'],'featured'=>false],

            // Sports
            ['name'=>'Cricket Bat - Kashmir Willow','cat'=>'Cricket','price'=>3499,'sale'=>2799,'stock'=>30,'brand'=>'CricketPro','desc'=>'Full-size Kashmir willow cricket bat. Oval handle, medium blade profile. Ready to play straight from the box. Suitable for leather and tennis ball.','short'=>'Kashmir Willow · Full Size · Ready To Play','tags'=>['cricket','bat','sport'],'featured'=>false],
            ['name'=>'Yoga Mat Premium 6mm','cat'=>'Fitness','price'=>1299,'sale'=>999,'stock'=>75,'brand'=>'FitLife','desc'=>'Extra-thick 6mm TPE yoga mat with alignment lines. Non-slip surface, moisture resistant. Includes carry strap. Eco-friendly material.','short'=>'6mm TPE · Non-Slip · Eco-Friendly · Carry Strap','tags'=>['yoga','mat','fitness','exercise'],'featured'=>false],
            ['name'=>'Running Shoes - Breathable Mesh','cat'=>'Fitness','price'=>3999,'sale'=>2999,'stock'=>55,'brand'=>'SpeedRun','desc'=>'Lightweight running shoes with breathable mesh upper. Responsive foam midsole, rubber outsole for grip. Anti-odor lining.','short'=>'Breathable Mesh · Responsive Foam · Anti-Odor','tags'=>['shoes','running','sports','footwear'],'featured'=>true],

            // Beauty
            ['name'=>'Vitamin C Serum 30ml','cat'=>'Skincare','price'=>1299,'sale'=>999,'stock'=>130,'brand'=>'GlowUp','desc'=>'20% Vitamin C serum with Hyaluronic Acid and Vitamin E. Brightens skin, reduces dark spots and fine lines. Dermatologist tested, suitable for all skin types.','short'=>'20% Vit C · Hyaluronic Acid · All Skin Types','tags'=>['serum','vitamin-c','skincare','glow'],'featured'=>false],
            ['name'=>'Moisturizing Sunscreen SPF 50+','cat'=>'Skincare','price'=>899,'sale'=>699,'stock'=>180,'brand'=>'SunShield','desc'=>'Lightweight daily moisturizer with SPF 50+ protection. PA++++ rating, non-greasy formula. Suitable for oily and combination skin. 50ml.','short'=>'SPF 50+ · PA++++ · Non-Greasy · 50ml','tags'=>['sunscreen','spf','skincare','protection'],'featured'=>false],
            ['name'=>'Beard Grooming Kit 8pc','cat'=>'Men Grooming','price'=>1699,'sale'=>1299,'stock'=>60,'brand'=>'ManStyle','desc'=>'Complete 8-piece beard grooming set. Includes trimmer, scissors, boar bristle brush, comb, beard oil, balm, wash and styling cream.','short'=>'8pc Kit · Trimmer · Oil · Balm · Wash','tags'=>['beard','grooming','men','kit'],'featured'=>false],

            // Books
            ['name'=>"Atomic Habits - James Clear",'cat'=>'Non-Fiction','price'=>699,'sale'=>549,'stock'=>100,'brand'=>'Publisher','desc'=>"James Clear's #1 New York Times bestseller on building good habits and breaking bad ones. Proven framework for improving 1% every day.'",'short'=>'#1 NYT Bestseller · Self-Improvement · Paperback','tags'=>['books','habits','self-help','bestseller'],'featured'=>false],
            ['name'=>'HSC Physics Guide 2025','cat'=>'Textbooks','price'=>450,'sale'=>null,'stock'=>200,'brand'=>'BanglaEdu','desc'=>'Comprehensive HSC Physics guide with solved examples, MCQ practice and board question analysis. Covering all chapters of the new curriculum.','short'=>'New Curriculum · Solved Examples · MCQ Practice','tags'=>['hsc','physics','textbook','education'],'featured'=>false],

            // Food
            ['name'=>'Organic Honey 500g','cat'=>'Organic','price'=>599,'sale'=>499,'stock'=>90,'brand'=>'PureFarm','desc'=>'100% pure, raw, unfiltered organic honey. Collected from Sundarbans region. No additives, no sugar. Rich in antioxidants and natural enzymes.','short'=>'100% Pure · Sundarbans · Raw & Unfiltered · 500g','tags'=>['honey','organic','food','natural'],'featured'=>false],
        ];

        $createdProducts = [];
        foreach ($products as $idx => $pd) {
            $cat = $categoryMap[$pd['cat']] ?? null;
            $slug = Str::slug($pd['name']).'-'.Str::random(5);

            $product = Product::firstOrCreate(['slug'=>$slug],[
                'name'              => $pd['name'],
                'slug'              => $slug,
                'description'       => $pd['desc'],
                'short_description' => $pd['short'],
                'price'             => $pd['price'],
                'sale_price'        => $pd['sale'],
                'cost_price'        => $pd['price'] * 0.6,
                'sku'               => 'SKU-'.strtoupper(Str::random(6)),
                'stock'             => $pd['stock'],
                'category_id'       => $cat?->id,
                'brand'             => $pd['brand'],
                'is_active'         => true,
                'is_featured'       => $pd['featured'],
                'tags'              => $pd['tags'],
            ]);
            $createdProducts[] = $product;
        }

        // ── Coupons ──
        Coupon::firstOrCreate(['code'=>'WELCOME10'],[
            'type'=>'percentage','value'=>10,'min_order_amount'=>500,
            'max_uses'=>1000,'is_active'=>true,'expires_at'=>now()->addYear(),
        ]);
        Coupon::firstOrCreate(['code'=>'FLAT150'],[
            'type'=>'fixed','value'=>150,'min_order_amount'=>1500,
            'max_uses'=>500,'is_active'=>true,'expires_at'=>now()->addMonths(6),
        ]);
        Coupon::firstOrCreate(['code'=>'EID2025'],[
            'type'=>'percentage','value'=>20,'min_order_amount'=>2000,
            'max_uses'=>200,'is_active'=>true,'expires_at'=>now()->addMonths(3),
        ]);

        // ── Settings ──
        $defaults = [
            ['key'=>'site_name','value'=>'ShopBD','group'=>'general'],
            ['key'=>'site_tagline','value'=>'Bangladesh\'s Best Online Store','group'=>'general'],
            ['key'=>'site_email','value'=>'info@shopbd.com','group'=>'general'],
            ['key'=>'site_phone','value'=>'+8801700000000','group'=>'general'],
            ['key'=>'site_address','value'=>'Dhaka, Bangladesh','group'=>'general'],
            ['key'=>'site_currency','value'=>'BDT','group'=>'general'],
            ['key'=>'site_currency_symbol','value'=>'৳','group'=>'general'],
            ['key'=>'free_shipping_min','value'=>'1000','group'=>'shipping'],
            ['key'=>'flat_rate_amount','value'=>'80','group'=>'shipping'],
            ['key'=>'free_shipping_enabled','value'=>'1','group'=>'shipping'],
            ['key'=>'flat_rate_enabled','value'=>'1','group'=>'shipping'],
            ['key'=>'cod_enabled','value'=>'1','group'=>'payment'],
            ['key'=>'stripe_enabled','value'=>'1','group'=>'payment'],
            ['key'=>'sslcommerz_enabled','value'=>'1','group'=>'payment'],
        ];
        foreach ($defaults as $s) {
            Setting::firstOrCreate(['key'=>$s['key']],$s);
        }

        // ── Sample Reviews ──
        $reviewTexts = [
            ['rating'=>5,'title'=>'Excellent product!','comment'=>'Absolutely love it. Exactly as described, fast delivery!'],
            ['rating'=>4,'title'=>'Good value','comment'=>'Great quality for the price. Would recommend to others.'],
            ['rating'=>5,'title'=>'Perfect!','comment'=>'Exceeded my expectations. Will definitely buy again.'],
            ['rating'=>3,'title'=>'Decent','comment'=>'Average product. Does the job but nothing special.'],
            ['rating'=>5,'title'=>'Highly recommended','comment'=>'Amazing quality! Very happy with this purchase.'],
        ];
        foreach (array_slice($createdProducts, 0, 10) as $product) {
            $rv = $reviewTexts[array_rand($reviewTexts)];
            Review::firstOrCreate(
                ['product_id'=>$product->id,'user_id'=>$cu->id],
                array_merge($rv,['is_approved'=>true])
            );
        }

        $this->command->info('');
        $this->command->info('✅  Database seeded successfully!');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('🔑  Super Admin : admin@shopbd.com / admin123');
        $this->command->info('🔑  Manager     : manager@shopbd.com / manager123');
        $this->command->info('🔑  Customer    : customer@shopbd.com / customer123');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('📦  Products    : '.Product::count());
        $this->command->info('🗂️   Categories  : '.Category::count().' (incl. sub-cats)');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    }
}
