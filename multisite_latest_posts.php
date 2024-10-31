<?php
/*
Plugin Name: Multisite Latest Posts Widget
Description: A sidebar widget to display latest posts from all blogs
Author: Tristan Min
Version: 1.4
Plugin URI: http://www.wpclue.com/development/
Author URI: http://www.wpclue.com/
*/

/*  Copyright 2010  Tristan Min (www.keentricks.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

### widget start
function  multisite_Latest_Posts_Widget_init(){
	if ( !function_exists('register_sidebar_widget') )
		return;

	function multisite_Latest_Posts_Widget($args) {
            extract($args);

            $options = get_option('multisite_Latest_Posts_Widget');
            $title = $options['title'];
            $limit = $options['limit'];
            if ($limit<1) $limit = 5;

            $before_widget = '<li class="widget-container widget_ms_latest_posts" id="ms_latest_posts">';
            $after_widget = '</li>';
            $before_title = '<h3 class="widget-title">';
            $after_title = '</h3>';

            echo $before_widget . $before_title . $title . $after_title;
            echo get_ms_latest_posts($limit) . $after_widget;
	}

        function multisite_Latest_Posts_Widget_control(){
            $options = get_option('multisite_Latest_Posts_Widget');
            if ( !is_array($options) )
                $options = array('title'=>'Multisite Latest Posts', 'limit'=>'5');

            if ( $_POST['ms-latest-posts-submit'] ) {
                $options['title'] = strip_tags(stripslashes($_POST['title']));
                $options['limit'] = strip_tags(stripslashes($_POST['limit']));
                update_option('multisite_Latest_Posts_Widget', $options);
            }

            $title = htmlspecialchars($options['title'], ENT_QUOTES);
            $limit = htmlspecialchars($options['limit'], ENT_QUOTES);

            echo '<p style="text-align:right;">
                            <label for="title">' . __('Title:') . '
                            <input style="width: 200px;" id="title" name="title" type="text" value="'.$title.'" />
                            </label></p>';
            echo '<p style="text-align:right;">
                            <label for="limit">' . __('Limit:') . '
                            <input style="width: 200px;" id="limit" name="limit" type="text" value="'.$limit.'" />
                            </label></p>';
            echo '<input type="hidden" id="ms-latest-posts-submit" name="ms-latest-posts-submit" value="1" />';
        }

	register_sidebar_widget(array('Multisite Latest Posts', 'widgets'), 'multisite_Latest_Posts_Widget');

	register_widget_control(array('Multisite Latest Posts', 'widgets'), 'multisite_Latest_Posts_Widget_control', 300, 200);

        ### Output
        if(!function_exists('get_ms_latest_posts')){
            function get_ms_latest_posts($limit) {
                global $wpdb;
                $output = '';

                $request = $wpdb->prepare("SELECT ".$wpdb->base_prefix."ms_posts.*
                   FROM ".$wpdb->base_prefix."ms_posts
                   WHERE post_type='post' ORDER BY post_date DESC LIMIT ".$limit);

                $results = $wpdb->get_results($request);

                if(!empty($results)){
                    $output = '<ul>';

                    foreach($results as $post){
                        if (empty($post->post_excerpt)) {
                             $post->post_excerpt = explode(" ",strrev(substr(strip_tags($post->post_content), 0, 100)),2);
                             $post->post_excerpt = strrev($post->post_excerpt[1]);
                             $post->post_excerpt.= " ...";
                        }
                        
                        switch_to_blog($post->blog_id);
                        $post_link = get_permalink($post->ID);
                        restore_current_blog();

                        $output .= '<li>';
                        $output .= '<a rel="bookmark" href="'.$post_link.'"><strong class="title">'.$post->post_title.'</strong></a>';
                        $output .= '<br>'.$post->post_excerpt;
                        $output .= '</li>';
                    }

                    $output .= '</ul>';
                }
                return $output;
            }
        }
}
add_action('widgets_init', 'multisite_Latest_Posts_Widget_init');


### For short_code
if(!function_exists('get_sc_ms_latest_posts')){
            function get_sc_ms_latest_posts($limit, $style) {
                global $wpdb;
                $output = '';

                if($style == 'list'){
                    $outer_start = '<ul class="mslp_ul">';
                    $outer_end = '</ul>';
                    $inner_start = '<li class="mslp_li">';
                    $inner_end = '</li>';
                }
                elseif($style == 'div'){
                    $outer_start = '<div class="mslp_wrapper_div">';
                    $outer_end = '</div>';
                    $inner_start = '<div class="mslp_post_div">';
                    $inner_end = '</div>';
                }

                $request = $wpdb->prepare("SELECT ".$wpdb->base_prefix."ms_posts.*
                   FROM ".$wpdb->base_prefix."ms_posts
                   WHERE post_type='post' ORDER BY post_date DESC LIMIT ".$limit);

                $results = $wpdb->get_results($request);

                if(!empty($results)){
                    $output = $outer_start;

                    foreach($results as $post){
                        if (empty($post->post_excerpt)) {
                             $post->post_excerpt = explode(" ",strrev(substr(strip_tags($post->post_content), 0, 100)),2);
                             $post->post_excerpt = strrev($post->post_excerpt[1]);
                             $post->post_excerpt.= " ...";
                             $post->post_excerpt = wpautop($post->post_excerpt);
                        }

                        switch_to_blog($post->blog_id);
                        $post_link = get_permalink($post->ID);
                        restore_current_blog();

                        $output .= $inner_start;
                        $output .= '<a rel="bookmark" href="'.$post_link.'"><strong class="mslp_title">'.$post->post_title.'</strong></a>';
                        $output .= '<br>'.$post->post_excerpt;
                        $output .= $inner_end;
                    }

                    $output .= $outer_end;
                }
                return $output;
            }
        }

add_shortcode('mslp', 'do_mslp');
function do_mslp($attr) {
    $attr = shortcode_atts(array('limit' => 5, 'style' => 'list'), $attr);
    $limit = $attr['limit'];
    $style = $attr['style'];

    if ($limit<1) $limit = 5;
    if ($style != 'list' && $style !='div') $style = 'list';
    
    return get_sc_ms_latest_posts($limit, $style);
}

register_activation_hook( __FILE__, 'latest_post_build_views_add' );
/**
 * Builds a view that contains posts from all blogs.
 * Views are built by activate_blog, desactivate_blog, archive_blog, unarchive_blog, delete_blog and wpmu_new_blog hooks.
 */
