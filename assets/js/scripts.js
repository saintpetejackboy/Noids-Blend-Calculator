let selectedIngredients = [];

// Define blendCreationContainer
const blendCreationContainer = document.getElementById("blend-creation-container");
const saveBlendButton = document.getElementById("save-blend");


// Ingredient Search Listener
document.getElementById("ingredient-search").addEventListener("input", function(){
    const query = this.value.trim();
    if(query.length === 0){
        document.getElementById("ingredient-list").innerHTML = "";
        return;
    }
    fetch("includes/search_ingredients.php?q=" + encodeURIComponent(query))
        .then(response => response.json())
        .then(data => {
            let html = "";
            data.forEach(item => {
                let displayName = item.name;
                let type = item.type;
                let typeLabel = type === "blend" ? "[Blend]" : "[Ingredient]";
                let isAdded = selectedIngredients.some(ing => ing.id === item.id && ing.type === type);
                let checkMark = isAdded ? "✅" : "";
                html += `
                    <div class="p-2 border-b border-primary cursor-pointer hover:bg-gray-700" onclick="addIngredient(${item.id}, '${item.name}', '${type}', this)">
                        ${typeLabel} ${displayName} ${checkMark}
                    </div>
                `;
            });
            document.getElementById("ingredient-list").innerHTML = html;
        })
        .catch(error => {
            console.error("Error fetching ingredients:", error);
        });
});

// Add Ingredient Function
function addIngredient(id, name, type = "ingredient", element = null) {
    if (selectedIngredients.some(ing => ing.id === id && ing.type === type)) {
        if(element){
            element.classList.add("shake");
            setTimeout(() => {
                element.classList.remove("shake");
            }, 500);
        }

        // Shake the duplicate row if it exists
        const duplicateRow = document.querySelector(`tr[data-id="${id}"][data-type="${type}"]`);
        if (duplicateRow) {
            duplicateRow.classList.add("shake");
            setTimeout(() => {
                duplicateRow.classList.remove("shake");
            }, 500);
        }

        return;
    }

    const fetchUrl = type === "blend" ? "includes/get_blend_attributes.php" : "includes/get_ingredient_attributes.php";

    fetch(`${fetchUrl}?id=${id}`)
        .then(response => response.json())
        .then(data => {
            const attributes = data.attributes;
            selectedIngredients.push({id: id, name: name, grams: 0, price: 0, attributes: attributes, type: type});
            const attributeEmojis = attributes.map(attr => `<span title="${attr.name}">${attr.emoji}</span>`).join(" ");

            
            // **Updated Row with Price Input**
            const row = document.createElement("tr");
            row.setAttribute("data-id", id);
            row.setAttribute("data-type", type);
            row.innerHTML = `
                <td>${name}</td>
                <td>${attributeEmojis}</td>
                <td class="text-right">
                    <input type="number" min="0" step="0.001" class="w-20 p-1 bg-gray-700 text-input rounded" value="0" oninput="updateIngredient(${id}, '${type}', 'grams', this.value)">
                </td>
                <td class="ingredient-percentage text-right">0%</td>
                <td class="text-right">
                    <input type="number" min="0" step="0.01" class="w-20 p-1 bg-gray-700 text-input rounded" value="0" oninput="updateIngredient(${id}, '${type}', 'price', this.value)">
                </td>
                <td class="text-right">
                    <button onclick="removeIngredient(${id}, '${type}')" class="text-red-500 hover:text-red-700 cursor-pointer">🗑️</button>
                </td>
            `;
            document.getElementById("blend-ingredients").appendChild(row);
            updateCalculations();
        })
        .catch(error => {
            console.error(`Error fetching ${type} attributes:`, error);
            alert(`Failed to add ${type}.`);
        });
}

// Update Ingredient Function
function updateIngredient(id, type, field, value){
    const ingredient = selectedIngredients.find(ing => ing.id === id && ing.type === type);
    if (ingredient) {
        ingredient[field] = parseFloat(value) || 0;
        updateCalculations();
    } else {
        console.error(`Ingredient with ID ${id} and type ${type} not found.`);
    }
}

