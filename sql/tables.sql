-- Create 'ingredients' table
CREATE TABLE ingredients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create 'attributes' table
CREATE TABLE attributes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    emoji VARCHAR(10) NOT NULL,
    color VARCHAR(7) NOT NULL  -- Hex color code
);

-- Create 'ingredient_attributes' table (Many-to-Many relationship)
CREATE TABLE ingredient_attributes (
    ingredient_id INT NOT NULL,
    attribute_id INT NOT NULL,
    FOREIGN KEY (ingredient_id) REFERENCES ingredients(id),
    FOREIGN KEY (attribute_id) REFERENCES attributes(id),
    PRIMARY KEY (ingredient_id, attribute_id)
);

-- Create 'keywords' table
CREATE TABLE keywords (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ingredient_id INT NOT NULL,
    keyword VARCHAR(255) NOT NULL,
    FOREIGN KEY (ingredient_id) REFERENCES ingredients(id),
    INDEX(keyword)
);

-- Create 'blends' table
CREATE TABLE blends (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create 'blend_ingredients' table
CREATE TABLE blend_ingredients (
    blend_id INT NOT NULL,
    ingredient_id INT NOT NULL,
    grams DECIMAL(10,3) DEFAULT 0,
    price DECIMAL(10,2) DEFAULT 0,
    FOREIGN KEY (blend_id) REFERENCES blends(id),
    FOREIGN KEY (ingredient_id) REFERENCES ingredients(id),
    PRIMARY KEY (blend_id, ingredient_id)
);

-- Create 'blend_images' table
CREATE TABLE blend_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    blend_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (blend_id) REFERENCES blends(id)
);

-- Create 'blend_notes' table
CREATE TABLE blend_notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    blend_id INT NOT NULL,
    note TEXT,
    email VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (blend_id) REFERENCES blends(id)
);