add_action ('wpmu_new_blog', 'latest_post_build_views_add');
add_action ('delete_blog', 'latest_post_build_views_drop', 10, 1);
add_action ('archive_blog', 'latest_post_build_views_drop', 10, 1);
add_action ('unarchive_blog', 'latest_post_build_views_unarchive', 10, 1);
add_action ('activate_blog', 'latest_post_build_views_activate', 10, 1);
add_action ('deactivate_blog', 'latest_post_build_views_drop', 10, 1);

if(!function_exists('latest_post_build_views_drop')) {
        function latest_post_build_views_drop($trigger) {
            global $wpdb;

            $blogs = $wpdb->get_results( $wpdb->prepare("SELECT blog_id, domain, path FROM {$wpdb->blogs} WHERE blog_id != {$trigger} AND site_id = {$wpdb->siteid} AND archived = '0' AND mature = '0' AND spam = '0' AND deleted = '0' ORDER BY registered DESC"));
            latest_post_v_query($blogs);
        }
}

if(!function_exists('latest_post_build_views_add')) {
        function latest_post_build_views_add() {
            global $wpdb;

            $blogs = $wpdb->get_results( $wpdb->prepare("SELECT blog_id, domain, path FROM {$wpdb->blogs} WHERE site_id = {$wpdb->siteid} AND archived = '0' AND mature = '0' AND spam = '0' AND deleted = '0' ORDER BY registered DESC"));
            latest_post_v_query($blogs);
        }
}

if(!function_exists('latest_post_build_views_activate')) {
        function latest_post_build_views_activate($trigger) {
            global $wpdb;

            $blogs = $wpdb->get_results( $wpdb->prepare("SELECT blog_id, domain, path FROM {$wpdb->blogs} WHERE (blog_id = {$trigger} AND archived = '0' AND mature = '0' AND spam = '0') OR (site_id = {$wpdb->siteid} AND public = '1' AND archived = '0' AND mature = '0' AND spam = '0' AND deleted = '0') ORDER BY registered DESC"));

            latest_post_v_query($blogs);
        }
}

if(!function_exists('latest_post_build_views_unarchive')) {
        function latest_post_build_views_unarchive($trigger) {
            global $wpdb;

            $blogs = $wpdb->get_results( $wpdb->prepare("SELECT blog_id, domain, path FROM {$wpdb->blogs} WHERE (blog_id = {$trigger} AND deleted = '0' AND mature = '0' AND spam = '0') OR (site_id = {$wpdb->siteid} AND public = '1' AND archived = '0' AND mature = '0' AND spam = '0' AND deleted = '0') ORDER BY registered DESC"));
            latest_post_v_query($blogs);
        }
}

if(!function_exists('latest_post_v_query')) {
        function latest_post_v_query($blogs) {
            global $wpdb;

            $i = 0;
            $posts_query = '';

            foreach ($blogs as $blog) {
                if ($i != 0) {
                    $posts_query    .= ' UNION ';
                }

                if($blog->blog_id == 1) {
                        $posts_query    .= " (SELECT '{$blog->blog_id}' AS blog_id, '{$blog->domain}' AS domain, '{$blog->path}' AS path, posts{$blog->blog_id}.* FROM {$wpdb->base_prefix}posts posts{$blog->blog_id} WHERE posts{$blog->blog_id}.post_type != 'revision' AND posts{$blog->blog_id}.post_status = 'publish') ";
                } else {
                        $posts_query    .= " (SELECT '{$blog->blog_id}' AS blog_id, '{$blog->domain}' AS domain, '{$blog->path}' AS path, posts{$blog->blog_id}.* FROM {$wpdb->base_prefix}{$blog->blog_id}_posts posts{$blog->blog_id} WHERE posts{$blog->blog_id}.post_type != 'revision' AND posts{$blog->blog_id}.post_status = 'publish') ";
                }
                $i++;
            }

                $v_query1  = "CREATE OR REPLACE VIEW `{$wpdb->base_prefix}ms_posts` AS ".$posts_query;
                $wpdb->query($wpdb->prepare($v_query1));
        }
}
?>