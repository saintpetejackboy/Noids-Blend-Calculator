<?php
// includes/get_ingredient_attributes.php
include('dbconn.php');

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("
    SELECT a.id, a.name, a.emoji
    FROM attributes a
    INNER JOIN ingredient_attributes ia ON ia.attribute_id = a.id
    WHERE ia.ingredient_id = ?
");
$stmt->execute([$id]);
$attributes = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode(['attributes' => $attributes]);
?>
