<?php
/*
Plugin Name: Rainscript Slider
Description: Create center-focused sliders using selected pages and filter by page categories.
Version: 1.6
Author: Kasingye Viva
*/

if (!defined('ABSPATH')) exit;

// Register Custom Post Type
function rainscript_register_slider_cpt() {
    register_post_type('viva_slider', [
        'label' => 'Rainscript Sliders',
        'public' => false,
        'show_ui' => true,
        'supports' => ['title'],
        'menu_icon' => 'dashicons-slides',
    ]);
}
add_action('init', 'rainscript_register_slider_cpt');

// Meta Boxes
function rainscript_add_meta_boxes() {
    add_meta_box('viva_slider_pages', 'Select Pages and Categories for Slider', 'rainscript_pages_box', 'viva_slider', 'normal', 'default');
    add_meta_box('viva_shortcode_box', 'Shortcode', 'rainscript_shortcode_box', 'viva_slider', 'side', 'default');
}
add_action('add_meta_boxes', 'rainscript_add_meta_boxes');

// Page Selection Box
function rainscript_pages_box($post) {
    $selected_pages = get_post_meta($post->ID, '_viva_pages', true) ?: [];
    $selected_categories = get_post_meta($post->ID, '_viva_categories', true) ?: [];

    $pages = get_pages();
    $categories = get_categories(['hide_empty' => false]);

    wp_nonce_field('rainscript_save_data', 'rainscript_nonce');

    echo "<p>Select the pages to include:</p><div style='max-height:200px; overflow:auto;'>";
    foreach ($pages as $page) {
        $checked = in_array($page->ID, $selected_pages) ? 'checked' : '';
        echo "<label><input type='checkbox' name='viva_pages[]' value='{$page->ID}' $checked> {$page->post_title}</label><br>";
    }
    echo "</div>";

    echo "<p><strong>Or select categories (optional):</strong></p>";
    echo "<div style='max-height:200px; overflow:auto;'>";
    foreach ($categories as $cat) {
        $checked = in_array($cat->term_id, $selected_categories) ? 'checked' : '';
        echo "<label><input type='checkbox' name='viva_categories[]' value='{$cat->term_id}' $checked> {$cat->name}</label><br>";
    }
    echo "</div>";
}

// Save Meta
function rainscript_save_slider_meta($post_id) {
    // Verify nonce
    if (!isset($_POST['rainscript_nonce']) || !wp_verify_nonce($_POST['rainscript_nonce'], 'rainscript_save_data')) {
        return;
    }

    // Check if this is an autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check user permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save pages
    if (isset($_POST['viva_pages'])) {
        update_post_meta($post_id, '_viva_pages', array_map('intval', $_POST['viva_pages']));
    } else {
        delete_post_meta($post_id, '_viva_pages');
    }

    // Save categories
    if (isset($_POST['viva_categories'])) {
        update_post_meta($post_id, '_viva_categories', array_map('intval', $_POST['viva_categories']));
        // Remove old single category meta if it exists
        delete_post_meta($post_id, '_viva_category');
    } else {
        delete_post_meta($post_id, '_viva_categories');
    }
}
add_action('save_post', 'rainscript_save_slider_meta');

// Shortcode Display Box
function rainscript_shortcode_box($post) {
    echo "<p>Use this shortcode to show this slider:</p>";
    echo "<code>[viva_page_slider id=\"{$post->post_name}\"]</code>";
}

