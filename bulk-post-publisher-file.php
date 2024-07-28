<?php
use PhpOffice\PhpSpreadsheet\IOFactory;

function bulk_post_publisher_process_csv($file_path) {
    if (($handle = fopen($file_path, "r")) !== FALSE) {
        // Get the column headers from the first row
        $headers = fgetcsv($handle, 1000, ",");

        // Loop through the CSV rows
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            // Create an associative array combining headers and data
            $post_data = array_combine($headers, $data);
            create_post_from_data($post_data);
        }

        fclose($handle);
    }
}

function bulk_post_publisher_process_excel($file_path) {
    $spreadsheet = IOFactory::load($file_path);
    $worksheet = $spreadsheet->getActiveSheet();
    $rows = $worksheet->toArray();

    // Get the column headers from the first row
    $headers = array_shift($rows);

    // Loop through the Excel rows
    foreach ($rows as $row) {
        // Create an associative array combining headers and data
        $post_data = array_combine($headers, $row);
        create_post_from_data($post_data);
    }
}

function create_post_from_data($post_data) {
    // Prepare post data
    $new_post = array(
        'post_title'    => $post_data['Tools'],
        'post_content'  => $post_data['Long Description'],
        'post_excerpt'  => $post_data['Short Description'],
        'post_status'   => 'publish', // You can set it to 'draft' if you want to review before publishing
        'post_author'   => 1, // Replace with the author ID
        'post_type'     => 'ai-tool', // Custom post type
    );

    // Insert the post into the database and get the post ID
    $post_id = wp_insert_post($new_post);

    // Add post meta fields
    if (!is_wp_error($post_id)) {
        update_field('tool_url', $post_data['Links'], $post_id);

        // Handle custom taxonomies
        wp_set_object_terms($post_id, $post_data['Category'], 'tool-category');
        wp_set_object_terms($post_id, $post_data['Free/Paid'], 'tool-plan');
        wp_set_object_terms($post_id, explode(',', $post_data['Tags']), 'tool-tag');

        // Handle featured image
        if (!empty($post_data['Image'])) {
            set_featured_image($post_id, $post_data['Image']);
        }
    }
}

function set_featured_image($post_id, $image_url) {
    $upload_dir = wp_upload_dir();
    $image_data = file_get_contents($image_url);
    $filename = basename($image_url);
    if (wp_mkdir_p($upload_dir['path'])) {
        $file = $upload_dir['path'] . '/' . $filename;
    } else {
        $file = $upload_dir['basedir'] . '/' . $filename;
    }
    file_put_contents($file, $image_data);

    $wp_filetype = wp_check_filetype($filename, null);
    $attachment = array(
        'post_mime_type' => $wp_filetype['type'],
        'post_title' => sanitize_file_name($filename),
        'post_content' => '',
        'post_status' => 'inherit'
    );
    $attach_id = wp_insert_attachment($attachment, $file, $post_id);
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    $attach_data = wp_generate_attachment_metadata($attach_id, $file);
    wp_update_attachment_metadata($attach_id, $attach_data);
    set_post_thumbnail($post_id, $attach_id);
}
?>
