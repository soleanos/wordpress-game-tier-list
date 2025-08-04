<?php
if(!defined('ABSPATH')) exit;

/**
 * Try to fetch global tier (palier) list.
 * We check:
 *  - option 'wptlm_tiers_global' (array or comma string)
 *  - meta key on any tier_list post '_wptlm_tiers' (array)
 *  - fallback: scan existing tier rows in front (not ideal, but ok)
 */
function wptlm_get_global_tiers(){
    $tiers = [];

    $opt = get_option('wptlm_tiers_global');
    if(!empty($opt)){
        if(is_array($opt)){
            foreach($opt as $t){ $t=trim($t); if($t) $tiers[$t]=true; }
        }else{
            foreach(explode(',', $opt) as $t){ $t=trim($t); if($t) $tiers[$t]=true; }
        }
    }

    // meta scan
    $lists = get_posts(['post_type'=>'tier_list','numberposts'=>-1,'post_status'=>'any']);
    foreach($lists as $tl){
        $rows = get_post_meta($tl->ID, '_wptlm_tiers', true);
        if(is_array($rows)){
            foreach($rows as $r){
                if(!empty($r['name'])){
                    $tiers[ trim($r['name']) ] = true;
                }
            }
        }
    }

    return array_keys($tiers);
}

add_action('wp_enqueue_scripts', function(){
    $tiers = wptlm_get_global_tiers();
    wp_localize_script('wptlm-filters', 'WPTLM_TIERS_GLOBAL', $tiers);
}, 99);
