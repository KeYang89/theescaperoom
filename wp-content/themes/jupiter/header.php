<!DOCTYPE html>
<html <?php mk_html_tag_schema(); ?> xmlns="http<?php echo (is_ssl())? 's' : ''; ?>://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<?php  
global $mk_options;
?>            
    <head>
        <meta charset="<?php bloginfo('charset'); ?>" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0" />
          <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
          <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
          <meta name="format-detection" content="telephone=no">
        <title itemprop="name">
        <?php
           if ( defined('WPSEO_VERSION') ) {
            wp_title('');
             } else {
             bloginfo('name'); ?> <?php wp_title(' - ', true);
          }
        ?>
        </title>
        <link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url');?>">
        <link rel="alternate" type="application/atom+xml" title="<?php bloginfo('name'); ?> Atom Feed" href="<?php bloginfo('atom_url');?>">
        <link rel="pingback" href="<?php bloginfo('pingback_url');?>">

         <!--[if lt IE 9]>
         <script src="<?php echo THEME_JS;?>/html5shiv.js" type="text/javascript"></script>
         <link rel='stylesheet' href='<?php echo get_template_directory_uri(); ?>/stylesheet/css/ie.css' /> 
         <![endif]-->
         <!--[if IE 7 ]>
               <link href="<?php echo THEME_STYLES;?>/ie7.css" media="screen" rel="stylesheet" type="text/css" />
               <![endif]-->
         <!--[if IE 8 ]>
               <link href="<?php echo THEME_STYLES;?>/ie8.css" media="screen" rel="stylesheet" type="text/css" />
         <![endif]-->

         <!--[if lte IE 8]>
            <script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/respond.js"></script>
         <![endif]-->

         <?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>

         <script type="text/javascript">
          var mk_header_parallax, mk_banner_parallax, mk_page_parallax, mk_footer_parallax, mk_body_parallax;
          var mk_images_dir = "<?php echo THEME_IMAGES; ?>",
          mk_theme_js_path = "<?php echo THEME_JS;  ?>",
          mk_theme_dir = "<?php echo THEME_DIR_URI; ?>",
          mk_responsive_nav_width = <?php echo $mk_options['responsive_nav_width']; ?>,
          mk_grid_width = <?php echo $mk_options['grid_width'] ?>,
          mk_ajax_search_option = "<?php echo $mk_options['header_search_location']; ?>",
          mk_preloader_txt_color = "<?php echo ($mk_options['preloader_txt_color']) ? $mk_options['preloader_txt_color'] : '#444'; ?>",
          mk_preloader_bg_color = "<?php echo ($mk_options['preloader_bg_color']) ? $mk_options['preloader_bg_color'] : '#fff'; ?>",
          mk_accent_color = "<?php echo $mk_options['skin_color']; ?>",
          mk_preloader_bar_color = "<?php echo (isset($mk_options['preloader_bar_color']) && !empty($mk_options['preloader_bar_color'])) ? $mk_options['preloader_bar_color'] : $mk_options['skin_color']; ?>",
          mk_preloader_logo = "<?php echo $mk_options['preloader_logo']; ?>";
          <?php if(global_get_post_id()) : ?>
          var mk_header_parallax = <?php echo get_post_meta( $post->ID, 'header_parallax', true ) ? get_post_meta( $post->ID, 'header_parallax', true ) : "false" ?>,
          mk_banner_parallax = <?php echo get_post_meta( $post->ID, 'banner_parallax', true ) ? get_post_meta( $post->ID, 'banner_parallax', true ) : "false"; ?>,
          mk_page_parallax = <?php echo get_post_meta( $post->ID, 'page_parallax', true ) ? get_post_meta( $post->ID, 'page_parallax', true ) : "false"; ?>,
          mk_footer_parallax = <?php echo get_post_meta( $post->ID, 'footer_parallax', true ) ? get_post_meta( $post->ID, 'footer_parallax', true ) : "false"; ?>,
          mk_body_parallax = <?php echo get_post_meta( $post->ID, 'body_parallax', true ) ? get_post_meta( $post->ID, 'body_parallax', true ) : "false"; ?>,
          mk_no_more_posts = "<?php echo _e('No More Posts', 'mk_framework'); ?>";
          <?php endif; ?>
          
          function is_touch_device() {
              return ('ontouchstart' in document.documentElement);
          }
          
         </script>
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="/wp-content/themes/jupiter/home/scripts/vendor/modernizr.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <!--[if (lte IE 8)&!(IEMobile)]>
    <script type="text/javascript">
    <document video class="createElement">;document.createElement('audio');document.createElement('track');</document></script>
    <![endif]-->
    <link rel="stylesheet" href="/wp-content/themes/jupiter/home/styles/main.min.css"/>
    <link href="/wp-content/themes/jupiter/home/styles/jquerysctipttop.css" rel="stylesheet" type="text/css">
    <!--[if (lte IE 8)&!(IEMobile)]>
    <link href="/video-js/video-js.css" rel="stylesheet">
    <![endif]-->
    <?php wp_head(); ?>
    </head>
