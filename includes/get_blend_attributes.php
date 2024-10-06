<?php
// includes/get_blend_attributes.php
include('dbconn.php');

$blendId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($blendId <= 0) {
    echo json_encode(['error' => 'Invalid blend ID']);
    exit;
}

try {
    // Fetch blend ingredients and their grams
    $stmt = $pdo->prepare("
        SELECT bi.ingredient_id, bi.grams
        FROM blend_ingredients bi
        WHERE bi.blend_id = ?
    ");
    $stmt->execute([$blendId]);
    $blendIngredients = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $totalGrams = 0;
    $attributeWeights = [];

    foreach ($blendIngredients as $ingredient) {
        $ingredientId = $ingredient['ingredient_id'];
        $grams = $ingredient['grams'];
        $totalGrams += $grams;

        // Fetch attributes for each ingredient
        $stmt = $pdo->prepare("
            SELECT a.id, a.name, a.emoji
            FROM attributes a
            INNER JOIN ingredient_attributes ia ON ia.attribute_id = a.id
            WHERE ia.ingredient_id = ?
        ");
        $stmt->execute([$ingredientId]);
        $attributes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($attributes as $attr) {
            if (!isset($attributeWeights[$attr['id']])) {
                $attributeWeights[$attr['id']] = [
                    'id' => $attr['id'],
                    'name' => $attr['name'],
                    'emoji' => $attr['emoji'],
                    'weight' => 0
                ];
            }
            $attributeWeights[$attr['id']]['weight'] += $grams;
        }
    }

    // Calculate attribute percentages
    $blendAttributes = [];
    foreach ($attributeWeights as $attrId => $attr) {
        $percentage = $totalGrams > 0 ? ($attr['weight'] / $totalGrams) * 100 : 0;
        if ($percentage >= 1) { // Only include attributes that make up at least 1%
            $blendAttributes[] = [
                'id' => $attrId,
                'name' => $attr['name'],
                'emoji' => $attr['emoji'], // Ensure emoji is included
                'percentage' => round($percentage, 2)
            ];
            
        }
    }

    // Sort attributes by percentage descending
    usort($blendAttributes, function($a, $b) {
        return $b['percentage'] <=> $a['percentage'];
    });

    echo json_encode(['attributes' => $blendAttributes]);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
