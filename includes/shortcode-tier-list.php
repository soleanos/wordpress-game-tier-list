<?php
add_shortcode('tierlist', function($atts){
    $atts = shortcode_atts(['id'=>get_the_ID()], $atts);
    $data = get_post_meta($atts['id'], '_tierbuilder_data', true);
    $obj = $data ? json_decode($data, true) : ['groups'=>[], 'elements'=>[], 'rarities'=>[], 'tiers'=>[]];
    if(isset($obj['groups'][0]) && is_string($obj['groups'][0])){
        $obj['groups'] = array_map(function($g){ return ['name'=>$g,'color'=>'#ffffff']; }, $obj['groups']);
    }
    if(!isset($obj['elements'])) $obj['elements'] = [];
    if(!isset($obj['rarities'])) $obj['rarities'] = [];
    foreach($obj['tiers'] as &$t){
        if(!isset($t['color']) || !$t['color']) $t['color'] = '#e74c3c';
        if(!isset($t['items'])) $t['items'] = [];
    }
    $groups = $obj['groups'];
    $elements = $obj['elements'];
    $rarities = $obj['rarities'];
    $tiers = $obj['tiers'];
    ob_start();
    include __DIR__.'/../templates/tier-list-template.php';
    return ob_get_clean();
});
