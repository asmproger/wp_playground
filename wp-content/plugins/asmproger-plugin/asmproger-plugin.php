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

/**
 * Class WP_Asmproger_Plugin
 * Main and only class for test Asmproger plugin
 */
class WP_Asmproger_Plugin
{
    /**
     * Singleton rule!
     * @var null
     */
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

    /**
     * Some WP hooks for our plugin
     */
    public function init()
    {
        // here we create our post type
        add_action('init', ['WP_Asmproger_Plugin', 'registerPostType']);

        // here we create our settings link for autogenerated menu
        add_action('admin_menu', ['WP_Asmproger_Plugin', 'addMenu']);

        // here we create our ettings page
        add_action('admin_init', ['WP_Asmproger_Plugin', 'settingsPage']);

        // just echo html code with isbn & author name
        add_action('storefront_single_post', ['WP_Asmproger_Plugin', 'echoMeta'], 25);

        // just echo html code with isbn & author name through shortcode
        add_shortcode('asmp_book_meta', ['WP_Asmproger_Plugin', 'shortBook']);

    }

    /**
     * html code with isbn & author name
     */
    public static function echoMeta()
    {
        // lets check settings!
        $isbn = self::checkShowISBN() ? get_post_meta(get_the_ID(), 'ISBN', 1) : 'hidden';
        $author = get_post_meta(get_the_ID(), 'Writer', 1);

        if (self::checkShowPrefix()) {
            $author = self::getAdminPrefix() . $author;
        }

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

        echo $meta;
    }

    /**
     * shortcode for html code with isbn & author name
     * @return string
     */
    public static function shortBook()
    {
        // lets check, if there is book id in args?
        $args = func_get_arg(0);
        if (!empty($args) && is_array($args)) {
            if (isset($args['id'])) {
                $id = $args['id'];
                /**
                 * ok, lets get & check book from database
                 * @var WP_Post $post
                 */
                $post = get_post($id);
                if ($post) {
                    $isbn = self::checkShowISBN() ? get_post_meta($id, 'ISBN', 1) : 'hidden';
                    $auth = get_post_meta($id, 'Writer', 1);
                    if (self::checkShowPrefix()) {
                        $auth = self::getAdminPrefix() . $auth;
                    }
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
        return '';
    }

    /**
     * here we create Options menuitem for autogenerated menu
     */
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

    /**
     * Our custom post type registration
     */
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

    /**
     * Our settings page initialization
     */
    public static function settingsPage()
    {
        register_setting('asmp_options', 'asmp_settings_show_isbn');
        register_setting('asmp_options', 'asmp_settings_author_prefix');
        register_setting('asmp_options', 'asmp_settings_show_prefix');

        //onse section for our options
        add_settings_section(
            'asmp_settings_section',
            'Books options',
            function () {
                return 'some result';
            },
            'asmp_options'
        );

        // add check isbn field
        add_settings_field(
            'asmp_settings_show_isbn',
            'Show ISBN?',
            function () { // this callback generate html for our field
                $checked = WP_Asmproger_Plugin::checkShowISBN() ? 'checked' : '';
                $field = <<<FIELD
    <input type="checkbox" name="asmp_settings_show_isbn" {$checked}>
FIELD;
                echo $field;
            },
            'asmp_options',
            'asmp_settings_section'
        );

        add_settings_field(
            'asmp_settings_author_prefix',
            'Author prefix',
            function () {
                $value = WP_Asmproger_Plugin::getAdminPrefix();
                $field = <<<FIELD
    <input type="text" name="asmp_settings_author_prefix" value="{$value}">
FIELD;
                echo $field;
            },
            'asmp_options',
            'asmp_settings_section'
        );

        add_settings_field(
            'asmp_settings_show_prefix',
            'Show author prefix?',
            function () {
                $checked = WP_Asmproger_Plugin::checkShowPrefix() ? 'checked' : '';
                $field = <<<FIELD
    <input type="checkbox" name="asmp_settings_show_prefix" {$checked}>
FIELD;
                echo $field;
            },
            'asmp_options',
            'asmp_settings_section'
        );
    }

    public static function checkShowISBN()
    {
        $show = get_option('asmp_settings_show_isbn', 'on');
        return $show == 'on';
    }

    public static function checkShowPrefix()
    {
        $show = get_option('asmp_settings_show_prefix', 'on');
        return $show == 'on';
    }

    public static function getAdminPrefix()
    {
        $setting = get_option('asmp_settings_author_prefix');
        return isset($setting) ? esc_attr($setting) : '';
    }
}

$asmpInstance = WP_Asmproger_Plugin::getInstance();
$asmpInstance->init();