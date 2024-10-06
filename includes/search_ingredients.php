
<?php
include('dbconn.php');
$q = $_GET['q'] ?? '';
$searchTerm = "%$q%";

// Search ingredients
$stmt = $pdo->prepare("SELECT id, name, 'ingredient' AS type FROM ingredients WHERE name LIKE ? OR id IN (SELECT ingredient_id FROM keywords WHERE keyword LIKE ?)");
$stmt->execute([$searchTerm, $searchTerm]);
$ingredients = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Search blends
$stmt = $pdo->prepare("SELECT id, name, 'blend' AS type FROM blends WHERE name LIKE ?");
$stmt->execute([$searchTerm]);
$blends = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Combine results
$results = array_merge($ingredients, $blends);

echo json_encode($results);
?>
