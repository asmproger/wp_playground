<?php

/**
 * Created by PhpStorm.
 * User: sovkutsan
 * Date: 3/29/18
 * Time: 2:32 PM
 */

/**
 * Widget displaying some info about propositions.
 * Top proposition & total count for one book (on single-book.php)
 * Or total propositions for all books and top proposition with book (for any other page)
 * Class WP_Widget_Propositions
 */
class Asmp_Propositions extends WP_Widget
{
    public function __construct()
    {
        $options = [
            'classname' => 'asmp_propositions',
            'description' => 'Widget displaying some info about propositions.'
        ];
        parent::__construct('asmp_propositions', 'Propositions', $options);
    }

    public function widget($args, $instance)
    {
        $viewHelper = ViewHelper::getInstance();
        if (is_singular('book')) {
            $data = $this->getSinglePagePropositions(get_the_ID());
            $template = 'propositions_widget_single';
        } else {
            $data = $this->getTotalPropositions();
            $template = 'propositions_widget_total';
        }
        $html = $viewHelper->getView($data, $template);
        echo $html;
    }

    private function getSinglePagePropositions($id)
    {
        global $wpdb;
        $prefix = $wpdb->prefix;

        $sqlCnt = "SELECT COUNT(*) as `cnt` FROM `{$prefix}asmp_proposes` WHERE `book_id` = {$id};";
        $sqlTop = "SELECT * FROM `{$prefix}asmp_proposes` WHERE `book_id` = {$id} ORDER BY `price` DESC LIMIT 1;";

        $countResult = $wpdb->get_results($sqlCnt);
        $topResult = $wpdb->get_results($sqlTop);

        $count = $countResult[0]->cnt;
        $top = $topResult[0];

        return [
            'count' => $count,
            'price' => ($top && isset($top->price)) ? $top->price : 0,
            'email' => ($top && isset($top->email)) ? $top->email : 0
        ];
    }

    private function getTotalPropositions()
    {
        global $wpdb;
        $prefix = $wpdb->prefix;

        $sqlCnt = "SELECT COUNT(*) as `cnt` FROM `{$prefix}asmp_proposes`;";
        $sqlTop = "SELECT * FROM `{$prefix}asmp_proposes` ORDER BY `price` DESC LIMIT 1;";

        $countResult = $wpdb->get_results($sqlCnt);
        $topResult = $wpdb->get_results($sqlTop);

        $count = $countResult[0]->cnt;
        $top = $topResult[0];
        $title = '';
        if ($top && $top->book_id) {
            $title = the_title('<a target="_blank" href="' . get_the_permalink($top->book_id). '">', '</a>', false);
        }

        return [
            'count' => $count,
            'price' => $top ? $top->price : 0,
            'email' => $top ? $top->email : 0,
            'title' => $title
        ];
    }

    public function update($new_instance, $old_instance)
    {
        return parent::update($new_instance, $old_instance); // TODO: Change the autogenerated stub
    }

    public function form($instance)
    {
        return parent::form($instance); // TODO: Change the autogenerated stub
    }
}