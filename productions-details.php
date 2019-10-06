<?php
/*
Plugin Name: Theatre Wordpress
Plugin URI: linkedin.com/in/jamile-carvalho-kollar-14793947
Description: Declares a plugin that will create custom post types for theatre content management.
Version: 1.0
Author: Jamile Kollar
Author URI: linkedin.com/in/jamile-carvalho-kollar-14793947
License: GPLv2
*/



define('NUM_REVIEWS', 10);
define('NUM_CREATIVES', 10);



function create_theatre_post_types()
{

    register_post_type('productions',
        array(
            'labels' => array(
                'name' => 'Productions',
                'singular_name' => 'Production',
                'add_new' => 'Add New',
                'add_new_item' => 'Add New Production',
                'edit' => 'Edit',
                'edit_item' => 'Edit Production',
                'new_item' => 'New Production',
                'view' => 'View',
                'view_item' => 'View Production',
                'search_items' => 'Search Productions',
                'not_found' => 'No Productions found',
                'not_found_in_trash' => 'No Productions found in Trash',
                'parent' => 'Parent Production'
            ),

            'public' => true,
            'menu_position' => 15,
            'supports' => array('title', 'editor','revisions','thumbnail'),
            'taxonomies' => array('post_tag', 'category'),
            'menu_icon' => 'dashicons-format-aside',
            'has_archive' => true
        )
    );

}

add_action('init', 'create_theatre_post_types');

function add_custom_taxonomies()
{
    // Add new "Venues" taxonomy to Posts
    register_taxonomy('venue',
        array('productions', 'events'),
        array(
            // Hierarchical taxonomy (like categories)
            'hierarchical' => true,
            // This array of options controls the labels displayed in the WordPress Admin UI
            'labels' => array(
                'name' => _x('Venues', 'taxonomy general name'),
                'singular_name' => _x('Venue', 'taxonomy singular name'),
                'search_items' => __('Search Venue'),
                'all_items' => __('All Venues'),
                'parent_item' => __('Parent Venue'),
                'parent_item_colon' => __('Parent Venue:'),
                'edit_item' => __('Edit Venue'),
                'update_item' => __('Update Venue'),
                'add_new_item' => __('Add New Venue'),
                'new_item_name' => __('New Venue Name'),
                'menu_name' => __('Venues'),
            ),
            // Control the slugs used for this taxonomy
            'rewrite' => array(
                'slug' => 'venues', // This controls the base slug that will display before each term
                'with_front' => false, // Don't display the category base before "/venues/"
                'hierarchical' => true // This will allow URL's like "/locations/boston/cambridge/"
            ),
        ));
    // Add new "Status" taxonomy to Posts
    
    register_taxonomy('flag',
        array('productions'),
        array(
            // Hierarchical taxonomy (like categories)
            'hierarchical' => true,
            // This array of options controls the labels displayed in the WordPress Admin UI
                'labels' => array(
                'name' => _x('Flags', 'taxonomy general name'),
                'singular_name' => _x('Flag', 'taxonomy singular name'),
                'search_items' => __('Search Flag'),
                'all_items' => __('All Flags'),
                'parent_item' => __('Parent Flag'),
                'parent_item_colon' => __('Parent Flag:'),
                'edit_item' => __('Edit Flag'),
                'update_item' => __('Update Flag'),
                'add_new_item' => __('Add New Flag'),
                'new_item_name' => __('New Flag Name'),
                'menu_name' => __('Flags'),
            ),
            // Control the slugs used for this taxonomy
                'rewrite' => array(
                'slug' => 'flags', // This controls the base slug that will display before each term
                'with_front' => false, // Don't display the category base before "/venues/"
                'hierarchical' => true // This will allow URL's like "/locations/boston/cambridge/"
            ),
        ));
    register_taxonomy('reviewer',
        array('reviews'),
        array(
            // Hierarchical taxonomy (like categories)
            'hierarchical' => true,
            // This array of options controls the labels displayed in the WordPress Admin UI
            'labels' => array(
                'name' => _x('Reviewers', 'taxonomy general name'),
                'singular_name' => _x('Reviewer', 'taxonomy singular name'),
                'search_items' => __('Search Reviewer'),
                'all_items' => __('All Reviewers'),
                'parent_item' => __('Parent Reviewer'),
                'parent_item_colon' => __('Parent Reviewer:'),
                'edit_item' => __('Edit Reviewer'),
                'update_item' => __('Update Reviewer'),
                'add_new_item' => __('Add New Reviewer'),
                'new_item_name' => __('New Reviewer Name'),
                'menu_name' => __('Reviewers'),
            ),
            // Control the slugs used for this taxonomy
            'rewrite' => array(
                'slug' => 'reviewers', // This controls the base slug that will display before each term
                'with_front' => false, // Don't display the category base before "/venues/"
                'hierarchical' => true // This will allow URL's like "/locations/boston/cambridge/"
            ),
        ));
}

add_action('init', 'add_custom_taxonomies', 0);

/**
 * Calls all of the functions responsible for rendering each of the meta boxes
 * use in the theme.
 *
 * @since 1.0.0
 *
 */

