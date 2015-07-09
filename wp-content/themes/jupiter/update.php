<?php
/*
Template Name: Update Page by Ke Yang
*/
global $post,
$mk_options;
$page_layout = get_post_meta( $post->ID, '_layout', true );
$padding = get_post_meta( $post->ID, '_padding', true );


if ( empty( $page_layout ) ) {
  $page_layout = 'full';
}
$padding = ($padding == 'true') ? 'no-padding' : '';

get_header(); ?>
<style>
.image-hover-overlay {display: none;}
a {font-size:22px}
#theme-page {margin-top:130px;}
</style>
 <header id="app-masthead" role="banner">
      <div class="row" style="text-align:center;">
       
        <div class="col nomargin nopadding">
        <a href="/index.php">
        <img src="/wp-content/themes/jupiter/home/img/Logo250x113.jpg" alt="homepage"/>
        </a> 
          <nav id="primary-nav" class="nav">
            <div id="nav-wrapper">

              <div class="mask">
                <p id="menu"><a href="javascript:void(0);" style="float:left;">Menu</a><img src="/wp-content/themes/jupiter/home/img/Logo250x113.jpg" alt="homepage"/></p>
              </div>

              <ul>
                <li><a href="/index.php">Home</a></li>
                <li><a href="/booking">Bookings</a></li>
                <li><a href="/update" class="active">Updates</a></li>
                <li><a href="/faqs-page">FAQs</a></li>
                <li><a href="/contact">Contact</a></li>
                <li class="last-child"><a href="/franchises">Franchises</a></li>
              </ul> 
            </div>
          </nav>
        </div>
      </div>
</header>
<div id="theme-page">
  <div class="mk-main-wrapper-holder">
    <div id="mk-page-id-<?php echo $post->ID; ?>" class="theme-page-wrapper mk-main-wrapper <?php echo $page_layout; ?>-layout <?php echo $padding; ?> mk-grid vc_row-fluid">
      <div class="theme-content <?php echo $padding; ?>" itemprop="mainContentOfPage">
        <?php if ( have_posts() ) while ( have_posts() ) : the_post();?>
            <?php the_content();?>
            <div class="clearboth"></div>
            <?php wp_link_pages( 'before=<div id="mk-page-links">'.__( 'Pages:', 'mk_framework' ).'&after=</div>' ); ?>
        <?php endwhile; ?>
      </div>
      <?php
        if(isset($mk_options['pages_comments']) && $mk_options['pages_comments'] == 'true') {
          comments_template( '', true );  
        }
      ?>
    <?php if ( $page_layout != 'full' ) get_sidebar(); ?>
    <div class="clearboth"></div>
    </div>
    <div class="clearboth"></div>
  </div>  
</div>
<?php //do_action( 'footer_twitter' ); removed! ?>
<?php get_footer(); ?>