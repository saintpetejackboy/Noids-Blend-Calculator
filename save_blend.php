<?php
// save_blend.php
include('includes/dbconn.php');

$data = json_decode(file_get_contents('php://input'), true);
$blendName = $data['name'] ?? 'Untitled Blend';
$ingredients = $data['ingredients'] ?? [];

if (empty($blendName) || empty($ingredients)) {
    echo json_encode(['status' => 'error', 'message' => 'Blend name and ingredients are required.']);
    exit;
}

$pdo->beginTransaction();

try {
    $stmt = $pdo->prepare("INSERT INTO blends (name) VALUES (?)");
    $stmt->execute([$blendName]);
    $blendId = $pdo->lastInsertId();

    $stmt = $pdo->prepare("INSERT INTO blend_ingredients (blend_id, ingredient_id, grams, price) VALUES (?, ?, ?, ?)");
    foreach ($ingredients as $ingredient) {
        $stmt->execute([
            $blendId,
            $ingredient['id'],
            $ingredient['grams'],
            $ingredient['price'] // Ensure price is included
        ]);
    }

    $pdo->commit();
    echo json_encode(['status' => 'success', 'blend_id' => $blendId]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
