<?php
// add_note.php
include('includes/dbconn.php');

$blendId = $_POST['blend_id'] ?? 0;
$note = $_POST['note'] ?? '';
$email = $_POST['email'] ?? '';

if (empty($note)) {
    echo "Note cannot be empty.";
    exit;
}

$stmt = $pdo->prepare("INSERT INTO blend_notes (blend_id, note, email) VALUES (?, ?, ?)");
$stmt->execute([$blendId, $note, $email]);

echo "Note added successfully.";
?>
