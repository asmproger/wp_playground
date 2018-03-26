<?php
/*
Plugin Name: Asmproger Plugin
Plugin URI: https://wordpress2.local
Description: My first plugin for WP.
Version: 1.0
Author: asmproger
Author URI: http://wasm.in
*/

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

class WP_Asmproger_Plugin
{
    protected static $_instance = null;

    private function __constructor()
    {

    }

    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function init()
    {
        add_action('admin_menu', ['WP_Asmproger_Plugin', 'addMenu']);
        add_action('storefront_single_post', ['WP_Asmproger_Plugin', 'echoMeta'], 25);

        add_shortcode('asmp_book_meta', ['WP_Asmproger_Plugin', 'shortBook']);
    }

    public static function echoMeta()
    {
        $isbn = get_post_meta(get_the_ID(), 'ISBN', 1);
        $author = get_post_meta(get_the_ID(), 'Writer', 1);

        $meta = <<<META
<table class="book-meta-table">
<tr>
<td class="book-meta-title">ISBN</td>
<td>{$isbn}</td>
</tr>
<tr>
<td class="book-meta-title">Author</td>
<td>{$author}</td>
</tr>
</table>
META;

        return $meta;
    }

    public static function shortBook()
    {
        $args = func_get_arg(0);
        if (!empty($args) && is_array($args)) {
            if (isset($args['id'])) {
                $id = $args['id'];
                /**
                 * @var WP_Post $post
                 */
                $post = get_post($id);
                if ($post) {
                    $isbn = get_post_meta($id, 'ISBN', 1);
                    $auth = get_post_meta($id, 'Writer', 1);
                    $title = get_the_title($id);
                    $html = <<<HTML
<p>
{$title}
<br/>
<small>{$auth}</small>
<br/>
<small>{$isbn}</small>
</p>
HTML;
                    return $html;
                }
            }
        }
        $argsStr = implode(',', $args);
        return 'args - ' . $argsStr;
    }

    public static function addMenu()
    {
        add_submenu_page(
            'edit.php?post_type=book',
            'Books',
            'Books options',
            'manage_options',
            plugin_dir_path(__FILE__) . 'admin/admin-options.php',
            null,
            20
        );
    }

    public static function registerPostType()
    {
        register_post_type(
            'book',
            [
                'labels' => [
                    'name' => __('Books'),
                    'singular_name' => __('Book')
                ],
                'description' => 'Custom pot type for books',
                'public' => true,
                'has_archive' => true,
                'supports' => ['title', 'editor', 'custom-fields', 'thumbnail']
            ]
        );
    }
}

add_action('init', ['WP_Asmproger_Plugin', 'registerPostType']);

$asmpInstance = WP_Asmproger_Plugin::getInstance();
$asmpInstance->init();


//add_action('init', 'asmp_setup_post_type');
function asmp_install()
{
    // trigger our function that registers the custom post type
    asmp_setup_post_type();
    // clear the permalinks after the post type has been registered
    flush_rewrite_rules();
}

//register_activation_hook(__FILE__, 'asmp_install');