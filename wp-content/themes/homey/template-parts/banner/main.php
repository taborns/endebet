<?php
global $post, $homey_prefix;
$banner_type = get_post_meta($post->ID, $homey_prefix.'header_type', true);

if( $banner_type == 'parallax' ) {
	get_template_part('template-parts/banner/parallax');

} elseif( $banner_type == 'video' ) {
	get_template_part('template-parts/banner/video');

} elseif( $banner_type == 'map' ) {
	get_template_part('template-parts/banner/map');

} elseif( $banner_type == 'slider' ) {
	get_template_part('template-parts/banner/slider');

} elseif( $banner_type == 'rev_slider' ) {
	get_template_part('template-parts/banner/revolution-slider');

} elseif( $banner_type == 'half_search' ) {
	get_template_part('template-parts/banner/half-search');
} 
?>