// Remove Ingredient Function
function removeIngredient(id, type){
    selectedIngredients = selectedIngredients.filter(ing => !(ing.id === id && ing.type === type));
    const row = document.querySelector(`tr[data-id="${id}"][data-type="${type}"]`);
    if (row) {
        row.remove();
    }
    updateCalculations();
}

function updateCalculations(){
  let totalGrams = 0;
  let totalPrice = 0;
  selectedIngredients.forEach(ing => {
      totalGrams += ing.grams;
      totalPrice += ing.price;
  });
  selectedIngredients.forEach(ing => {
      ing.percentage = totalGrams > 0 ? (ing.grams / totalGrams * 100) : 0;
      const row = document.querySelector(`tr[data-id="${ing.id}"][data-type="${ing.type}"]`);
      if(row){
          const percentageCell = row.querySelector(".ingredient-percentage");
          if(percentageCell){
              percentageCell.textContent = ing.percentage.toFixed(2) + "%";
          }
      }
  });

  // Calculate attribute totals with emojis
  let attributeTotals = {};
  selectedIngredients.forEach(ing => {
      ing.attributes.forEach(attr => {
          if(!attributeTotals[attr.name]) {
              attributeTotals[attr.name] = { emoji: attr.emoji, grams: 0 };
          }
          attributeTotals[attr.name].grams += ing.grams;
      });
  });

  let attributePercentages = {};
  for(let attrName in attributeTotals){
      attributePercentages[attrName] = totalGrams > 0 ? (attributeTotals[attrName].grams / totalGrams * 100) : 0;
  }

  // Update calculations area with emojis
  document.getElementById("calculations").innerHTML = `
      <p>Total Weight: ${totalGrams.toFixed(3)} grams</p>
      <p>Total Price: $${totalPrice.toFixed(2)}</p>
      <p class="mt-4">Attribute Percentages:</p>
      <ul>
          ${Object.keys(attributePercentages).map(attrName => {
			const emoji = attributeTotals[attrName].emoji;
			return `<li><span title="${attrName}">${emoji}</span> ${attrName}: ${attributePercentages[attrName].toFixed(2)}%</li>`;
		}).join("")}
      </ul>
  `;
}

// Save Blend Event Listener
document.getElementById("save-blend").addEventListener("click", function(){
    const blendName = prompt("Enter a name for your blend:", "");
    if(!blendName) return;
    fetch("save_blend.php", {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify({name: blendName, ingredients: selectedIngredients})
    })
    .then(response => response.json())
    .then(data => {
        if(data.status === "success") {
            alert("Blend saved successfully! Reloading...");
            location.reload(); // Reload the current page
        } else {
            alert("Error saving blend.");
        }
    })
    .catch(error => {
        console.error("Error saving blend:", error);
        alert("Error saving blend.");
    });
});

// Add Ingredient Form Submission
document.getElementById("add-ingredient-form").addEventListener("submit", function(event){
    event.preventDefault();
    const formData = new FormData(this);
    const data = {
        name: formData.get("name"),
        description: formData.get("description"),
        attributes: formData.getAll("attributes[]")
    };
    console.log(data);
    if(data.attributes.length === 0){
        alert("Please select at least one attribute.");
        return;
    }
    fetch("includes/add_ingredient.php", {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if(result.status === "success"){
            document.getElementById("add-ingredient-modal").classList.add("hidden");
            addIngredient(result.ingredient.id, result.ingredient.name, "ingredient");
            this.reset();
        } else {
            alert("Error adding ingredient: " + result.message);
        }
    })
    .catch(error => {
        console.error("Error:", error);
    });
});

// Open Add Ingredient Modal
document.getElementById("open-add-ingredient-modal").addEventListener("click", function(){
    document.getElementById("add-ingredient-modal").classList.remove("hidden");
});

