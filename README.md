# Noids-Blend-Calculator

![Noids Blend Calculator](assets/images/logo.webp)

**Noids-Blend-Calculator** is a comprehensive web-based application designed to help users create, manage, and analyze custom blends of ingredients. Whether you're in the culinary industry, crafting e-liquids, or involved in any field requiring precise ingredient blending, this calculator provides an intuitive interface to streamline your blend creation process.

## Table of Contents

- [Features](#features)
- [Demo](#demo)
- [Project Structure](#project-structure)
- [Installation](#installation)
- [Usage](#usage)
- [Technologies Used](#technologies-used)
- [Contributing](#contributing)
- [License](#license)
- [Contact](#contact)

## Features

- **Ingredient Management**: Add, search, and manage a wide variety of ingredients with detailed attributes.
- **Blend Creation**: Select multiple ingredients, specify quantities, and calculate blend compositions and costs in real-time.
- **Attribute Analysis**: Analyze blend attributes to ensure balanced compositions.
- **Image Uploads**: Upload and associate images with blends for better visualization.
- **Notes and Collaboration**: Add notes to blends, optionally associating them with your email for collaborative purposes.
- **Search Functionality**: Efficiently search through blends and ingredients to quickly find and add them to your projects.
- **Responsive Design**: User-friendly interface optimized for both desktop and mobile devices.

## Demo

![Blend Creation Interface](assets/images/blend_creation.webp)

*Blend creation interface showcasing ingredient selection and real-time calculations.*

## Project Structure

```
Noids-Blend-Calculator/
├── add_note.php
├── assets/
│   ├── js/
│   │   └── scripts.js
│   └── sql/
│       └── blend-tables.sql
├── blend.php
├── includes/
│   ├── add_ingredient.php
│   ├── dbconn.php
│   ├── get_blend.php
│   ├── get_blend_attributes.php
│   ├── get_ingredient_attributes.php
│   ├── search_blends.php
│   └── search_ingredients.php
├── index.php
├── packaged_output.txt
├── save_blend.php
├── sql/
│   └── tables.sql
├── upload_image.php
└── uploads/
    └── images/
        └── missing.webp
```

### Description of Key Components

- **add_note.php**: Handles the addition of notes to specific blends.
- **assets/js/scripts.js**: Contains all the frontend JavaScript functionalities, including ingredient search, blend calculations, and UI interactions.
- **assets/sql/blend-tables.sql**: SQL scripts related to blend tables.
- **blend.php**: Main PHP file for handling blend-related operations and displaying blend details.
- **includes/**: Contains reusable PHP scripts for database connections, fetching blend and ingredient attributes, and searching functionalities.
  - **add_ingredient.php**: Handles adding new ingredients to the database.
  - **dbconn.php**: Manages database connections.
  - **get_blend.php**: Retrieves and displays detailed information about a specific blend.
  - **get_blend_attributes.php**: Fetches attributes associated with a blend.
  - **get_ingredient_attributes.php**: Retrieves attributes for a specific ingredient.
  - **search_blends.php**: Implements search functionality for blends.
  - **search_ingredients.php**: Implements search functionality for ingredients.
- **index.php**: The main entry point of the application, providing the blend creation interface.
- **save_blend.php**: Handles saving newly created blends to the database.
- **upload_image.php**: Manages image uploads associated with blends.
- **uploads/images/**: Directory for storing uploaded blend images. Includes a default `missing.webp` image for blends without associated images.

## Installation

### Prerequisites

- **Web Server**: Apache, Nginx, or any server capable of running PHP.
- **PHP**: Version 7.4 or higher.
- **Database**: MySQL or MariaDB.
- **Composer**: For managing PHP dependencies (if applicable).

### Steps

1. **Clone the Repository**

   ```bash
   git clone https://github.com/saintpetejackboy/Noids-Blend-Calculator.git
   ```

2. **Navigate to the Project Directory**

   ```bash
   cd Noids-Blend-Calculator
   ```

3. **Set Up the Database**

   - Create a new database in MySQL/MariaDB.

   - Import the database schema:

     ```bash
     mysql -u your_username -p your_database < sql/tables.sql
     ```

4. **Configure Database Connection**

   - Open `includes/dbconn.php`.

   - Update the database connection details as per your setup:

     ```php
     <?php
     // includes/dbconn.php
     include('/path/to/your/database/config.php');
     ?>
     ```

     Ensure that `/path/to/your/database/config.php` contains the correct `$pdo` connection.

5. **Set Up File Permissions**

   - Ensure that the `uploads/images/` directory is writable by the web server:

     ```bash
     chmod -R 755 uploads/images/
     ```

6. **Deploy the Application**

   - Place the project files in your web server's root directory or configure a virtual host pointing to the project directory.

7. **Access the Application**

   - Open your web browser and navigate to `http://your-server-address/index.php`.

## Usage

### Creating a New Blend

1. **Navigate to the Home Page**

   - Access the blend creation interface via `index.php`.

2. **Add a Blend Name**

   - Enter a unique name for your blend in the "Blend Name" field.

3. **Search and Add Ingredients**

   - Use the ingredient search bar to find desired ingredients.
   - Click on an ingredient from the search results to add it to your blend.
   - Specify the quantity in grams and the price for each ingredient.

4. **View Selected Ingredients**

   - The selected ingredients will appear in a table with real-time calculations of percentages and total costs.

5. **Save the Blend**

   - Once satisfied with your blend, click on the "Save Blend" button.
   - Enter a name for your blend when prompted.
   - The blend will be saved to the database, and you can view it later in the "Search Blends" section.

### Managing Blends and Ingredients

- **Search Blends**

  - Click on the "Search Blends" button to open the blend search interface.
  - Enter keywords to find existing blends.
  - View details, add notes, or incorporate existing blends as ingredients into new blends.

- **Add New Ingredients**

  - Click on the "➕" button next to the ingredient search bar to open the "Add New Ingredient" modal.
  - Fill in the ingredient name, description, and select relevant attributes.
  - Submit the form to add the ingredient to the database.

- **Upload Images**

  - Within a blend's details, use the image upload feature to associate images with your blend.
  - Drag and drop images or select them via the file input.

- **Add Notes**

  - Add notes to blends to keep track of observations, modifications, or collaborative inputs.
  - Optionally provide an email to associate with the note.

## Technologies Used

- **Frontend**:
  - HTML5
  - CSS3 (Tailwind CSS)
  - JavaScript (Vanilla JS)

- **Backend**:
  - PHP 7.4+
  - PDO for database interactions

- **Database**:
  - MySQL / MariaDB

- **Others**:
  - [Tailwind CSS](https://tailwindcss.com/) for styling
  - [Emoji](https://emojipedia.org/) for visual attributes

## Contributing

Contributions are welcome! If you'd like to improve the Noids-Blend-Calculator, please follow these steps:

1. **Fork the Repository**

   Click the [Fork](https://github.com/saintpetejackboy/Noids-Blend-Calculator/fork) button at the top right of this page.

2. **Clone Your Fork**

   ```bash
   git clone https://github.com/your-username/Noids-Blend-Calculator.git
   ```

3. **Create a New Branch**

   ```bash
   git checkout -b feature/YourFeatureName
   ```

4. **Make Your Changes**

   Implement your feature or bug fix.

5. **Commit Your Changes**

   ```bash
   git commit -m "Add feature: Your Feature Description"
   ```

6. **Push to Your Fork**

   ```bash
   git push origin feature/YourFeatureName
   ```

7. **Submit a Pull Request**

   Go to the original repository and click the "New Pull Request" button. Describe your changes and submit.

## License

This project is licensed under the [MIT License](LICENSE).

## Contact

**SaintPeteJackBoy**

- GitHub: [@saintpetejackboy](https://github.com/saintpetejackboy)
- Email: [saintpetejackboy.com](mailto:saintpetejackboy@gmail.com)

For any inquiries or support, please feel free to reach out!

---

*Happy blending! 🌿*