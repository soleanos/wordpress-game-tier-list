<?php
if(!defined('ABSPATH')) exit;

/**
 * Try to detect global force list:
 * - If CPT 'wptlm_force' exists, use its titles.
 * - Else if option 'wptlm_forces_global' exists, use it.
 * - Else scan meta of tier_list posts for stored forces arrays.
 */
function wptlm_get_global_force_names(){
    $names = [];

    // 1) CPT
    if(post_type_exists('wptlm_force')){
        $posts = get_posts(['post_type'=>'wptlm_force','numberposts'=>-1,'post_status'=>'publish']);
        foreach($posts as $p){
            $n = trim($p->post_title);
            if($n) $names[$n] = true;
        }
    }

    // 2) Option
    $opt = get_option('wptlm_forces_global');
    if(!empty($opt['names'])){
        foreach(explode(',', $opt['names']) as $n){
            $n = trim($n);
            if($n) $names[$n] = true;
        }
    }

    // 3) Meta scan (fallback)
    $lists = get_posts(['post_type'=>'tier_list','numberposts'=>-1,'post_status'=>'any']);
    foreach($lists as $tl){
        $data = get_post_meta($tl->ID, '_wptlm_data', true); // guess key
        if(!$data) continue;
        if(!empty($data['characters'])){
            foreach($data['characters'] as $char){
                if(!empty($char['forces'])){
                    foreach($char['forces'] as $f){
                        if(!empty($f['name'])){
                            $names[ trim($f['name']) ] = true;
                        }
                    }
                }
            }
        }
    }

    return array_keys($names);
}

add_action('wp_enqueue_scripts', function(){
    $names = wptlm_get_global_force_names();
    wp_localize_script('wptlm-filters', 'WPTLM_FORCE_NAMES_GLOBAL', $names);
}, 99);