// Close Add Ingredient Modal
document.getElementById("toggle-blend-search").addEventListener("click", function() {
    const searchContainer = document.getElementById("blend-search-container");
    const blendCreationContainer = document.getElementById("blend-creation-container");
    const calculations = document.getElementById("calculations");
    const saveBlendButton = document.getElementById("save-blend");
    const toggleSearchButton = document.getElementById("toggle-blend-search");

    const isHidden = searchContainer.classList.contains("hidden");

    if (isHidden) {
        // Show search, hide blend creation and save button
        searchContainer.classList.remove("hidden");
        blendCreationContainer.classList.add("hidden");
        calculations.classList.add("hidden");
        saveBlendButton.classList.add("hidden");
        toggleSearchButton.innerHTML = "🔒 Close Search"; // Update button text/icon
    } else {
        // Hide search, show blend creation and save button
        searchContainer.classList.add("hidden");
        blendCreationContainer.classList.remove("hidden");
        calculations.classList.remove("hidden");
        saveBlendButton.classList.remove("hidden");
        toggleSearchButton.innerHTML = "🔎 Search Blends"; // Revert button text/icon
    }
});


// Blend Search Input Listener


document.getElementById("blend-search").addEventListener("input", function(){
  const query = this.value.trim();
  if(query.length === 0){
      document.getElementById("blend-list").innerHTML = "";
      return;
  }
  fetch("includes/search_blends.php?q=" + encodeURIComponent(query))
  .then(response => response.json())
  .then(data => {
      if(data.length === 0){
          document.getElementById("blend-list").innerHTML = "<p class='p-2'>No blends found.</p>";
          return;
      }

      let html = `
          <table class="w-full table-auto bg-gray-800 rounded">
              <thead>
                  <tr>
                      <th class="px-4 py-2 text-left">Name</th>
                      <th class="px-4 py-2 text-left">Attributes</th>
                      <th class="px-4 py-2 text-left">Actions</th>
                  </tr>
              </thead>
              <tbody>
      `;

      // Inside the fetch for blend-search input listener
		data.forEach(blend => {
			const attributes = blend.attributes.join(" ");
			const images = blend.images; // Array of image paths
			let imageHtml = "";
				if(images && images.length > 0){
					imageHtml = `<img src="${images[0]}" alt="${blend.name} Image" class="w-16 h-16 object-cover rounded mr-2">`;
				} else {
					// Default image if none exist
					imageHtml = `<img src="uploads/images/missing.webp" alt="Default Image" class="w-16 h-16 object-cover rounded mr-2">`;
				}

			html += `
				<tr class="border-t border-gray-700 hover:bg-gray-700 cursor-pointer">
					<td class="px-4 py-2 flex items-center">${imageHtml} ${blend.name}</td>
					<td class="px-4 py-2">${attributes}</td>
					<td class="px-4 py-2">
						<button onclick="openBlendModal(${blend.id})" class="bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600">View</button>
						<button onclick="addIngredient(${blend.id}, '${blend.name.replace(/'/g, "\\'")}', 'blend', this)" class="bg-green-500 text-white px-2 py-1 rounded hover:bg-green-600 ml-2">Add as Ingredient</button>
					</td>
				</tr>
			`;
		});


      html += "</tbody></table>";

      document.getElementById("blend-list").innerHTML = html;
  })
  .catch(error => {
      console.error("Error fetching blends:", error);
      document.getElementById("blend-list").innerHTML = "<p class='p-2 text-red-500'>Error fetching blends.</p>";
  });
});



// Live Image Preview
function setupImageUploadPreview(blendId) {
    const imageInput = document.getElementById("image-upload-input");
    const imagePreview = document.getElementById("image-preview");
    const uploadArea = document.getElementById("image-upload-area");

    if(imageInput && imagePreview && uploadArea){
        // Handle file selection via input
        imageInput.addEventListener("change", function(){
            const file = this.files[0];
            if(file){
                const reader = new FileReader();
                reader.onload = function(e){
                    imagePreview.src = e.target.result;
                    document.getElementById("preview-container").classList.remove("hidden");
                }
                reader.readAsDataURL(file);
            } else {
                imagePreview.src = "";
                document.getElementById("preview-container").classList.add("hidden");
            }
        });

        // Prevent default drag behaviors
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults (e) {
            e.preventDefault();
            e.stopPropagation();
        }

        // Highlight upload area when item is dragged over it
        ['dragenter', 'dragover'].forEach(eventName => {
            uploadArea.addEventListener(eventName, () => {
                uploadArea.classList.add('highlight');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, () => {
                uploadArea.classList.remove('highlight');
            }, false);
        });

        // Handle dropped files
        uploadArea.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            let dt = e.dataTransfer;
            let files = dt.files;

            if (files.length > 0) {
                imageInput.files = files; // Assign the dropped file to the input
                const file = files[0];
                const reader = new FileReader();
                reader.onload = function(event){
                    imagePreview.src = event.target.result;
                    document.getElementById("preview-container").classList.remove("hidden");
                }
                reader.readAsDataURL(file);
            }
        }
    }
}


