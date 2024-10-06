<?php
include('includes/dbconn.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>🌿 Noids Calculator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        background: '#1a202c',
                        primary: '#ffffff',
                        input: '#fff',
                    },
                },
            },
        };

    </script>
<style>
	#close-blend-search {
		width: 32px;
		height: 32px;
		display: flex;
		align-items: center;
		justify-content: center;
		font-size: 18px;
		cursor: pointer;
	}

	#close-blend-search:hover {
		background-color: #4a5568; /* Darker gray */
	}

	#image-preview img {
			border: 2px solid #4a5568; /* Optional border */
			margin: auto;
			text-align: center;
		}
		/* Highlight drop area when active */
#image-upload-area.highlight {
    border-color: #00aaff;
    background-color: #f0f8ff;
    transition: background-color 0.3s, border-color 0.3s;
}

/* Cursor pointer for clickable area */
#image-upload-area {
    cursor: pointer;
}

/* Existing styles */

#image-upload-area.highlight {
    border-color: #00aaff;
    background-color: #f0f8ff;
    transition: background-color 0.3s, border-color 0.3s;
}

/* Ensure cursor indicates clickable area */
#image-upload-area {
    cursor: pointer;
}

/* Style for the image preview */
#image-preview {
    max-width: 100%;
    max-height: 42px; 
    border: 2px solid #4a5568; /* Optional border */
    margin: auto;
    text-align: center;
    border-radius: 8px;
}


		</style>
</head>
<body class="bg-background text-primary min-h-screen">
    <div class="container mx-auto p-4">
        <h1 class="text-3xl font-bold mb-6">🧪 Noids Calculator</h1>
        <div id="blend-creation-container">
            <div class="mb-6">
                <h2 class="text-2xl mb-4">🍶 Build Your Blend</h2>
                <div class="mb-4">
                    <label for="blend-name" class="block mb-1">🏷️ Blend Name</label>
                    <input type="text" id="blend-name" class="w-full p-2 bg-gray-700 text-input rounded border border-gray-600 focus:outline-none focus:border-black" placeholder="Enter blend name">
                </div>
            </div>
            <div class="mb-6">
                <div class="flex items-center mb-4">
                    <input type="text" id="ingredient-search" class="flex-grow p-2 bg-gray-700 text-input rounded-l border border-gray-600 focus:outline-none focus:border-black" placeholder="🔍 Search ingredients...">
                    <button id="open-add-ingredient-modal" class="bg-black text-white p-2 rounded-r hover:bg-blue-600 transition duration-300">➕</button>
                </div>
                <div id="ingredient-list" class="mb-4 bg-gray-800 rounded"></div>
            </div>
            <div class="mb-6">
                <h3 class="text-xl mb-2">📋 Selected Ingredients</h3>
                <table class="w-full mb-4">
                <thead>
    <tr>
        <th class="text-left">🥦 Ingredient</th>
        <th class="text-left">⚡ Attributes</th>
        <th class="text-right">⚖️ Grams</th>
        <th class="text-right">📊 %</th>
        <th class="text-right">💲 Price</th>
        <th></th>
    </tr>
</thead>
                    <tbody id="blend-ingredients"></tbody>
                </table>
            </div>
        </div>
        <div id="calculations" class="mb-6 p-4 bg-gray-800 rounded">🧮 Calculations</div>
        <div class="flex justify-between items-center mb-6">
            <button id="save-blend" class="bg-green-500 text-white p-2 rounded hover:bg-green-600 transition duration-300">💾 Save Blend</button>
            <button id="toggle-blend-search" class="bg-black text-white p-2 rounded hover:bg-blue-600 transition duration-300">🔎 Search Blends</button>
        </div>
        <div id="blend-search-container" class="hidden mt-4 relative">
			<button id="close-blend-search" class="absolute top-2 right-2 bg-gray-500 text-white p-2 rounded-full hover:bg-gray-600 transition duration-300">❌</button>
			<input type="text" id="blend-search" class="w-full p-2 mb-2 bg-gray-700 text-input rounded border border-gray-600 focus:outline-none focus:border-black" placeholder="🔍 Search blends...">
			<div id="blend-list" class="bg-gray-800 rounded"></div>
		</div>


    </div>

    <div id="add-ingredient-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div id="add-ingredient-modal-content" class="bg-gray-800 p-6 rounded-lg w-full max-w-md">
        <!-- Modal Content -->
        <h2 class="text-xl mb-4">➕ Add New Ingredient</h2>
        <form id="add-ingredient-form" class="space-y-4">
                <div>
                    <label class="block mb-1">🌿 Ingredient Name</label>
                    <input type="text" name="name" class="w-full p-2  bg-gray-700 rounded" required>
                </div>
                <div>
                    <label class="block mb-1">📝 Description</label>
                    <textarea name="description" class="w-full p-2 bg-gray-700 rounded"></textarea>
                </div>
                <div>
                    <label class="block mb-1">⚡ Attributes</label>
                    <div id="attributes-list" class="flex flex-wrap gap-2">
                        <?php
                            $stmt = $pdo->query("SELECT id, name, emoji FROM attributes");
                            $attributes = $stmt->fetchAll();
                            foreach ($attributes as $attribute) {
                                echo "<label class='inline-flex items-center bg-gray-700 rounded p-1'>
                                        <input type='checkbox' name='attributes[]' value='{$attribute['id']}' class='mr-1'>
                                        <span>{$attribute['emoji']} {$attribute['name']}</span>
                                      </label>";
                            }
                        ?>
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="button" id="close-add-ingredient-modal" class="bg-gray-500 text-white p-2 rounded mr-2 hover:bg-gray-600 transition duration-300">❌ Cancel</button>
                    <button type="submit" class="bg-black text-white p-2 rounded hover:bg-blue-600 transition duration-300">✔️ Add Ingredient</button>
                </div>
            </form>
        </div>
    </div>
                        </div>
    
    <!-- Blend Modal -->
    <div id="blend-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-gray-800 p-6 rounded-lg w-full max-w-2xl">
            <div id="blend-modal-content"></div>
            <button id="close-blend-modal" class="mt-4 bg-gray-500 text-white p-2 rounded hover:bg-gray-600 transition duration-300">❌ Close</button>
        </div>
    </div>

    <script src="assets/js/scripts.js"></script>
    <style>
        @keyframes shake {
            0% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            50% { transform: translateX(5px); }
            75% { transform: translateX(-5px); }
            100% { transform: translateX(0); }
        }
        .shake {
            animation: shake 0.5s;
        }
    </style>
</body>
</html>
