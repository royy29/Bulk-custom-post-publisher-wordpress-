# Bulk Post Publisher Plugin for WordPress

## Overview

The Bulk Post Publisher Plugin is a comprehensive WordPress plugin designed to automate the process of creating multiple posts from Excel, CSV files, and Airtable. It supports custom post types, fields, and taxonomies, enabling flexible and consistent content management. The plugin integrates seamlessly with the WordPress media library, allowing for easy handling of images associated with the posts.

![Screenshot 2024-07-30 015615](https://github.com/user-attachments/assets/3b818412-d541-40db-9867-49d118bb6fd3)


## Features

- **Multi-Source File Support:** Automatically detects and processes Excel, CSV files, and integrates with Airtable for post creation.
- **Custom Post Types and Fields:** Supports custom post types and allows for the addition of custom fields and taxonomies.
- **Image Handling:** Utilizes the WordPress media library to set featured images from provided URLs, ensuring efficient image management.
- **Data Sanitization and Security:** Implements comprehensive sanitization and validation to ensure data integrity and security.
- **User-Friendly Interface:** Provides an intuitive admin interface for uploading files and managing posts.

## Installation

### Prerequisites

1. **WordPress Version:** Ensure your WordPress installation is up-to-date. This plugin is compatible with WordPress 5.0 and above.
2. **PHP Version:** Requires PHP 7.2 or higher.
3. **Composer:** Composer is required to manage dependencies.

### Steps

1. **Download the Plugin:**
   - Clone or download the plugin from the repository.

2. **Install Dependencies:**
   - Navigate to the plugin directory and run `composer install` to install necessary dependencies, including PhpSpreadsheet for Excel file processing.

3. **Upload the Plugin:**
   - Upload the plugin folder to the `/wp-content/plugins/` directory of your WordPress installation.

4. **Activate the Plugin:**
   - Go to the WordPress Admin Dashboard, navigate to Plugins, and activate the "Bulk Post Publisher" plugin.

## Usage

### Admin Interface

1. **Accessing the Plugin:**
   - After activation, navigate to the WordPress Admin Dashboard. Under the menu, you will find a new item called "Bulk Post Publisher."

2. **Uploading Files:**
   - Click on "Bulk Post Publisher" to open the plugin's main interface.
   - Choose a file (Excel, CSV, or connect to Airtable) using the file input. The plugin accepts `.xls`, `.xlsx`, and `.csv` file formats.

3. **Publishing Posts:**
   - After selecting the file, click the "Upload and Publish" button. The plugin will process the file and create posts accordingly.

### Configuration and Customization

The plugin includes hardcoded configurations for handling custom post types, taxonomies, and fields. To adapt the plugin to different requirements, modifications to specific parts of the code are necessary.

#### Custom Post Type, Custom Fields, and Taxonomies

The plugin is designed to work with custom post types and related taxonomies and fields. Below are the relevant sections of the code where these elements are defined:

```php
// File: bulk-post-publisher-file.php

function create_post_from_data($post_data) {
    // Prepare post data
    $new_post = array(
        'post_title'    => sanitize_text_field($post_data['Tools']), // Default Post Field
        'post_content'  => sanitize_textarea_field($post_data['Long Description']), // Default Post Field
        'post_excerpt'  => sanitize_textarea_field($post_data['Short Description']), // Default Post Field
        'post_status'   => 'publish', // You can set it to 'draft' if you want to review before publishing
        'post_author'   => 1, // Replace with the author ID
        'post_type'     => 'ai-tool', // Custom Post Type
    );

    // Insert the post into the database and get the post ID
    $post_id = wp_insert_post($new_post);

    // Add post meta fields (Custom Fields)
    if (!is_wp_error($post_id)) {
        update_post_meta($post_id, 'tool_url', esc_url($post_data['Links'])); // Custom Field
        
        // Handle custom taxonomies
        if (!empty($post_data['Category'])) {
            wp_set_object_terms($post_id, sanitize_text_field($post_data['Category']), 'tool-category'); // Taxonomy
        }
        if (!empty($post_data['Free/Paid'])) {
            wp_set_object_terms($post_id, sanitize_text_field($post_data['Free/Paid']), 'tool-plan'); // Taxonomy
        }
        if (!empty($post_data['Tags'])) {
            wp_set_object_terms($post_id, array_map('sanitize_text_field', explode(',', $post_data['Tags'])), 'tool-tag'); // Taxonomy

        // Handle image URL
        if (!empty($post_data['Image'])) {
            set_featured_image_from_url($post_id, esc_url($post_data['Image'])); // Default Post Field
        }
    }
}
```

## Hardcoded Fields and Customization Instructions

To customize the plugin for different post types, fields, and taxonomies, the following modifications are required:

### Custom Post Type

- **Hardcoded Slug:** `'post_type' => 'ai-tool'`
- **Modification:** Change `'ai-tool'` to the desired custom post type slug, e.g., `'post_type' => 'custom-post-type-slug'`.

### Default Post Fields

- **Title:** `'post_title' => sanitize_text_field($post_data['Tools'])`
- **Content:** `'post_content' => sanitize_textarea_field($post_data['Long Description'])`
- **Excerpt:** `'post_excerpt' => sanitize_textarea_field($post_data['Short Description'])`
- **Image:** `set_featured_image_from_url($post_id, esc_url($post_data['Image']))`

**Modification:** Update the array keys (e.g., `['Tools']`, `['Long Description']`, etc.) to match the column headers in the CSV or Excel file for corresponding fields.

### Custom Fields (Meta Fields)

- **Field Name:** `'tool_url'`
- **Hardcoded Slug:** `update_post_meta($post_id, 'tool_url', esc_url($post_data['Links']))`
- **Modification:** Change `'tool_url'` to the appropriate custom field key, and adjust `['Links']` to the corresponding column name.

### Taxonomies

- **Taxonomy Slugs:** `'tool-category'`, `'tool-plan'`, `'tool-tag'`
- **Hardcoded Slugs:**
  ```php
  wp_set_object_terms($post_id, sanitize_text_field($post_data['Category']), 'tool-category');
  wp_set_object_terms($post_id, sanitize_text_field($post_data['Free/Paid']), 'tool-plan');
  wp_set_object_terms($post_id, array_map('sanitize_text_field', explode(',', $post_data['Tags'])), 'tool-tag');
  ```
-  **Modification:** Update the taxonomy slugs (e.g., `'tool-category'`) to your specific taxonomy keys. Ensure the array keys (`['Category']`, `['Free/Paid']`, `['Tags']`) align with the column headers in your data file.

## Dependencies

- **PhpSpreadsheet:** Used for handling Excel file processing.
- **WordPress Functions:** Utilized for file handling, media library integration, and post management.

## Error Handling and Logging

The plugin logs errors and warnings to the WordPress debug log. Ensure that WP_DEBUG is enabled in your `wp-config.php` file for development purposes. Common errors include issues with file parsing, invalid URLs for images, and problems connecting to Airtable.

## Security Considerations

The plugin implements rigorous sanitization and validation processes to ensure data integrity. Only authorized users (with appropriate permissions) can access the plugin's functionality.

## Contribution

We welcome contributions to enhance this plugin. Please fork the repository, make your changes, and submit a pull request.

## License

This plugin is open-source and available under the MIT License.

## Support

For support and inquiries, please contact subhroneelroy5@gmail.com.