<?php

$mk_body_class[] = $mk_header_class = $show_header_old = $show_header = $transparent_header = $transparent_header_skin = $trans_header_class = $page_is_transparent = $header_sticky_style_css = '';

$header_style = !empty($mk_options['theme_header_style']) ? $mk_options['theme_header_style'] : 1;
$toolbar_toggle = !empty($mk_options['theme_toolbar_toggle']) ? $mk_options['theme_toolbar_toggle'] : 'true';
$header_align = !empty($mk_options['theme_header_align']) ? $mk_options['theme_header_align'] : 'left';

$header_grid_start = ($mk_options['header_grid'] == 'true') ? '<div class="mk-grid header-grid">' : '';
$header_grid_end = ($mk_options['header_grid'] == 'true') ? '</div>' : '';
$header_width_class = ($mk_options['header_grid'] == 'true') ? 'boxed-header' : 'full-header';
$sticky_header_style = isset($mk_options['header_sticky_style']) ? $mk_options['header_sticky_style'] : 'fixed';
$sticky_header_offset = isset($mk_options['sticky_header_offset']) ? $mk_options['sticky_header_offset'] : 'header';


$post_id = global_get_post_id();

if($post_id) {
  $enable = get_post_meta($post_id, '_enable_local_backgrounds', true );
  
  if($enable == 'true') {
    $header_style_meta = get_post_meta( $post_id, 'theme_header_style', true );
    $header_align_meta = get_post_meta( $post_id, 'theme_header_align', true );
    $toolbar_toggle_meta = get_post_meta( $post_id, 'theme_toolbar_toggle', true );
    $transparent_header_meta = get_post_meta( $post_id, '_transparent_header', true );
    $transparent_header_skin_meta = get_post_meta( $post_id, '_transparent_header_skin', true );
    $sticky_header_offset_meta = get_post_meta( $post_id, '_sticky_header_offset', true );
    $trans_header_remove_bg_meta = get_post_meta( $post_id, '_trans_header_remove_bg', true );
    
    $header_style = (isset($header_style_meta) && !empty($header_style_meta)) ? $header_style_meta : $header_style;
    $header_align = (isset($header_align_meta) && !empty($header_align_meta)) ? $header_align_meta : $header_align;
    $toolbar_toggle = (isset($toolbar_toggle_meta) && !empty($toolbar_toggle_meta)) ? $toolbar_toggle_meta : $toolbar_toggle;
    $transparent_header = (isset($transparent_header_meta) && !empty($transparent_header_meta)) ? $transparent_header_meta : 'false';
    $transparent_header_skin = (isset($transparent_header_skin_meta) && !empty($transparent_header_skin_meta)) ? $transparent_header_skin_meta : 'light';
    $sticky_header_offset = (isset($sticky_header_offset_meta) && !empty($sticky_header_offset_meta)) ? $sticky_header_offset_meta : $sticky_header_offset;
    $trans_header_remove_bg = (isset($trans_header_remove_bg_meta) && !empty($trans_header_remove_bg_meta)) ? $trans_header_remove_bg_meta : 'true';
  }
  
}


$single_preloader = '';
$preloader_check = 'disabled';

if($post_id) {
  $show_header_old = get_post_meta( $post_id, '_header', true );
  $show_header = get_post_meta( $post_id, '_template', true );
  $single_preloader = get_post_meta($post_id, 'page_preloader', true );
}






if($single_preloader == 'true') {
    $preloader_check = 'enabled';
  } else {
    if($mk_options['preloader'] == 'true') {
      $preloader_check = 'enabled';
    }
  }

?>

<body <?php body_class($mk_body_class); ?> data-backText="<?php _e('Back', 'mk_framework'); ?>" data-vm-anim="<?php echo $mk_options['vertical_menu_anim']; ?>">

<?php
if($preloader_check == 'enabled') { 
  echo '<div class="mk-body-loader-overlay"></div>';
} 
?>


