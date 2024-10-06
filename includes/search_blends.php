<?php
// includes/search_blends.php
include('dbconn.php');
$q = $_GET['q'] ?? '';
$stmt = $pdo->prepare("SELECT * FROM blends WHERE name LIKE ?");
$searchTerm = '%' . $q . '%';
$stmt->execute([$searchTerm]);
$blends = $stmt->fetchAll(PDO::FETCH_ASSOC);

$result = [];

foreach ($blends as $blend) {
    // Fetch total grams for the blend
    $stmtTotal = $pdo->prepare("
        SELECT SUM(grams) as total_grams
        FROM blend_ingredients
        WHERE blend_id = ?
    ");
    $stmtTotal->execute([$blend['id']]);
    $totalGrams = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total_grams'];

    // Fetch attributes with their grams
    $stmtAttr = $pdo->prepare("
        SELECT DISTINCT a.emoji, a.name, SUM(bi.grams) as attr_grams
        FROM attributes a 
        JOIN ingredient_attributes ia ON a.id = ia.attribute_id
        JOIN blend_ingredients bi ON ia.ingredient_id = bi.ingredient_id
        WHERE bi.blend_id = ?
        GROUP BY a.id
    ");
    $stmtAttr->execute([$blend['id']]);
    $attributes = $stmtAttr->fetchAll(PDO::FETCH_ASSOC);

    // Fetch images
    $stmtImages = $pdo->prepare("SELECT image_path FROM blend_images WHERE blend_id = ?");
    $stmtImages->execute([$blend['id']]);
    $images = $stmtImages->fetchAll(PDO::FETCH_ASSOC);

    // Calculate percentages and format attributes
    $formattedAttributes = [];
    foreach ($attributes as $attr) {
        $percentage = ($attr['attr_grams'] / $totalGrams) * 100;
        $formattedAttributes[] = $attr['emoji'] . ' ' . round($percentage, 1) . '%';
    }

    $result[] = [
        'id' => $blend['id'],
        'name' => $blend['name'],
        'attributes' => $formattedAttributes,
        'images' => array_column($images, 'image_path') // Include images
    ];
}

echo json_encode($result);
?>
