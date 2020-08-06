<?php
/*
Plugin Name: Remote Thumbnail
Plugin URI: http://wordpress.org/plugins/remote-thumbnail
Description: Lightweight plugin to use remote images for post thumbnails and featured image. Enter remote image url into custom field 'remote_thumbnail' of any post.
Version: 1.1
Author: Samuel Diethelm
Author URI: http://profiles.wordpress.org/samueldiethelm
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

class remote_thumbnail_plugin{
    public static function thumbnail_html($html, $post_ID, $post_image_id, $size, $attr){
        $src = get_post_meta($post_ID,'remote_thumbnail',true);

        if(!$src)
            return $html;

        global $_wp_additional_image_sizes;

        $w = get_option($size.'_size_w',0);
        $h = get_option($size.'_size_h',0);
        $c = (bool)get_option($size.'_crop',0);

        if(isset($_wp_additional_image_sizes[$size]))
            $size = $_wp_additional_image_sizes[$size];
        else
            $size = array('width'=>$w,'height'=>$h,'crop'=>$c);

        $attr['style'] = sprintf('width:%s ; height:%s ; background-image:url(%s); background-size:cover; background-position:center; display:inline-block;',	$size['width'].'px',$size['height'].'px',$src);

        $html = '<div';

        foreach ( $attr as $name => $value ) {
            $html .= " $name=" . '"' . $value . '"';
        }

        $html .= '></div>';

        return $html;
    }

    public static function return_false_thumbnail_id($value, $object_id, $meta_key, $single ){
        if(!$single || $meta_key != '_thumbnail_id')
            return $value;

        $src = get_post_meta($object_id,'remote_thumbnail',true);
        if(!$src)
            return $value;

        return -1;
    }
}
add_filter('post_thumbnail_html', array('remote_thumbnail_plugin','thumbnail_html'), 11, 5);
add_filter('get_post_metadata' , array('remote_thumbnail_plugin','return_false_thumbnail_id'),10,4);