// Enqueue Scripts and Styles
function rainscript_enqueue_assets() {
    wp_enqueue_style('slick-css', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css');
    wp_enqueue_style('slick-theme-css', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css');
    wp_enqueue_script('slick-js', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', ['jquery'], null, true);

    // Inline JavaScript for center active slide
    wp_add_inline_script('slick-js', "
        jQuery(document).ready(function($) {
            $('.viva-page-slider').on('init reInit afterChange', function(event, slick){
                var slides = $(this).find('.slick-slide');
                slides.removeClass('active-slide');
                slides.each(function() {
                    if ($(this).hasClass('slick-center')) {
                        $(this).addClass('active-slide');
                    }
                });
            }).slick({
                centerMode: true,
                centerPadding: '20%',
                slidesToShow: 1,
                autoplay: true,
                autoplaySpeed: 3000,
                arrows: true,
                dots: false,
                focusOnSelect: true,
                prevArrow: '<button type=\"button\" class=\"slick-prev\">←</button>',
                nextArrow: '<button type=\"button\" class=\"slick-next\">→</button>',
                responsive: [
                    {
                        breakpoint: 768,
                        settings: {
                            centerPadding: '10%',
                        }
                    }
                ]
            });
        });
    ");

    // Inline CSS
    wp_add_inline_style('slick-css', '
        .viva-page-slider {
            margin: 40px 0;
            position: relative;
        }
        .viva-slide {
            transition: all 0.5s ease-in-out;
            opacity: 0.3;
            transform: scale(0.85);
            padding: 0 0 30px;
            background: #f9f9f9;
            border-radius: 8px;
            box-shadow: none;
            text-align: center;
            height: auto;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .slick-center .viva-slide {
            opacity: 1;
            transform: scale(1);
            background: #fff;
            padding: 10px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            height: 400px;
        }
        .viva-slide img {
            width: 100%;
            max-height: 300px;
            object-fit: cover;
            border-radius: 8px;
        }
        .viva-slide h3 {
            margin-top: 15px;
            color: #9e6a16;
            font-size: 28px;
        }
        .viva-slide a.viva-button {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 16px;
            background: #bb832a !important;
            color: #fff;
            text-decoration: none;
            transition: background 0.3s ease;
        }
        .viva-slide a.viva-button:hover {
            background: #005177!important;
        }
        .slick-prev, .slick-next {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: #0073aa;
            border: none;
            color: #fff;
            font-size: 20px;
            padding: 10px 12px;
            cursor: pointer;
            border-radius: 50%;
            z-index: 1000;
        }
        .slick-prev:hover, .slick-next:hover {
            background: #005177;
        }
        .slick-prev { left: -40px; }
        .slick-next { right: -40px; }

        @media (max-width: 768px) {
            .slick-prev { left: -20px; }
            .slick-next { right: -20px; }
        }

        .viva-slide.active-slide {
            opacity: 1 !important;
        }
    
        [type="button"], [type="submit"], button {
            display: inline-block;
            font-weight: 400;
            color: none!important;
            text-align: center;
            -webkit-user-select: none;
            user-select: none;
            background-color: transparent!important;
            border: none!important;
            padding: .5rem 1rem;
            font-size: 1rem;
            border-radius: 3px;
            transition: all .3s;
        }

        .viva-slide {
            opacity: 0.3;
            transform: scale(0.75);
            transition: all 0.4s ease;
            z-index: 1;
        }

        .viva-slide.active-slide {
            opacity: 1 !important;
            transform: scale(1) translateY(-10px);
            z-index: 10;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
    ');
}
add_action('wp_enqueue_scripts', 'rainscript_enqueue_assets');
add_action('admin_enqueue_scripts', 'rainscript_enqueue_assets');

// Shortcode Handler
function rainscript_page_slider_shortcode($atts) {
    $atts = shortcode_atts(['id' => ''], $atts);
    if (empty($atts['id'])) return '<p>No slider ID given.</p>';

    $slider = get_page_by_path($atts['id'], OBJECT, 'viva_slider');
    if (!$slider) return '<p>Slider not found.</p>';

    $page_ids = get_post_meta($slider->ID, '_viva_pages', true);
    $category_ids = get_post_meta($slider->ID, '_viva_categories', true);
    
    // Backward compatibility - check for old single category field
    if (empty($category_ids)) {
        $old_category = get_post_meta($slider->ID, '_viva_category', true);
        if ($old_category) {
            $category_ids = [$old_category];
        }
    }

    $args = [
        'post_type' => 'page',
        'posts_per_page' => -1,
        'orderby' => 'post__in'
    ];

    if (!empty($page_ids)) {
        $args['post__in'] = $page_ids;
    } elseif (!empty($category_ids)) {
        $args['category__in'] = $category_ids;
    } else {
        return '<p>No pages or categories selected for this slider.</p>';
    }

    $query = new WP_Query($args);

    ob_start();
    if ($query->have_posts()) {
        echo '<div class="viva-page-slider">';
        while ($query->have_posts()) {
            $query->the_post();
            echo '<div class="viva-slide">';
            if (has_post_thumbnail()) {
                the_post_thumbnail('full');
            }
            echo '<h3>' . get_the_title() . '</h3>';
            echo '<a href="' . get_permalink() . '" class="viva-button">Read More</a>';
            echo '</div>';
        }
        echo '</div>';
    } else {
        echo '<p>No pages found.</p>';
    }
    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('viva_page_slider', 'rainscript_page_slider_shortcode');