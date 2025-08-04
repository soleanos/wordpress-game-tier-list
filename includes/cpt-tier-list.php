<?php
add_action('init', function(){
    register_post_type('tierlist', [
        'label'=>'Tier Lists',
        'public'=>true,
        'show_in_menu'=>true,
        'menu_icon'=>'dashicons-format-gallery',
        'supports'=>['title','thumbnail'],
    ]);
});
