<?php
/**
 * The template to display blog archive
 *
 * @package WordPress
 * @subpackage GAMEZONE
 * @since GAMEZONE 1.0
 */

/*
Template Name: Blog archive
*/

/**
 * Make page with this template and put it into menu
 * to display posts as blog archive
 * You can setup output parameters (blog style, posts per page, parent category, etc.)
 * in the Theme Options section (under the page content)
 * You can build this page in the WordPress editor or any Page Builder to make custom page layout:
 * just insert %%CONTENT%% in the desired place of content
 */

// Get template page's content
$gamezone_content = '';
$gamezone_blog_archive_mask = '%%CONTENT%%';
$gamezone_blog_archive_subst = sprintf('<div class="blog_archive">%s</div>', $gamezone_blog_archive_mask);
if ( have_posts() ) {
	the_post();
	if (($gamezone_content = apply_filters('the_content', get_the_content())) != '') {
		if (($gamezone_pos = strpos($gamezone_content, $gamezone_blog_archive_mask)) !== false) {
			$gamezone_content = preg_replace('/(\<p\>\s*)?'.$gamezone_blog_archive_mask.'(\s*\<\/p\>)/i', $gamezone_blog_archive_subst, $gamezone_content);
		} else
			$gamezone_content .= $gamezone_blog_archive_subst;
		$gamezone_content = explode($gamezone_blog_archive_mask, $gamezone_content);
		// Add VC custom styles to the inline CSS
		$vc_custom_css = get_post_meta( get_the_ID(), '_wpb_shortcodes_custom_css', true );
		if ( !empty( $vc_custom_css ) ) gamezone_add_inline_css(strip_tags($vc_custom_css));
	}
}

// Prepare args for a new query
$gamezone_args = array(
	'post_status' => current_user_can('read_private_pages') && current_user_can('read_private_posts') ? array('publish', 'private') : 'publish'
);
$gamezone_args = gamezone_query_add_posts_and_cats($gamezone_args, '', gamezone_get_theme_option('post_type'), gamezone_get_theme_option('parent_cat'));
$gamezone_page_number = get_query_var('paged') ? get_query_var('paged') : (get_query_var('page') ? get_query_var('page') : 1);
if ($gamezone_page_number > 1) {
	$gamezone_args['paged'] = $gamezone_page_number;
	$gamezone_args['ignore_sticky_posts'] = true;
}
$gamezone_ppp = gamezone_get_theme_option('posts_per_page');
if ((int) $gamezone_ppp != 0)
	$gamezone_args['posts_per_page'] = (int) $gamezone_ppp;
// Make a new main query
$GLOBALS['wp_the_query']->query($gamezone_args);


// Add internal query vars in the new query!
if (is_array($gamezone_content) && count($gamezone_content) == 2) {
	set_query_var('blog_archive_start', $gamezone_content[0]);
	set_query_var('blog_archive_end', $gamezone_content[1]);
}

get_template_part('index');
?>