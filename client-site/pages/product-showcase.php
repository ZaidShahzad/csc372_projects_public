<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">  
    <title>Product Showcase</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
</head>
<body>
    <?php
        /**
         * Class representing a Catalog Product
         */
        class CatalogProduct {

            // Properties
            public $name;
            public $description;
            public $type;
            public $brand;
            public $quantity;

            // Constructor to initialize a new catalog product
            function __construct($name, $description, $type, $brand, $quantity) {
                $this->name = $name;
                $this->description = $description;
                $this->type = $type;
                $this->brand = $brand;
                $this->quantity = $quantity;
            }

            // Buy the product
            // If it's in stock, decrease the quantity by 1, and return true
            // Otherwise, return false
            public function buy() {
                if ($this->isInStock()) {
                    $this->quantity--;
                    return true;
                }
                return false;
            }

            // Get the current quantity in stock
            public function getQuantity() {
                return $this->quantity;
            }

            // Check if the product is in stock
            public function isInStock() {
                return $this->quantity > 0;
            }

            // Get the product information as string
            public function getProductInformation() {
                return "Product: {$this->name}\n" .
                    "Description: {$this->description}\n" .
                    "Type: {$this->type}\n" . 
                    "Brand: {$this->brand}\n" .
                    "Quantity: {$this->quantity}";
            }

        }

        // Create the unisex t-shirt product
        $unisexTshirt = new CatalogProduct(
            "Unisex T-Shirt",
            "Unisex t-shirt for anyone", 
            "Tops",
            "Polo",
            999
        );

        // Create the classic dad hat product
        $classicDadHat = new CatalogProduct(
            "Classic Dad Hat", 
            "Elegant hat for anyone",
            "Hats",
            "Economous",
            999
        );
    ?>
    <div class="mx-auto max-w-7xl px-6 py-24">
      <h1 class="text-4xl font-bold text-center text-gray-900 mb-16 drop-shadow-lg">Product Showcase</h1>

      <div class="grid gap-8 sm:grid-cols-2">
        <?php
        $products = [$unisexTshirt, $classicDadHat];
        
        foreach($products as $product): ?>
          <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-4"><?php echo $product->name; ?></h2>
            <p class="text-gray-600 mb-4"><?php echo $product->description; ?></p>
            
            <div class="text-sm text-gray-500 mb-4">
              <p>Brand: <?php echo $product->brand; ?></p>
              <p>Category: <?php echo $product->type; ?></p>
            </div>

            <div class="flex items-center justify-between">
              <?php if($product->isInStock()): ?>
                <span class="px-2 py-1 text-xs font-medium text-green-700 bg-green-50 rounded-full">In Stock</span>
              <?php else: ?>
                <span class="px-2 py-1 text-xs font-medium text-red-700 bg-red-50 rounded-full">Out of Stock</span>
              <?php endif; ?>
              <span class="text-sm font-medium">Qty: <?php echo $product->getQuantity(); ?></span>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
</body>
</html>


