<?php
/**
 * Plugin Name: Bulk Post Publisher
 * Description: Publishes multiple posts from a CSV or Excel file.
 * Version: 1.2
 * Author: Your Name
 */

// Prevent direct access to the file
if (!defined('ABSPATH')) {
    exit;
}

// Include Composer autoloader
require 'vendor/autoload.php';

// Include the CSV and Excel processing functionality
include_once plugin_dir_path(__FILE__) . 'bulk-post-publisher-file.php';

// Add admin menu
add_action('admin_menu', 'bulk_post_publisher_menu');

function bulk_post_publisher_menu() {
    add_menu_page(
        'Bulk Post Publisher',
        'Bulk Post Publisher',
        'manage_options',
        'bulk-post-publisher',
        'bulk_post_publisher_page'
    );
}

// Admin page content
function bulk_post_publisher_page() {
    ?>
    <div class="wrap">
        <h1>Bulk Post Publisher</h1>
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="file" accept=".csv, .xls, .xlsx" required>
            <?php submit_button('Upload and Publish'); ?>
        </form>
    </div>
    <?php

    // Handle file upload
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_FILES['file']['tmp_name'])) {
        $file_path = $_FILES['file']['tmp_name'];
        $file_type = $_FILES['file']['type'];

        if ($file_type == 'application/vnd.ms-excel' || $file_type == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
            bulk_post_publisher_process_excel($file_path);
        } else {
            bulk_post_publisher_process_csv($file_path);
        }

        echo '<div class="updated"><p>Posts published successfully!</p></div>';
    }
}
?>
