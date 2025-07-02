<?php
/**
 * WCR REST API Class
 * Handles all REST API related functionality
 */

namespace WCR\API;

class WCR_REST_API {
    /**
     * Initialize the REST API functionality
     */
    public function init() {
        // Filter posts by language in REST API
        add_filter('rest_post_query', array($this, 'filter_posts_by_language_in_rest'), 10, 2);
        
        // Add clean content to REST API response
        add_filter('rest_prepare_post', array($this, 'add_clean_content_to_rest'), 10, 3);
        
        // Register custom endpoints
        add_action('rest_api_init', array($this, 'register_rest_endpoints'));
    }

    /**
     * Filter posts by language in REST API
     */
    public function filter_posts_by_language_in_rest($args, $request) {
        if (isset($request['lang'])) {
            $lang = sanitize_text_field($request['lang']);
            $args['lang'] = $lang;
        }
        return $args;
    }

    /**
     * Add clean content to REST API response
     */
    public function add_clean_content_to_rest($data, $post, $request) {
        $clean_content = wp_strip_all_tags($data->data['content']['rendered']);
        $clean_content = html_entity_decode($clean_content);
        $data->data['content']['clean'] = $clean_content;
        return $data;
    }

    /**
     * Register custom REST endpoints
     */
    public function register_rest_endpoints() {
        register_rest_route('wcr/v1', '/english-posts', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_english_posts'),
            'permission_callback' => '__return_true'
        ));
    }

    /**
     * Get English posts endpoint callback
     */
    public function get_english_posts($request) {
        $args = array(
            'post_type' => 'post',
            'posts_per_page' => $request['per_page'] ?? 10,
            'paged' => $request['page'] ?? 1,
            'lang' => 'en'
        );

        $query = new \WP_Query($args);
        $posts = array();

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $posts[] = array(
                    'id' => get_the_ID(),
                    'title' => get_the_title(),
                    'content' => array(
                        'rendered' => get_the_content(),
                        'clean' => wp_strip_all_tags(get_the_content())
                    ),
                    'date' => get_the_date('c'),
                    'link' => get_permalink()
                );
            }
            wp_reset_postdata();
        }

        return new \WP_REST_Response($posts, 200);
    }
} 