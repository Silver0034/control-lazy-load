<?php

// Set the plugin's namespace
namespace jlCLL;

// Disallow direct access to the plugin
if (!defined('ABSPATH')) die('You shall not pass.');

if (!defined('JLCLL_OPTION_KEY_DISABLE_SITE_WIDE')) return;
if (!defined('JLCLL_OPTION_KEY_DISABLE_PER_POST')) return;

/**
 * The class to handle adding settings to the post editor
 * 
 * @since 1.0.0
 * @version 1.0.0
 */
class ImageBlockEditor
{
    const SCRIPT_KEY = 'jlcll-block-editor';
    /**
     * The constructor for the class
     * 
     * @since 1.0.0
     * @version 1.0.0
     */
    public function __construct()
    {
        // If the user is an admin, construct the admin hooks
        add_action('enqueue_block_editor_assets', [$this, 'enqueue_scripts']);
        // Register the meta key for the post editor
        add_action('init', [$this, 'register_meta_key']);

        // Add a filter to disable lazy load site-wide
        add_action('render_block', [$this, 'render_block_add_loading_eager'], 10, 2);

        // Add a filter to disable lazy load site-wide
        add_filter('wp_img_tag_add_loading_attr', [$this, 'disable_lazy_load_image_block'], 10, 3);
    }

    /**
     * Add admin script to the post editor
     * 
     * @since 1.0.0
     * @version 1.0.0
     */
    public function enqueue_scripts()
    {
        // Add the script to the post editor
        wp_enqueue_script(
            $this::SCRIPT_KEY,
            plugin_dir_url(JLCLL_PLUGIN_FILE) . 'js/block-editor.js',
            ['wp-plugins', 'wp-edit-post', 'wp-components', 'wp-data', 'wp-compose', 'wp-element'],
            null,
            true
        );

        // Get if the user has disabled lazy load for the current post
        $post_id = get_the_ID();
        $disabled_per_post = get_post_meta($post_id, JLCLL_OPTION_KEY_DISABLE_PER_POST, true);

        // Localize the script with your data
        wp_localize_script(
            $this::SCRIPT_KEY,
            'jlcllBlockData',
            [
                'disabledPerPost' => $disabled_per_post,
                'disabledSiteWide' => get_option(JLCLL_OPTION_KEY_DISABLE_SITE_WIDE, false),
                'postEditorKey' => JLCLL_OPTION_KEY_DISABLE_PER_POST,
            ]
        );
    }

    /**
     * Register the meta key for the post editor
     * 
     * @since 1.0.0
     * @version 1.0.0
     */
    public static function register_meta_key()
    {
        $post_types = get_post_types(['public' => true]);

        foreach ($post_types as $post_type) {
            register_post_meta($post_type, JLCLL_OPTION_KEY_DISABLE_IMAGE_BLOCK, array(
                'show_in_rest' => true,
                'single' => true,
                'type' => 'string',
            ));
        }
    }

    /**
     * Modify image block HTML to add loading="eager" to <img> tag if the <figure> tag has loading="eager"
     * 
     * @since 1.0.0
     * @version 1.0.0
     */
    public function render_block_add_loading_eager($block_content, $block)
    {
        // If not an image block, stop
        if ($block['blockName'] !== 'core/image') return $block_content;

        // If the block doesn't contain 'loading="eager"', stop
        if (strpos($block_content, 'loading="eager"') === false) return $block_content;

        // Add loading="eager" to the <img> tag
        $block_content = str_replace('<img', '<img loading="eager"', $block_content);

        return $block_content;
    }

    /**
     * Disable lazy load per image block.
     * 
     * @since 1.0.0
     * @version 1.0.0
     */
    public function disable_lazy_load_image_block($value, $tag, $context)
    {
        // If not an image tag, stop
        if (strpos($tag, '<img') === false) return $value;

        // Stop if tag doesn't contain 'loading="eager"'
        if (strpos($tag, 'loading="eager"') === false) return $value;

        // Disable lazy load for the image block
        return 'eager';
    }
}