// Call this function after loading blend modal content
function openBlendModal(blendId) {
    fetch(`includes/get_blend.php?id=${blendId}`)
        .then(response => response.text())
        .then(data => {
            document.getElementById("blend-modal-content").innerHTML = data;
            document.getElementById("blend-modal").classList.remove("hidden");

            // **Add Event Listeners for Image Upload and Note Addition**

            // Handle Image Upload Form
            const uploadImageForm = document.getElementById("upload-image-form");
            if (uploadImageForm) {
                uploadImageForm.addEventListener("submit", function(event) {
                    event.preventDefault();
                    const formData = new FormData(this);
                    fetch("upload_image.php", {
                        method: "POST",
                        body: formData
                    })
                    .then(response => response.text())
                    .then(message => {
                        document.getElementById("upload-image-status").innerText = message;
                        // Reload the modal content to display the new image
                        openBlendModal(blendId);
                    })
                    .catch(error => {
                        console.error("Error uploading image:", error);
                        document.getElementById("upload-image-status").innerText = "Error uploading image.";
                    });
                });

                // Initialize live preview
                setupImageUploadPreview(blendId);
            }

            // Handle Add Note Form
            const addNoteForm = document.getElementById("add-note-form");
            if (addNoteForm) {
                addNoteForm.addEventListener("submit", function(event) {
                    event.preventDefault();
                    const formData = new FormData(this);
                    fetch("add_note.php", {
                        method: "POST",
                        body: formData
                    })
                    .then(response => response.text())
                    .then(message => {
                        document.getElementById("add-note-status").innerText = message;
                        // Reload the modal content to display the new note
                        openBlendModal(blendId);
                    })
                    .catch(error => {
                        console.error("Error adding note:", error);
                        document.getElementById("add-note-status").innerText = "Error adding note.";
                    });
                });
            }
        })
        .catch(error => {
            console.error("Error fetching blend details:", error);
            alert("Failed to load blend details.");
        });
}


// Close Blend Modal
document.getElementById("close-blend-modal").addEventListener("click", function(){
    document.getElementById("blend-modal").classList.add("hidden");
});

// Close Blend Search via Close Button
document.getElementById("close-blend-search").addEventListener("click", function(event){
    event.stopPropagation(); // Prevent triggering the document click listener
    const searchContainer = document.getElementById("blend-search-container");
    const blendCreationContainer = document.getElementById("blend-creation-container");
    const calculations = document.getElementById("calculations");
    const saveBlendButton = document.getElementById("save-blend");
    const toggleSearchButton = document.getElementById("toggle-blend-search");

    searchContainer.classList.add("hidden");
    blendCreationContainer.classList.remove("hidden");
    calculations.classList.remove("hidden");
    saveBlendButton.classList.remove("hidden");
    toggleSearchButton.innerHTML = "🔎 Search Blends"; // Reset button text/icon
});


// Close Blend Search Container When Clicking Outside
document.addEventListener("click", function(event){
    const searchContainer = document.getElementById("blend-search-container");
    const toggleButton = document.getElementById("toggle-blend-search");
    if(!searchContainer.contains(event.target) && !toggleButton.contains(event.target)){
        if (!searchContainer.classList.contains("hidden")) { // Only proceed if search is open
            searchContainer.classList.add("hidden");
            blendCreationContainer.classList.remove("hidden");
            calculations.classList.remove("hidden");
            saveBlendButton.classList.remove("hidden");
            toggleButton.innerHTML = "🔎 Search Blends"; // Reset button text/icon
        }
    }
});
