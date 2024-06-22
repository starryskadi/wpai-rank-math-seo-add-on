<?php 
/*
Plugin Name: WP All Import - Rank Math SEO Add-On 
Description: Import the variation images with WP ALL IMPORT 
Version: 1.0
Author: Starry Skadi
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
include 'rapid-addon.php';

class WPAI_RANK_MATH_SEO {
    protected static $instance;

    protected $add_on;

    static public function get_instance() {
        if ( self::$instance == NULL ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    protected function __construct() {        
        $this->add_on = new RapidAddon( 'Rank Math SEO', 'wpai_rank_math_seo_addon' );

        $this->add_on->add_field( 'rank_math_title', 'Meta Title', 'text' );
        $this->add_on->add_field( 'rank_math_description', 'Meta Description', 'text' );
        $this->add_on->add_field('rank_math_focus_keyword', 'Focus Keywords', 'text',  null, "Seperate each keyword by comma", true, 'keyword1,keyword2');
        $this->add_on->add_field('rank_math_facebook_image', 'Social Image', 'image');
        $this->add_on->add_field('rank_math_robots', 'Meta Robots', 'radio', ['1' => 'noindex', '2' => 'index']);

        $this->add_on->set_import_function([ $this, 'import' ]);
        
    
        add_action( 'init', [ $this, 'init' ] );
    }

    public function import( $post_id, $data, $import_options, $article) {        
        update_post_meta( $post_id, 'rank_math_title', $data['rank_math_title'] );
        update_post_meta( $post_id, 'rank_math_description', $data['rank_math_description'] );
        update_post_meta( $post_id, 'rank_math_focus_keyword', $data['rank_math_focus_keyword'] );

        $image_id = $data['rank_math_facebook_image-image']['attachment_id'];
        $image_url = wp_get_attachment_url($attachment_id);

        update_post_meta( $post_id, 'rank_math_facebook_image', $image_url);
        update_post_meta( $post_id, 'rank_math_facebook_image_id', $image_id);

        if ($data['rank_math_robots'] == '1') {

            $robots_index = array(
                "index"
            );
    
            $advanced_robots_index = array(
                "max-snippet" => "-1",
                "max-video-preview" => "-1",
                "max-image-preview" => "large"
            );

            update_post_meta( $post_id, 'rank_math_robots', $robots_index );
            update_post_meta( $post_id, 'rank_math_advanced_robots', $advanced_robots_index );

        } else {

            $robots_no_index = array(
                "nofollow",
                "noindex",
                "noarchive",
                "nosnippet",
                "noimageindex"
            );

            update_post_meta( $post_id, 'rank_math_robots', $robots_no_index );
            update_post_meta( $post_id, 'rank_math_advanced_robots', array() );
        }
    }

    public function init() {
        $this->add_on->run(array(
            'plugins' => array( 
                'seo-by-rank-math/rank-math.php',
                // 'wp-all-export/wp-all-export.php'
            )
        ));
    }
}

function wpai_rank_math_seo_activate() {
    if ( is_plugin_active( 'wp-all-import/wp-all-import.php' ) || is_plugin_active( 'wp-all-import-pro/wp-all-import-pro.php' ) || is_plugin_active( 'wp-all-import-pro/wp-all-import-pro.php' )) {
        // The other plugin is not active, throw an error
        
    } else {
        wp_die(
            'This plugin requires the WP ALL IMPORT (or) WP ALL IMPORT PRO to be installed and activated. Please install and activate it first.',
            'Plugin Dependency Check',
            array(
                'back_link' => true, // This will provide a back link to the plugins page
            )
        );
    }
}

// Register the activation hook
register_activation_hook( __FILE__, 'wpai_rank_math_seo_activate' );

WPAI_RANK_MATH_SEO::get_instance();