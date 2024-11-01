 <?php
/*
Plugin Name: Very Simple SEO Pack
Description: A very simple seo plugin that enables you to add meta box to all of your posts and/or pages.
Version: 1.0
Author: Raghunath Gurjar
Author URI: http://www.raghunathgurjar.wordpress.com/
License: GPLv2
Copyright 2014 Very Simple SEO  raghunath.0087@gmail.com


This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

*/

//Start code for very-simple seo pack

add_action( 'add_meta_boxes', 'add_vssp_meta_box' );

/**
 * Adds the SEO meta box to the page screen
 */
function add_vssp_meta_box()
{
 global $meta_box;
    add_meta_box($meta_box['id'], $meta_box['title'], 'vssp_meta_show_box', $meta_box['page'], $meta_box['context'], $meta_box['priority']);
}

//Define SEO Meta box Fields

  $prefix = 'vssp_';
    $meta_box = array(
    'id' => 'my-meta-box',
    'title' => 'SEO Information <span style="float:right;font-size:10px">Created by <a target="_blank" href="mailto:raghunath.0087@gmail.com">Raghunath Gurjar</a></span>',
    'page' => '',
    'context' => 'normal',
    'priority' => 'high',
    'fields' => array(
    array(
    'name' => 'Meta Title',
    'desc' => '',
    'id' => $prefix . 'title',
    'type' => 'text',
    'std' => ''
    ),
    array(
    'name' => 'Meta Keyword',
    'desc' => '',
    'id' => $prefix . 'keywords',
    'type' => 'text',
    'std' => ''
    ),
    array(
    'name' => 'Meta Description',
    'id' => $prefix . 'description',
    'desc' => '',
    'type' => 'textarea',
    'std' => ''
    ),
    array(
    'name' => 'Open Graph(OG) Meta Title | og:title',
    'desc' => '',
    'id' => $prefix . 'og_title',
    'type' => 'text',
    'std' => ''
    ),
    array(
    'name' => 'Open Graph(OG) Meta Description | og:description',
    'id' => $prefix . 'og_description',
    'desc' => '',
    'type' => 'textarea',
    'std' => ''
    ),
    array(
    'name' => 'Open Graph(OG) Meta Type | og:type',
    'desc' => '',
    'id' => $prefix . 'og_type',
    'type' => 'text',
    'std' => ''
    ),
    array(
    'name' => 'Open Graph(OG) Meta Image Path | og:image',
    'desc' => '',
    'id' => $prefix . 'og_image',
    'type' => 'text',
    'std' => ''
    )
    )
    );

//Display SEO Meta Box
function vssp_meta_show_box()
{
global $meta_box, $post;
    // Use nonce for verification
    echo '<input type="hidden" name="vssp_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';
    echo '';
    foreach ($meta_box['fields'] as $field) {
    // get current post meta data
    $meta = get_post_meta($post->ID, $field['id'], true);
    echo '<p>',
    '<label for="', $field['id'], '">', $field['name'], '</label>','';
    switch ($field['type']) {
    case 'text':
    echo '<input type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" size="30" style="width:97%" />', '<br />', $field['desc'];
    break;
    case 'textarea':
    echo '<textarea name="', $field['id'], '" id="', $field['id'], '" cols="60" rows="4" style="width:97%">', $meta ? $meta : $field['std'], '</textarea>', '<br />', $field['desc'];
    break;
    '</p>';
    }

    }
}

//Save SEO Meta Box fields Value

add_action( 'save_post', 'save_vssp_meta_box' );

function save_vssp_meta_box($post_id) {
global $meta_box;
// verify nonce
if (!wp_verify_nonce($_POST['vssp_meta_box_nonce'], basename(__FILE__))) {
return $post_id;
}
// check autosave
if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
return $post_id;
}
// check permissions
if ('page' == $_POST['post_type'] || 'post' == $_POST['post_type']) {
if (!current_user_can('edit_page', $post_id)) {
return $post_id;
}
} elseif (!current_user_can('edit_post', $post_id)) {
return $post_id;
}
foreach ($meta_box['fields'] as $field) {
$old = get_post_meta($post_id, $field['id'], true);
$new = $_POST[$field['id']];
if ($new && $new != $old) {
update_post_meta($post_id, $field['id'], $new);
} elseif ('' == $new && $old) {
delete_post_meta($post_id, $field['id'], $old);
}
}
}

//Add Seo Details in header

add_action('wp_head','add_metavalue_header',5);

function add_metavalue_header($metaContent)
{
global $meta_box, $post;

$metaKeyword=get_post_meta($post->ID,'vssp_keywords',true);

$metaDesc=get_post_meta($post->ID,'vssp_description',true);

$ogmetaTitle=get_post_meta($post->ID,'vssp_og_title',true);

$OgmetaDesc=get_post_meta($post->ID,'vssp_og_description',true);
$OgmetaImage=get_post_meta($post->ID,'vssp_og_image',true);
$Ogmetatype=get_post_meta($post->ID,'vssp_og_type',true);

$metaContent="\n<!-- Created By Raghunath Gurjar -->\n";
$metaContent.="\n<!-- SEO Meta Content -->\n";
if($metaKeyword!=''){
$metaContent.="<meta name='keywords' content='".$metaKeyword."'>\n";}

if($metaDesc!=''){
$metaContent .="<meta name='description' content='".$metaDesc."'>\n";}

$metaContent.="\n<!-- Open Graph Meta Content -->\n\n";

if($ogmetaTitle!=''){
$metaContent .="<meta property='og:title' content='".$ogmetaTitle."'>\n";}
if($OgmetaDesc!=''){
$metaContent .="<meta property='og:description' content='".$OgmetaDesc."'>\n";}

if($OgmetaImage!=''){
$metaContent .="<meta property='og:image' content='".$OgmetaImage."'>\n";}

if($Ogmetatype!=''){
$metaContent .="<meta property='og:type' content='".$Ogmetatype."'>\n\n";}

echo $metaContent;
}


add_filter('wp_title','vssp_page_title',10,2);
	
// override the meta title using hooks
function vssp_page_title($title){
	global $meta_box, $post;
    $metaTit=get_post_meta($post->ID,'vssp_title',true); //define your title here
    
    if($metaTit==''){
    $title=$post->post_title;}
    else
    {
		 $title=$metaTit;
		}
    
    return $title.' | ';
}
?>
