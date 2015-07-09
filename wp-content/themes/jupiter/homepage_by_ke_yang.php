<?php
/*
Template Name: Homepage Page by Ke Yang
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
    <header id="app-masthead" role="banner">
      <div class="row" style="text-align:center;">
       
        <div class="col nomargin nopadding">
        <a href="/index.php">
        <img src="/wp-content/themes/jupiter/home/img/Logo250x113.jpg" alt="homepage"/>
        </a> 
          <nav id="primary-nav" class="nav">
            <div id="nav-wrapper">

              <div class="mask">
                <p id="menu"><a href="javascript:void(0);">Menu</a><img src="/wp-content/themes/jupiter/home/img/Logo250x113.jpg" alt="homepage"/></p>
              </div>

              <ul>
                <li class="active"><a href="/index.php">Home</a></li>
                <li><a href="/booking">Bookings</a></li>
                <li><a href="/updates">Updates</a></li>
                <li><a href="/faqs-page">FAQs</a></li>
                <li><a href="/contact">Contact</a></li>
                <li class="last-child"><a href="/franchises">Franchises</a></li>
              </ul> 
            </div>
          </nav>
        </div>
      </div>
    </header>
    <div id="app-page">
      <div id="app-content" role="main">
        <section id="hero" class="section row">
          <div class="panel">
             <div data-eqheight="source" class="col span-12-xs span-7-md eqheight">
              <?php putRevSlider("room") ?>
            </div>
             <div data-eqheight="target" class="col span-12-xs span-5-md grey right eqheight">
              <div class="content">
                <h3>teamwork, communication, solve exciting life puzzles</h3>
                <p style="color:white;">Escape Room is an interactive and intuitive real-life escape game. Locked in a group of 2 to 6 people, participants have 60 minutes to solve challenging puzzles to escape the room.</p>
                <ul class="cta">
                  <li><a href="/booking">BOOK NOW</a></li>
                </ul>
              </div>
            </div>
          </div>
        </section>
                <section id="hero" class="section row">
          <div class="panel">
              <div data-eqheight="target" class="col span-12-xs span-5-md yellow left eqheight">
              <div class="content">
                <h3>an alternative form of entertainment</h3>
                <p>Over 380,000 people have experienced our Escape Rooms worldwide. We offer an experience truly unique, worth talking about and sharing with friends
              </div>
            </div>
          <div data-eqheight="source" class="col span-12-xs span-7-md eqheight right">
            <iframe style="width:100%;min-height:320px;"  frameborder="0" src="https://www.youtube.com/embed/TvQPErA3pKQ" frameborder="0" allowfullscreen></iframe>
          </div>
        </section>
        <section class="section row">
          <div class="panel testimonial">
 <div class="col span-12-xs span-2-md">
    <img src="/wp-content/themes/jupiter/home/content/img/homepage/Trip-Advisor-logo.png" class="trip-logo">
</div>
<div class="col span-12-xs span-10-md">
<div id="te-carousel">
  
  <div id="te-slides">
    <ul>
      <li class="te-slide">
        <div class="te-quoteContainer">
          <p style="color:white;"><span class="te-quote-marks">"</span> Just home now from attempting the prison break! What a brilliant experience! Nothing we have tried before so nothing to compare it to but would go again without a doubt. Staff welcoming, apologised they were running behind (only 5 minutes but couldn't apologise enough), really well thought out and clever mission. Didn't quite make it out, had the right idea but just ran out of time. Fabulous place, would recommend to all for a really different night out. WOW!
<span class="te-quote-marks">"</span> </p>
        </div>
        <div class="te-authorContainer">
          <p class="te-quote-author">Johanathan Ottawas, France</p>
        </div>
      </li>
      <li class="te-slide">
        <div class="te-quoteContainer">
          <p style="color:white;"><span class="quote-marks">"</span>Nothing we have tried before so nothing to compare it to but would go again without a doubt. Staff welcoming, apologised they were running behind (only 5 minutes but couldn't apologise enough), really well thought out and clever mission. Didn't quite make it out, had the right idea but just ran out of time. Fabulous place, would recommend to all for a really different night out. <span class="te-quote-marks">"</span> </p>
        </div>
        <div class="te-authorContainer">
          <p class="te-quote-author">Andy Duhbrey, UK</p>
        </div>
      </li>
      <li class="te-slide">
        <div class="te-quoteContainer">
          <p style="color:white;"><span class="quote-marks">"</span>Best experience! Nothing we have tried before so nothing to compare it to but would go again without a doubt. Staff welcoming, apologised they were running behind (only 5 minutes but couldn't apologise enough), really well thought out and clever mission. Didn't quite make it out, had the right idea but just ran out of time. Fabulous place, would recommend to all for a really different night out.<span class="te-quote-marks">"</span> </p>
        </div>
        <div class="te-authorContainer">
          <p class="te-quote-author">Janice White, Italy</p>
        </div>
      </li>
    </ul>
  </div>
<div class="te-btn-bar">
  <div id="te-buttons">
    <a id="prev" href="#" style="font-size:32px;font-weight:800;">&lt;</a>
    <a id="next" href="#" style="font-size:32px;font-weight:800;">&gt;</a> 
  </div>
  <div style="text-align: center;display: block; margin: auto; height: 100px;">
    <a href="booking-m" class="white-border-ma" style="float: none !important;">MANCHESTER</a>
    <a href="booking-p" class="white-border-pre" style="float: none !important;">PRESTON</a></div>
  </div>
</div>


<script>
$(document).ready(function () {
    //rotation speed and timer
    var speed = 3000;
    
    var run = setInterval(rotate, speed);
    var slides = $('.te-slide');
    var container = $('#te-slides ul');
    var elm = container.find(':first-child').prop("tagName");
    //var item_width = container.width();
    var item_width = "100%";
    var previous = 'prev'; //id of previous button
    var next = 'next'; //id of next button
    slides.width(item_width); //set the slides to the correct pixel width
    container.parent().width(item_width);
    container.width(slides.length * item_width); //set the slides container to the correct total width
    container.find(elm + ':first').before(container.find(elm + ':last'));
    resetSlides();
    
    
    //if user clicked on prev button
    
    $('#te-buttons a').click(function (e) {
        //slide the item
        
        if (container.is(':animated')) {
            return false;
        }
        if (e.target.id == previous) {
            container.stop().animate({
                'left': 0
            }, 50, function () {
                container.find(elm + ':first').before(container.find(elm + ':last'));
                resetSlides();
            });
        }
        
        if (e.target.id == next) {
            container.stop().animate({
                'left': item_width * -2
            }, 50, function () {
                container.find(elm + ':last').after(container.find(elm + ':first'));
                resetSlides();
            });
        }
        
        //cancel the link behavior            
        return false;
        
    });
    
    //if mouse hover, pause the auto rotation, otherwise rotate it    
    container.parent().mouseenter(function () {
        clearInterval(run);
    }).mouseleave(function () {
        run = setInterval(rotate, speed);
    });
    
    
    function resetSlides() {
        //and adjust the container so current is in the frame
        container.css({
            'left': -1 * item_width
        });
    }
    
});
//a simple function to click next link
//a timer will call this function, and the rotation will begin

function rotate() {
    $('#next').click();
}
</script>


</div>

          </div>
        </section>
        <section>
         <div class="panel">
         <div  class="col span-12-xs span-3-md">
         <div  style="padding:10px;text-align:center;">
<img src="/wp-content/themes/jupiter/home/img/icon2.png" style="height:100px;"><h5>FAMILY AND FRIENDS</h5>
<p style="color:white;">Looking for something unique that will entertain both you and the kids? Our Escape Rooms are an excellent way to spend some amazing time with your family and friends, as you work together to escape from our rooms. Going to the movies is so last year!
</p>  </div>       </div>
<div  class="col span-12-xs span-3-md">
 <div  style="padding:10px;text-align:center;">
<img src="/wp-content/themes/jupiter/home/img/icon3.png" style="height:100px;"><h5>CORPORATE EVENTS</h5>
<p style="color:white;">Tired of dull and conventional team building events in the city? Escape Room provides challenging scenarios packed for your team. Our Escape Rooms will provide a unique and fun event for corporate entertainment and networking events.
</p>    </div>     </div>
  <div  class="col span-12-xs span-3-md"> 
  <div  style="padding:10px;text-align:center;">
<img src="/wp-content/themes/jupiter/home/img/icon1.png" style="height:100px;"><h5>STUDENTS</h5>
<p style="color:white;">So you enjoy challenges and solving puzzles? Looking for a challenging yet fun activity to unwind with your mates? Take things up a notch by experiencing our Escape Rooms – LIVE! You need 2 – 6 players and can choose from 5 different rooms.
</p>      </div>  </div>
  <div  class="col span-12-xs span-3-md">
  <div  style="padding:10px;text-align:center;">
<img src="/wp-content/themes/jupiter/home/img/icon4.png" style="height:100px;"><h5>TOURISTS AND TRAVELLERS</h5>
<p style="color:white;">What better way to complement your trip than to experience our Escape Rooms. Our unique form of entertainment will appeal much more than the usual tour bus, museums and bridges. We’re taking entertainment to a whole new level!
</p>       </div>  </div>
        </section>
      </div>
    </div>
    <script src="/wp-content/themes/jupiter/home/scripts/main.min.js"></script>
    <noscript>
      Javascript has been disabled in your browser and is required for the full experience.
      
    </noscript>
    <?php get_footer(); ?>
  </body>

</html>