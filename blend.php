<?php // blend.php ?>
<form action="add_note.php" method="POST">
    <input type="hidden" name="blend_id" value="<?php echo $blendId; ?>">
    <textarea name="note" required></textarea>
    <input type="email" name="email" placeholder="Your email (optional)">
    <button type="submit">Add Note</button>
</form>


<form action="blend.php" method="GET">
    <input type="text" name="search" placeholder="Search blends...">
    <button type="submit">Search</button>
</form>


<?php
include('includes/dbconn.php');

$search = $_GET['search'] ?? '';

$stmt = $pdo->prepare("SELECT * FROM blends WHERE name LIKE ? OR id IN (SELECT blend_id FROM blend_ingredients WHERE ingredient_id IN (SELECT id FROM ingredients WHERE name LIKE ?))");
$searchTerm = "%$search%";
$stmt->execute([$searchTerm, $searchTerm]);
$blends = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($blends as $blend) {
    echo "<h2>{$blend['name']}</h2>";
    // Display blend details
}
?>



<div class="container mx-auto p-4">
    <h2 class="text-xl mb-4">Add New Ingredient</h2>
    <form id="add-ingredient-form" class="bg-gray-800 p-4 rounded">
        <div class="mb-4">
            <label class="block mb-1">Ingredient Name</label>
            <input type="text" name="name" class="w-full p-2 text-black" required>
        </div>
        <div class="mb-4">
            <label class="block mb-1">Description</label>
            <textarea name="description" class="w-full p-2 text-black"></textarea>
        </div>
        <div class="mb-4">
            <label class="block mb-1">Attributes</label>
            <div id="attributes-list" class="flex flex-wrap">
                <!-- Attributes checkboxes will be populated here -->
            </div>
        </div>
        <button type="submit" class="bg-primary text-black p-2">Add Ingredient</button>
    </form>
</div>
