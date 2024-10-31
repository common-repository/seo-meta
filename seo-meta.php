<?php
/*
Plugin Name: SOCIAL.NINJA
Plugin URI: https://wpapp.ninja/
Description: Optimize your SEO with Open Graph meta tags, Twitter Cards and social share links.
Version: 0.2
Author: WPAPP.NINJA
Author URI: https://wpapp.ninja/
*/

/**
 * Shortcode for social share link.
 *
 * @since 0.2
 */
add_shortcode( 'socialninja', 'seo_meta_shortcode' );
function seo_meta_shortcode($atts) {

    $a = shortcode_atts( array(
        'network' => ''
    ), $atts );


	$url 	= seo_meta_current_url();
	$text 	= urlencode(seo_meta_get_title());
	$image 	= seo_meta_getImage( get_the_ID() );

    switch ($a['network']) {

		case 'facebook':
			$link = 'https://www.facebook.com/sharer/sharer.php?u=' . $url;
			break;

		case 'google':
			$link = 'https://plus.google.com/share?url=' . $url;
			break;

		case 'twitter':
			$link = 'https://twitter.com/intent/tweet?text=' . $text . '+' . $url;
			break;

		case 'linkedin':
			$link = 'https://www.linkedin.com/shareArticle?mini=true&url=' . $url;
			break;

		case 'pinterest':
			$link = 'http://pinterest.com/pin/create/button/?description=' . $text . '&media=' . $image . '&url=' . $url;
			break;

		case 'reddit':
			$link = 'https://reddit.com/submit?url=' . $url;
			break;

		case 'digg':
			$link = 'https://digg.com/submit?url=' . $url;
			break;

    }


    return '<a href="' . $link . '" rel="nofollow" target="_blank" class="socialninja socialninja_' . $a['network'] . '">' . ucfirst($a['network']) . '</a>';
}

/**
 * CSS's shortcode.
 *
 * @since 0.2
 */
add_action( 'wp_enqueue_scripts', 'socialninja_css' );
function socialninja_css() {
	wp_register_style( 'socialninja', plugins_url( 'seo-meta/links.css' ) );
	wp_enqueue_style( 'socialninja' );
}

/**
 * Generate meta tag for SEO and social sharing.
 */
add_action('wp_head', 'seo_meta_tags');
function seo_meta_tags() {
    if (!is_single() && !is_home() && !is_page()) {
        return;
    }
    
    $ID				= get_the_ID();
	$content_post	= get_post($ID);
	$content 	 	= strip_shortcodes(apply_filters('the_content', $content_post->post_content));
    $title			= seo_meta_get_title();
	$excerpt		= wp_trim_words($content, 50);
    $url			= seo_meta_current_url();
    $sitename		= esc_attr(get_bloginfo('name'));
    $image			= seo_meta_getImage( $ID );
    
    echo '<meta property="og:locale" content="' . get_locale() . '" />' . PHP_EOL;
    echo '<meta property="og:type" content="article" />' . PHP_EOL;
    echo '<meta property="og:title" content="' . $title . '" />' . PHP_EOL;
	echo '<meta property="og:description" content="' . $excerpt . '" />' . PHP_EOL;
    echo '<meta property="og:url" content="' . $url . '" />' . PHP_EOL;
    echo '<meta property="og:site_name" content="' . $sitename . '" />' . PHP_EOL;
    echo '<meta property="og:image" content="' . $image . '" />' . PHP_EOL;
    echo '<meta name="twitter:card" content="summary_large_image"/>' . PHP_EOL;
    echo '<meta name="twitter:domain" content="' . $sitename . '"/>' . PHP_EOL;
    echo '<meta name="twitter:image:src" content="' . $image . '"/>' . PHP_EOL;
	echo '<meta name="twitter:title" content="' . $title . '" />' . PHP_EOL;
	echo '<meta name="twitter:description" content="' . $excerpt . '" />' . PHP_EOL;
}

/**
 * Construct the current url.
 * HTTPS supported.
 */
function seo_meta_current_url() {
    $host = 'http://';
    if (isset($_SERVER['HTTPS'])) {$host = 'https://';}

    return $host . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

/**
 * Return an image for the post
 *
 * - featured if available
 * - one image on the post
 */
function seo_meta_getImage($id) {
	$image = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), 'single-post-thumbnail' );
	if ($image[0] != '') {
		return $image[0];
	}
	
	$images = get_attached_media('image', $id);
	foreach($images as $img) {
		$i = wp_get_attachment_image_src($img->ID, 'large');
		if ($i[0] != '') {
			return $i[0];
		}
	}
	
	return '';
}

/**
 * Get and format the title for be sharing friendly.
 */
function seo_meta_get_title() {
	$title = wp_title('|', false, 'right');
	if ($title == '') {
		$title = esc_textarea(get_bloginfo('name'));
	} else {
		$title_arr = explode('|', $title);
		$title = trim($title_arr[0]);
	}
	
	return $title;
}
