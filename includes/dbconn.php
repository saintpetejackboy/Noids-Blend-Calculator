<?php
// This has to go to your configuration file with your database in it to access $pdo-> 
include('/var/www/dbconn/mako/mr.php');

/*
  Database Structure for `mr`:

  Table: `attributes`
    - id (int, primary key, auto-increment)
    - name (varchar, name of the attribute)
    - emoji (varchar, emoji symbol for the attribute)
    - color (varchar, color code for the attribute)

  Table: `blends`
    - id (int, primary key, auto-increment)
    - name (varchar, name of the blend)
    - notes (text, additional notes about the blend)
    - created_at (timestamp, automatically set to the current timestamp)
    - updated_at (timestamp, automatically updated to current timestamp on any update)

  Table: `blend_images`
    - id (int, primary key, auto-increment)
    - blend_id (int, foreign key referencing `blends.id`)
    - image_path (varchar, file path of the image)
    - uploaded_at (timestamp, automatically set to the current timestamp)

  Table: `blend_ingredients`
    - blend_id (int, foreign key referencing `blends.id`, part of composite primary key)
    - ingredient_id (int, foreign key referencing `ingredients.id`, part of composite primary key)
    - grams (decimal, amount of the ingredient in grams)
    - price (decimal, price of the ingredient)

  Table: `blend_notes`
    - id (int, primary key, auto-increment)
    - blend_id (int, foreign key referencing `blends.id`)
    - note (text, notes about the blend)
    - email (varchar, email associated with the note)
    - created_at (timestamp, automatically set to the current timestamp)

  Table: `ingredients`
    - id (int, primary key, auto-increment)
    - name (varchar, name of the ingredient)
    - description (text, description of the ingredient)
    - created_at (timestamp, automatically set to the current timestamp)
    - updated_at (timestamp, automatically updated to current timestamp on any update)

  Table: `ingredient_attributes`
    - ingredient_id (int, foreign key referencing `ingredients.id`, part of composite primary key)
    - attribute_id (int, foreign key referencing `attributes.id`, part of composite primary key)

  Foreign Key Constraints:
    - `blend_images.blend_id` references `blends.id`
    - `blend_ingredients.blend_id` references `blends.id`
    - `blend_ingredients.ingredient_id` references `ingredients.id`
    - `blend_notes.blend_id` references `blends.id`
    - `ingredient_attributes.ingredient_id` references `ingredients.id`
    - `ingredient_attributes.attribute_id` references `attributes.id`
*/
