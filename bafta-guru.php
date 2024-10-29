<?php

/**
 * Plugin Name: BAFTA Guru widget
 * Plugin URI: http://guru.bafta.org/guru-wordpress-plugin-and-widget
 * Description: The BAFTA Guru WordPress Plugin and Widget allows you to easily feature Guru Video, Audio and Article content on your own website.
 * Version: 1.0
 * Author: BAFTA
 * Author URI: http://www.bafta.org/guru
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * Add hook to add plugin to settings menu
 * 
 * @since 1.0
 */

add_action('admin_menu', 'baftapromo_admin_actions');

/**
 * Add function to load admin settings pages.
 * 
 * @since 1.0
 */

function baftapromo_admin_actions()

{
    add_options_page('BAFTA Guru Options', 'BAFTA Guru', 'manage_options',
        'bafta-promo-identifier', 'baftapromo_options');

}

/**
 * Add function to call admin settings interface.
 * 
 * @since 1.0
 */

function baftapromo_options()

{
    if (!current_user_can('manage_options'))
    {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }
    include ('baftaguru-admin.php');

}

// Add settings link on plugin page

function your_plugin_settings_link($links) { 
  $settings_link = '<a href="options-general.php?page=bafta-promo-identifier">Settings</a>'; 
  array_unshift($links, $settings_link); 
  return $links; 

}

$plugin = plugin_basename(__FILE__); 

add_filter("plugin_action_links_$plugin", 'your_plugin_settings_link' );

function admin_register_head()

{
    $siteurl = get_option('siteurl');
    $url = $siteurl . '/wp-content/plugins/' . basename(dirname(__file__)) .
        '/style-admin.css';
    echo "<link rel='stylesheet' type='text/css' href='$url' />\n";

}

add_action('admin_head', 'admin_register_head');

/**
 * Add function to get xml category feed.
 * 
 * @since 1.0
 */

function get_bafta_categories_xml()

{
    $xml = simplexml_load_file("http://guru.bafta.org/guru-wordpress-xml-category-list");
    return $xml;

}

/**
 * Add function to get xml feed.
 * 
 * @since 1.0
 */

function get_bafta_xml()

{
    $xml = simplexml_load_file("http://guru.bafta.org/guru-wp-xml-feed");
    return $xml;

}

/**
 * Add function to get all cartegories from xml feed.
 * 
 * @since 1.0
 */

function get_bafta_categories()

{
    $xml = get_bafta_categories_xml();
    $categories = $xml->xpath('node/craft-category');
    $categoriesList = '';
    foreach ($categories as $category)
    {
        if (!empty($category))
        {
            $categoriesList .= $category . ',';
        }
    }
    $categoriesList = substr($categoriesList, 0, -1);
    $categoriesList = explode(',', $categoriesList);
    return $categoriesList;

}

/**
 * Add function to get the sanitised form value version of the category name.
 * 
 * @since 1.0
 */

function get_bafta_categories_value($categoryList)

{
    $categoryList = str_replace(' ', '_', $categoryList);
    $categoryList = str_replace('+', '-', $categoryList);
    $categoryList = strtolower($categoryList);
    return $categoryList;

}

/**
 * Add function to get the name from the sanitised version of the category name.
 * 
 * @since 1.0
 */

function get_bafta_category_name_from_value($category)

{
    $category = str_replace(',', '', $category);
    $category = str_replace('_', ' ', $category);
    $category = str_replace('-', '+', $category);
    $category = strtolower($category);
    return $category;

}

function get_bafta_feed_by_category($categories)

{
    $xml = get_bafta_xml();
    $attribute = '';
    foreach ($categories as $category)
    {

        

        $attribute .= 'contains(craft-categories,"' . $category . '") or ';

    }
 
    $attribute = substr($attribute, 0, -4);

        
    $feed = $xml->xpath('node[' . $attribute . ']');

    return $feed;

}

/**
 * Add function to return unique array values only.
 * 
 * @since 1.0
 */

function super_unique($array)

