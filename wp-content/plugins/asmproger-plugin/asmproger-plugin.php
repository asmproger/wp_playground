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
        //here i try to handle post request from my custom form
        add_action('wp_ajax_amsp_propose', [$this, 'addPropose']);
        add_action('wp_ajax_nopriv_amsp_propose', [$this, 'addPropose']);

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

        // custom hook for displaying book meta info
        add_action('show_meta_custom_hook', array('WP_Asmproger_Plugin', 'echoMeta'));
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

    /**
     * Options method. Check, if we should show book ISBN
     * @return bool
     */
    public static function checkShowISBN()
    {
        $show = get_option('asmp_settings_show_isbn', 'on');
        return $show == 'on';
    }

    /**
     * Options method. Check, if we should show book author prefix
     * @return bool
     */
    public static function checkShowPrefix()
    {
        $show = get_option('asmp_settings_show_prefix', 'on');
        return $show == 'on';
    }

    /**
     * Options method. Returns admin prefix for book author.
     * @return string|void
     */
    public static function getAdminPrefix()
    {
        $setting = get_option('asmp_settings_author_prefix');
        return isset($setting) ? esc_attr($setting) : '';
    }

    /**
     * Custom table creation on plugin activation.
     */
    public static function createTable()
    {
        global $wpdb;
        $tableName = "{$wpdb->prefix}asmp_proposes";

        if ($wpdb->get_var("SHOW TABLES LIKE '$tableName'") != $tableName) {
            $sql = <<<SQL
CREATE TABLE IF NOT EXISTS {$tableName} (
id mediumint(9) NOT NULL AUTO_INCREMENT,
email tinytext NOT NULL,
currency tinytext NOT NULL,
price mediumint(9) NOT NULL,
UNIQUE KEY id (id)
);
SQL;
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
    }

    /**
     * Allowed currencies
     * @var array
     */
    private $allowedCurrencies = ['pound' => '&pound;', 'usd' => '&dollar;', 'euro' => '&euro;'];

    /**
     * Returns allowed currencies
     * @return array
     */
    public function getAllowedCurrencies()
    {
        return $this->allowedCurrencies;
    }

    /**
     * simple params validation for 'add proposition' form
     * @param $params
     * @return mixed
     */
    private function prepareParams($params)
    {
        if (array_key_exists('email', $params)) {
            $params['email'] = filter_var($params['email'], FILTER_VALIDATE_EMAIL);
        } else {
            $params['email'] = '';
        }

        if (array_key_exists('currency', $params)) {
            if (!array_key_exists($params['currency'], $this->allowedCurrencies)) {
                $params['currency'] = '';
            }
        } else {
            $params['currency'] = 'usd';
        }

        if (array_key_exists('currency', $params)) {
            $params['price'] = intval($params['price']);
        } else {
            $params['price'] = 0;
        }
        return $params;
    }

    /**
     * Callback for ajax call
     * Adding proposition to our custom table
     */
    public function addPropose()
    {
        // there is no post data?
        if (!$_POST) {
            return;
        }

        // params validation
        $params = $this->prepareParams($_POST);

        global $wpdb;
        $prefix = $wpdb->prefix;

        extract($params);

        // if some problem with params
        if (!$price || !$currency || !$email) {
            echo json_encode([
                'success' => false,
                'code' => 1
            ]);
            die;
        }

        // simple insert. we have no unique fileds in our table (except of id), so any email could be used multiple times
        $sql = "INSERT INTO {$prefix}asmp_proposes (`email`, `currency`, `price`) VALUES ('{$email}', '{$currency}', {$price});";

        if ($result = $wpdb->query($sql)) {
            echo json_encode([
                'success' => true
            ]);
            die;
        } else {
            echo json_encode([
                'success' => false,
                'code' => 2
            ]);
            die;
        }
    }

    /**
     * return all propositions, ordered by price
     * @return array|null|object
     */
    public function getPropositions()
    {
        global $wpdb;
        $items = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}asmp_proposes ORDER BY price DESC");
        return $items;
    }

    /**
     * return formatted html string with proposition
     * @param $item
     * @return string
     */
    public function getProposition($item)
    {
        $result = "<span class='asmp-proposition-email'>{$item->email}:</span> <span class='asmp-proposition-price'> " . $item->price . $this->getCurrencyLabel($item->currency) . '</span>';
        return $result;
    }

    /**
     * returns currency html code by currency name
     * @param $currency
     * @return mixed|string
     */
    public function getCurrencyLabel($currency)
    {
        if (array_key_exists($currency, $this->allowedCurrencies)) {
            return $this->allowedCurrencies[$currency];
        }
        return '';
    }
}

$asmpInstance = WP_Asmproger_Plugin::getInstance();
$asmpInstance->init();

register_activation_hook(__FILE__, ['WP_Asmproger_Plugin', 'createTable']);