function add_multiple_meta_boxes()
{
    add_meta_box('creatives_meta_box',
        'Creatives',
        'display_creatives_meta_box',
        'productions', 'normal', 'high');

    add_meta_box('date_rage_meta_box',
        'Date Range',
        'display_date_range_meta_box',
        'productions', 'normal', 'high');

    add_meta_box('partners_credits_meta_box',
        'Credits',
        'display_partners_credits_meta_box',
        'productions', 'normal', 'high');

    add_meta_box('awards_meta_box',
        'Awards',
        'display_awards_meta_box',
        'productions', 'normal', 'high');

    add_meta_box('reviews_meta_box',
        'Reviews',
        'display_reviews_meta_box',
        'productions', 'normal', 'high');

}

add_action('add_meta_boxes', 'add_multiple_meta_boxes');

/**
 * Renders the meta box that renders the production quotes with reviews and reviewers list
 *
 */
function display_reviews_meta_box($production)
{
    foreach (range(0, NUM_REVIEWS - 1) as $i) {
        $review_field_name = 'production_review_' . $i;
        $reviewer_field_name = 'production_reviewer_' . $i;
        $review = esc_html(get_post_meta($production->ID, $review_field_name, true));
        $reviewer = esc_html(get_post_meta($production->ID, $reviewer_field_name, true));
        ?>
        <table>
            <th>
                <tr>
                    <td>Review</td>
                    <td>Reviewer</td>
                </tr>
            </th>
            <tr>
                <td><input type="text" size="80" name="<?php echo $review_field_name; ?>" value="<?php echo $review; ?>"/></td>
                <td><input type="text" size="20" name="<?php echo $reviewer_field_name; ?>" value="<?php echo $reviewer; ?>"/></td>
            </tr>
        </table>

        <?php
    }
}

/**
 * Renders the meta box that renders the production creatives list
 *
 */
function display_creatives_meta_box($production)
{


foreach (range(0, NUM_CREATIVES - 1) as $i) {
        $creative_field_role = 'creative_role_' . $i;
        $creative_field_name = 'creative_name_' . $i;
        $creative_role = esc_html(get_post_meta($production->ID, $creative_field_role, true));
        $creative_name = esc_html(get_post_meta($production->ID, $creative_field_name, true));
        ?>
        <table>
            <th>
                <tr>
                    <td>Creative Role</td>
                    <td>Creative Name</td>
                </tr>
            </th>
            <tr>
                <td><input type="text" size="40" name="<?php echo $creative_field_role; ?>" value="<?php echo $creative_role; ?>"/></td>
                <td><input type="text" size="60" name="<?php echo $creative_field_name; ?>" value="<?php echo $creative_name; ?>"/></td>
            </tr>
        </table>

        <?php
    }
}

/**
 * Renders the meta box for displaying the contact information (such as email,
 * phone number, etc.).
 *
 * @since 1.0.0
 */
function display_date_range_meta_box($production)
{
    // Calls `add_meta_box` for implementation
    $production_from_date = esc_html(get_post_meta($production->ID, 'production_from_date', true)); //'2011-02-22'; //get_post_meta($production->ID, 'from_date', true);
    $production_to_date = esc_html(get_post_meta($production->ID, 'production_to_date', true)); //'2011-02-28'; //get_post_meta($production->ID, 'to_date', true);

    ?>
    <table>
        <tr>
            <td>From</td>
            <td><input type="text" size="50" name="production_from_date" value="<?php echo $production_from_date; ?>"/></td>
            <td>To</td>
            <td><input type="text" size="50" name="production_to_date" value="<?php echo $production_to_date; ?>"/></td>
        </tr>
    </table>
<?php
}

function display_partners_credits_meta_box($production)
{
    // Calls `add_meta_box` for implementation
    $production_credits = esc_html(get_post_meta($production->ID, 'production_credits', true));
    ?>
    <table>
        <tr>
            <td>Production Credits</td>
            <td><input type="textarea" size="100" name="production_credits" value="<?php echo $production_credits; ?>"/></td>
        </tr>
    </table>
<?php
}

function display_awards_meta_box($production)
{
    $production_nomination = esc_html(get_post_meta($production->ID, 'production_nomination', true));
    $production_award = esc_html(get_post_meta($production->ID, 'production_award', true));
    ?>
    <table>
        <tr>
            <td>Production Nomination</td>
            <td><input type="text" size="80" name="production_nomination"
                       value="<?php echo $production_nomination; ?>"/></td>
        </tr>
        <tr>
            <td>Production Award</td>
            <td><input type="text" size="80" name="production_award" value="<?php echo $production_award; ?>"/></td>
        </tr>
    </table>
<?php

}

//saving multiple fields

function add_production_fields($production_id, $production)
{
    // Check post type for production reviews
    if ($production->post_type == 'productions') {

        $review_meta_fields = array();
        foreach (range(0, NUM_REVIEWS - 1) as $i) {
            array_push($review_meta_fields, 'production_review_' . $i);
            array_push($review_meta_fields, 'production_reviewer_' . $i);
        }

        $creative_meta_fields = array();
        foreach (range(0, NUM_CREATIVES - 1) as $i) {
            array_push($creative_meta_fields, 'creative_role_' . $i);
            array_push($creative_meta_fields, 'creative_name_' . $i);
        }


        // Store data in post meta table if present in post data
        $meta_fields = array(
            'production_from_date',
            'production_to_date',
            'production_credits',
            'production_nomination',
            'production_award');

        foreach (array_merge($review_meta_fields, $creative_meta_fields, $meta_fields) as $field) {
            if (isset($_POST[$field]) && $_POST[$field] != '') {
                update_post_meta($production_id, $field, $_POST[$field]);
            }
        }
    }
}

add_action('save_post', 'add_production_fields', 10, 2);

?>