{
    $result = array_map("unserialize", array_unique(array_map("serialize", $array)));
    foreach ($result as $key => $value)
    {
        if (is_array($value))
        {
            $result[$key] = super_unique($value);
        }
    }
    return $result;

}

/**
 * Add function to widgets_init that'll load our widget.
 * 
 * @since 1.0
 */

add_action('widgets_init', baftapromo_load_widgets);

/**
 * Register our widget.
 *
 * @since 1.0
 */

function baftapromo_load_widgets()

{
    register_widget('BaftaPromo_Widget');

}

function localize_vars()

{
    $categoriesList = get_bafta_categories();
    foreach ($categoriesList as $categoryList)
    {
        $categoryListValue = get_bafta_categories_value($categoryList);
        if (get_option('bafta_promo_' . $categoryListValue))
        {
            $category .= $categoryList . '|';
        }
    }
    $category = substr($category, 0, -1);
    $categories = explode('|', $category);
    $feed = get_bafta_feed_by_category($categories);
    $feed_count = count($feed);
    $myArray = array('scroll' => 1, 'length' => 5, 'start' => 1, 'size' => $feed_count);
    return $myArray;

} //End localize_vars

function my_scripts_method()

{
    wp_register_script('jcarousel_script', WP_PLUGIN_URL .
        '/bafta-guru-widget/lib/jquery.jcarousel.min.js', array('jquery'), '1.0');
    // enqueue the script
    wp_enqueue_script('jcarousel_script');
    // register your script location, dependencies and version
    wp_register_script('bafta-jcarousel_script', WP_PLUGIN_URL .
        '/bafta-guru-widget/lib/jcarousel-bafta.js');
    // enqueue the script
    wp_enqueue_script('bafta-jcarousel_script');
    wp_localize_script('bafta-jcarousel_script', 'jc_options', localize_vars());

}

add_action('wp_enqueue_scripts', 'my_scripts_method');

function load_jcarousel_styles()

{
    wp_register_style('jcarousel_css_ie7', WP_PLUGIN_URL .
        '/bafta-guru-widget/skins/ie7/skin.css', false, '1.0.0');
    wp_enqueue_style('jcarousel_css_ie7');
    wp_register_style('jcarousel_css_skin', WP_PLUGIN_URL .
        '/bafta-guru-widget/skins/bafta/skin.css', false, '1.0.0');
    wp_enqueue_style('jcarousel_css_skin');
    wp_register_style('jcarousel_css', WP_PLUGIN_URL . '/bafta-guru-widget/guru-styles.css', false,
        '1.0.0');
    wp_enqueue_style('jcarousel_css');

}

add_action('wp_print_styles', 'load_jcarousel_styles');

/**
 * Promo Content class.
 *
 * @since 1.0
 */

class BaftaPromo_Widget extends WP_Widget

