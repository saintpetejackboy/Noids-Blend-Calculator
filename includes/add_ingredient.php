<?php
// includes/add_ingredient.php
include('dbconn.php');

// Get JSON input
$data = json_decode(file_get_contents('php://input'), true);

$name = $data['name'] ?? '';
$description = $data['description'] ?? '';
$attributes = $data['attributes'] ?? []; // Array of attribute IDs

if (empty($attributes)) {
    echo json_encode(['status' => 'error', 'message' => 'At least one attribute is required.']);
    exit;
}

if (empty($name)) {
    echo json_encode(['status' => 'error', 'message' => 'Ingredient name is required.']);
    exit;
}

try {
    $pdo->beginTransaction();

    // Insert the new ingredient
    $stmt = $pdo->prepare("INSERT INTO ingredients (name, description) VALUES (?, ?)");
    $stmt->execute([$name, $description]);
    $ingredient_id = $pdo->lastInsertId();

    // Insert attributes if available
    if (!empty($attributes)) {
        $stmt = $pdo->prepare("INSERT INTO ingredient_attributes (ingredient_id, attribute_id) VALUES (?, ?)");
        foreach ($attributes as $attr_id) {
            $stmt->execute([$ingredient_id, (int)$attr_id]);
        }
    }

    $pdo->commit();

// After $pdo->commit();
    echo json_encode(['status' => 'success', 'ingredient' => ['id' => $ingredient_id, 'name' => $name]]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
