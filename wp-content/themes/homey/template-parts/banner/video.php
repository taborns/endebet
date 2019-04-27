<?php
global $post, $homey_prefix;

$mp4 = get_post_meta($post->ID, $homey_prefix.'header_bg_mp4', true);
$webm = get_post_meta($post->ID, $homey_prefix.'header_bg_webm', true);
$ogv = get_post_meta($post->ID, $homey_prefix.'header_bg_ogv', true);
$video_image_id = get_post_meta($post->ID, $homey_prefix.'video_image', true);
$img_url = wp_get_attachment_image_src( $video_image_id, 'full' );

$ogv = substr($ogv, 0, strrpos($ogv, "."));
$mp4 = substr($mp4, 0, strrpos($mp4, "."));
$webm = substr($webm, 0, strrpos($webm, "."));
$video_image = substr($img_url[0], 0, strrpos($img_url[0], "."));
?>
<section class="top-banner-wrap <?php homey_banner_fullscreen(); ?>">    

    <div id="video-background" class="video-background splash-video-background" data-vide-bg="mp4: <?php echo esc_url($mp4); ?>, webm: <?php echo esc_url($webm); ?>, ogv: <?php echo esc_url($ogv); ?>, poster: <?php echo esc_url($video_image); ?>" data-vide-options="position: 0% 50%">
        </div>

    <div class="banner-caption <?php homey_banner_search_class(); ?>">
    	<?php 
        homey_banner_search_div_start(); 

        get_template_part('template-parts/banner/caption'); 
        
        if(homey_banner_search()) {
            get_template_part ('template-parts/search/banner-'.homey_banner_search_style()); 
        }
        
        homey_banner_search_div_end(); 
        ?>
    </div><!-- banner-caption -->

</section><!-- header-parallax -->