{
    function __construct()
    {
        /* Widget settings. */
        $widget_options = array('classname' => 'example', 'description' => __('Choose a category to promo.'));
        /* Widget control settings. */
        $control_options = array('width' => 300, 'height' => 350, 'id_base' =>
            'baftapromo_widget');
        parent::WP_Widget('baftapromo_widget', __('BAFTA Promo Widget',
            'baftapromo-widget'), $widget_options, $control_options);
    }
    /**
     * How to display the widget on the screen.
     */
    function widget($args, $instance)
    {
        extract($args);
        $category = '';
        $categoriesList = get_bafta_categories();
        foreach ($categoriesList as $categoryList)
        {
            $categoryListValue = get_bafta_categories_value($categoryList);
            if (get_option('bafta_promo_' . $categoryListValue))
            {
                $category .= $categoryList . '|';
            }
        }
        $category = substr($category, 0, -1);
        $categories = explode('|', $category);
        $feed = get_bafta_feed_by_category($categories);
        $feed_count = count($feed);
        /* Our variables from the widget settings. */
        $show_title = isset($instance['show_title']) ? $instance['show_title'] : false;
        $show_image = isset($instance['show_image']) ? $instance['show_image'] : false;
        $show_link = isset($instance['show_link']) ? $instance['show_link'] : false;
        /* Before widget (defined by themes). */
        echo $before_widget;

?>
        <div id="bafta-guru-widget">
            <!--[if IE 7]>    
                <style type="text/css">
                    .jcarousel-control {display:none;}
                </style>
            <![endif]-->

			<div id="bafta-guru-widget-header">

				<h2><img src="http://guru.bafta.org/sites/learning/files/variant_learning_logo.gif" width="200px" alt="BAFTA Guru logo"/></h2>

				<p>Inspiring Minds in Film, TV and Games</p>

			</div>            
            <div id="mycarousel" class="jcarousel-skin-bafta">
                <ul>
                    <?php
        foreach ($feed as $article)
        {

?>
                        <li>
                            <?php
            if ($show_image)
            {

?>
                                <?php
                if ($show_link)
                {

?>
                                    <a href="<?php
                    echo $article->path;

?>" title="Find out more about <?php
                    echo $article->title;

?>" target="_new"><img src="<?php
                    echo $article->image;

?>" alt="<?php
                    echo $article->title;

?>" /></a>       
                                <?php
                } else
                {

?>
                                   <img src="<?php
                    echo $article->image;

?>" alt="<?php
                    echo $article->title;

?>" />
                                <?php
                }

?>   
                            <?php
            }

?>
                            <?php
            if ($show_title)
            {

?>
                                <?php
                if ($show_link)
                {

?>
                                    <p><a href="<?php
                    echo $article->path;

?>" title="Find out more about <?php
                    echo $article->title;

?>" target="_new"><?php
                    if(strlen($article->title)>32) {
                        echo substr($article->title,0,32).'...';
                    }else{
                        echo $article->title;
                    }

?></a></p>       
                                <?php
                } else
                {

?>
                                    <p><?php
                    echo $article->title;

?></p> 
                                <?php
                }
            }

?>
                        </li>
                    <?php
        }

?>
                </ul>

				<div class="jcarousel-control-container">
                    <div class="jcarousel-control">
    
                        <?php
            $i = 0;
            while ($i < $feed_count)
            {
                $i++;
    ?>
    
                        <a href="#"><?php
                echo $i;
    ?></a>
                        <?php
    
            }
    
    ?>  
    
                    </div>
                </div>   
            </div>
            <p id="guru-more-info"><a href="http://guru.bafta.org" target="_blank">More inspiration from BAFTA Guru</a></p>
        </div>
     <?php
        echo $after_widget;
    }
    /**
     * Update the widget settings.
     */
    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $show_title = $newinstance['show_title'];
        $show_image = $newinstance['show_image'];
        $show_link = $newinstance['show_link'];
        return $new_instance;
    }
    /**
     * Displays the widget settings controls on the widget panel.
     * Make use of the get_field_id() and get_field_name() function
     * when creating your form elements. This handles the confusing stuff.
     */
    function form($instance)
    {

?>
        <!-- Show content type checkbox -->       
        <p>
           <label>
                <input name="<?php
        print $this->get_field_name('show_title');

?>" id="<?php
        print $this->get_field_id('show_title');

?>" type="checkbox" value="1" <?php
        checked($instance['show_title'], 1);

?> />
                <?php
        _e('Show title', 'baftapromo-widget');

?>
           </label>
        </p>

		
        <p>
           <label>
                <input name="<?php
        print $this->get_field_name('show_image');

?>" id="<?php
        print $this->get_field_id('show_image');

?>" type="checkbox" value="1" <?php
        checked($instance['show_image'], 1);

?> />
                <?php
        _e('Show image', 'baftapromo-widget');

?>
           </label>
        </p>
        <p>
           <label>
                <input name="<?php
        print $this->get_field_name('show_link');

?>" id="<?php
        print $this->get_field_id('show_link');

?>" type="checkbox" value="1" <?php
        checked($instance['show_link'], 1);

?> />
                <?php
        _e('Show link', 'baftapromo-widget');

?>
           </label>
        </p>

	<?php
    }

}

?>