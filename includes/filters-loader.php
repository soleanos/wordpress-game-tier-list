<?php
if(!defined('ABSPATH')) exit;
add_action('wp_enqueue_scripts', function(){
  $base = plugins_url('', dirname(__FILE__));
  wp_enqueue_style('wptlm-filters', $base.'/assets/filters/filters.css', [], '1.66');
  wp_enqueue_script('wptlm-filters', $base.'/assets/filters/filters.js', [], '1.66', true);
}, 100);
