<?php
// includes/get_blend.php
include('dbconn.php');

$blendId = $_GET['id'] ?? 0;
$blendId = intval($blendId);

$stmt = $pdo->prepare("SELECT * FROM blends WHERE id = ?");
$stmt->execute([$blendId]);
$blend = $stmt->fetch(PDO::FETCH_ASSOC);

if ($blend) {
    echo "<h2 class='text-xl mb-4'>{$blend['name']}</h2>";

    // Fetch blend ingredients with grams
    $stmt = $pdo->prepare("
        SELECT bi.ingredient_id, bi.grams, i.name 
        FROM blend_ingredients bi 
        JOIN ingredients i ON bi.ingredient_id = i.id 
        WHERE bi.blend_id = ?
    ");
    $stmt->execute([$blendId]);
    $ingredients = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calculate total grams
    $totalGrams = array_sum(array_column($ingredients, 'grams'));

    echo "<table class='w-full mb-4'>
    <thead>
        <tr>
            <th>Ingredient</th>
            <th>Grams</th>
            <th>Attributes</th>
            <th>% Composition</th>
            <th>Price</th> <!-- New Header -->
        </tr>
    </thead>
    <tbody>";

    foreach ($ingredients as $ing) {
        // Fetch attributes for each ingredient
        $stmt = $pdo->prepare("
            SELECT a.emoji, a.name 
            FROM attributes a 
            JOIN ingredient_attributes ia ON a.id = ia.attribute_id 
            WHERE ia.ingredient_id = ?
        ");
        $stmt->execute([$ing['ingredient_id']]);
        $attributes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Prepare attributes display
        $attrDisplay = '';
        foreach ($attributes as $attr) {
			$attrDisplay .= "<span title='" . htmlspecialchars($attr['name']) . "'>{$attr['emoji']}</span> ";

        }

        // Calculate percentage
        $percentage = $totalGrams > 0 ? ($ing['grams'] / $totalGrams) * 100 : 0;

        $price = $ing['price'] ?? '0.00';

        echo "<tr>
                <td>{$ing['name']}</td>
                <td>{$ing['grams']}g</td>
                <td>{$attrDisplay}</td>
                <td>" . number_format($percentage, 2) . "%</td>
                <td>$" . number_format($price, 2) . "</td> <!-- New Cell -->
            </tr>";
    }
    echo "</tbody></table>";

    // Add "Add as Ingredient" button
    echo "<button onclick=\"addIngredient({$blend['id']}, '" . addslashes($blend['name']) . "', 'blend', this)\" class='bg-green-500 text-white p-2 rounded hover:bg-green-600 transition duration-300'>Add as Ingredient</button>";

    // **New Sections for Images and Notes**

    // Display Existing Images
    echo "<div class='mt-6'>
            <h3 class='text-lg mb-2'>📸 Images</h3>
            <div class='flex flex-wrap gap-4'>";
    
    $stmt = $pdo->prepare("SELECT image_path FROM blend_images WHERE blend_id = ?");
    $stmt->execute([$blendId]);
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($images as $image) {
        echo "<img src='{$image['image_path']}' alt='Blend Image' class='w-32 h-32 object-cover rounded'>";
    }
    
    echo "</div>
<form id='upload-image-form' class='mt-4' enctype='multipart/form-data'>
    <input type='hidden' name='blend_id' value='".$blendId."'>
	
	
    <div class='relative'>

<div id=\"image-upload-area\" class=\"bg-gray-800 p-6 rounded-lg border-2 border-dashed border-gray-500 flex flex-col items-center justify-center space-y-4\">
    <div id=\"preview-container\" class=\"hidden\">
        <img id=\"image-preview\" class=\"max-w-full max-h-48 rounded-lg\" alt=\"Image preview\" />
    </div>
    <div id=\"upload-icon\" class=\"text-4xl\">📸🖼️</div>
    <p class=\"text-gray-400 text-center\">Drag and drop your image here or click to select</p>
    <label for='image-upload-input' class='cursor-pointer bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 flex items-center justify-center transition duration-300'>
        📤 Select Image
    </label>

<!-- After -->
<input id='image-upload-input' name='image' type='file' accept='image/*' class='hidden' />

</div>
    
    </div>
    <button type='submit' class='bg-blue-500 text-white p-2 rounded hover:bg-blue-600 mt-2'>🖼️ Upload Image</button>
</form>
<div id='upload-image-status' class='mt-2'></div>

          </div>";

    // Display Existing Notes
    echo "<div class='mt-6'>
            <h3 class='text-lg mb-2'>📝 Notes</h3>
            <div class='space-y-2'>";
    
    $stmt = $pdo->prepare("SELECT note, email, created_at FROM blend_notes WHERE blend_id = ? ORDER BY created_at DESC");
    $stmt->execute([$blendId]);
    $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($notes as $note) {
        echo "<div class='p-2 bg-gray-700 rounded'>
                <p>{$note['note']}</p>
                <p class='text-sm text-gray-400'>By: " . ($note['email'] ?: 'Anonymous') . " on " . date('F j, Y, g:i a', strtotime($note['created_at'])) . "</p>
              </div>";
    }
    
    echo "</div>
          <form id='add-note-form' class='mt-4'>
              <input type='hidden' name='blend_id' value='{$blendId}'>
              <textarea name='note' class='w-full p-2 bg-gray-700 rounded' placeholder='Add a note...' required></textarea>
              <input type='email' name='email' class='w-full p-2 bg-gray-700 rounded mt-2' placeholder='Your email (optional)'>
              <button type='submit' class='bg-blue-500 text-white p-2 rounded hover:bg-blue-600 transition duration-300 mt-2'>💾 Add Note</button>
          </form>
          <div id='add-note-status' class='mt-2'></div>
          </div>";
} else {
    echo "<p>Blend not found.</p>";
}
?>
