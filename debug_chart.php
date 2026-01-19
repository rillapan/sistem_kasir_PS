<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$controller = app('App\Http\Controllers\HomeController');
$data = $controller->hourlyRevenueAndSalesData();

echo "=== Chart Data Debug ===" . PHP_EOL;
echo "Labels count: " . count($data['labels']) . PHP_EOL;
echo "Datasets count: " . count($data['datasets']) . PHP_EOL . PHP_EOL;

echo "=== Revenue Data (Paid - Green) ===" . PHP_EOL;
$revenueData = $data['datasets'][0]['data'];
$nonZeroRevenue = [];
for ($i = 0; $i < 24; $i++) {
    if ($revenueData[$i] > 0) {
        $nonZeroRevenue[] = $i . ':00 = Rp ' . number_format($revenueData[$i], 0, ',', '.');
    }
}
if (empty($nonZeroRevenue)) {
    echo "No revenue data found for any hour!" . PHP_EOL;
} else {
    echo "Hours with revenue: " . PHP_EOL;
    foreach ($nonZeroRevenue as $item) {
        echo "  " . $item . PHP_EOL;
    }
}
echo "Total Revenue: Rp " . number_format(array_sum($revenueData), 0, ',', '.') . PHP_EOL . PHP_EOL;

echo "=== Sales Data (All - Blue) ===" . PHP_EOL;
$salesData = $data['datasets'][1]['data'];
$nonZeroSales = [];
for ($i = 0; $i < 24; $i++) {
    if ($salesData[$i] > 0) {
        $nonZeroSales[] = $i . ':00 = Rp ' . number_format($salesData[$i], 0, ',', '.');
    }
}
if (empty($nonZeroSales)) {
    echo "No sales data found for any hour!" . PHP_EOL;
} else {
    echo "Hours with sales: " . PHP_EOL;
    foreach ($nonZeroSales as $item) {
        echo "  " . $item . PHP_EOL;
    }
}
echo "Total Sales: Rp " . number_format(array_sum($salesData), 0, ',', '.') . PHP_EOL . PHP_EOL;

echo "=== Dataset Configuration ===" . PHP_EOL;
echo "Dataset 0 Label: " . $data['datasets'][0]['label'] . PHP_EOL;
echo "Dataset 0 Color: " . $data['datasets'][0]['borderColor'] . PHP_EOL;
echo "Dataset 1 Label: " . $data['datasets'][1]['label'] . PHP_EOL;
echo "Dataset 1 Color: " . $data['datasets'][1]['borderColor'] . PHP_EOL;
