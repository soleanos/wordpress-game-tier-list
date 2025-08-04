<?php
/**
 * Plugin Name: WP Tier List Maker
 * Description: v1.71 - Fixed filter "Palier" localization handle.
 * Version: 1.71
 * Author: ChatGPT
 */
defined('ABSPATH') || exit;
require_once __DIR__.'/includes/cpt-tier-list.php';
require_once __DIR__.'/includes/metabox-tier-builder.php';
require_once __DIR__.'/includes/shortcode-tier-list.php';
add_action('wp_enqueue_scripts', function(){
    wp_enqueue_style('tierlist-css', plugins_url('assets/css/style.css', __FILE__));
    wp_enqueue_script('tierlist-js', plugins_url('assets/js/script.js', __FILE__), [], null, true);
});
add_action('admin_enqueue_scripts', function($hook){
    if(in_array($hook, ['post.php','post-new.php'])){
        wp_enqueue_style('tierlist-admin-css', plugins_url('assets/css/admin.css', __FILE__));
        wp_enqueue_script('tierlist-admin-js', plugins_url('assets/js/admin.js', __FILE__), ['jquery','jquery-ui-sortable'], null, true);
    }
});

require_once __DIR__.'/includes/filters-loader.php';

require_once __DIR__.'/includes/force-names-global.php';

require_once __DIR__.'/includes/tiers-global.php';

// Localize current post paliers for filter (v1.71 fixed handle)
add_action('wp_enqueue_scripts', function(){
    if(is_singular('tier_list')){
        global $post;
        $rows = get_post_meta($post->ID, '_wptlm_tiers', true);
        $tiers = array();
        if(is_array($rows)){
            foreach($rows as $r){
                if(!empty($r['name'])){
                    $tiers[] = sanitize_text_field($r['name']);
                }
            }
        }
        wp_localize_script('wptlm-filters', 'WPTLM_TIERS_CURRENT', $tiers);
    }
}, 20);

?>