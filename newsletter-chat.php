<?php
/**
 * Plugin Name:       Newsletter Chat
 * Plugin URI:        https://geeky.com.ng/newsletter-chat-plugin
 * Description:       A lite plugin to generate today's posts as Newsletter to share on WhatsApp  
 * Version:           1.2
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Geeky Nigeria
 * Author URI:        https://geeky.com.ng
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */
 
 add_action( 'activated_plugin', 'NWCGNIG_activate_redirect' );
 register_deactivation_hook( __FILE__, 'NWCGNIG_newsletter_remove' );
 register_uninstall_hook(__FILE__, 'NWCGNIG_newsletter_remove');
 
 function NWCGNIG_newsletter_remove() {
     $page = get_page_by_path("newsletter-chat");
            if ($page) {
                $page_id =  $page->ID;
                 wp_delete_post($page_id);
            }
 }

function NWCGNIG_newsletter_plugin_styles() {
    wp_register_style( 'newsletter-chat', plugins_url( 'newsletter-chat/css/styles.css' ) );
    wp_enqueue_style( 'newsletter-chat' );
}
// Register style sheet.
add_action( 'wp_enqueue_scripts', 'NWCGNIG_newsletter_plugin_styles' );
add_action( 'admin_head', 'NWCGNIG_newsletter_plugin_styles' );
add_action('admin_menu', 'NWCGNIG_chat_settings_menu');

function NWCGNIG_chat_settings_menu() {
	add_menu_page('Newsletter Chat', 'Newsletter Chat', 'manage_options', 'newschat-settings', 'NWCGNIG_chat_settings_page', 'dashicons-clipboard');
	add_submenu_page('newschat-settings', 'Headlines', 'Headlines', 'manage_options', 'newschat_headlines', 'NWCGNIG_newschat_headlines' );
	    add_submenu_page('newschat-settings', 'Support', 'Support', 'manage_options', 'newschat_support', 'NWCGNIG_newschat_support' );
	    


}

 function NWCGNIG_activate_redirect( $plugin ) {
    if( $plugin == plugin_basename( __FILE__ ) ) {
        exit( wp_redirect( admin_url( 'admin.php?page=newschat-settings' ) ) );
    }
}

add_action( 'admin_init', 'NWCGNIG_register_settings' );


function NWCGNIG_register_settings() {
	//register our settings
	register_setting( 'newschat_settings', 'posts_title' );
    register_setting( 'newschat_settings', 'posts_footer' );
    register_setting( 'newschat_settings', 'nw_cng');
    register_setting( 'newschat_settings', 'nw_lng');
    
}

    
function NWCGNIG_newsletter_prev() {

$today = getdate();
    $args = array(
    'date_query' => array(
        array(
            'year'  => $today['year'],
            'month' => $today['mon'],
            'day'   => $today['mday'],
        ),
    ),
);
$query = new WP_Query( $args );
$whatsnum = $query->post_count;


   if ( $query->have_posts()) {
       
$checkweb = get_option( 'nw_cng' );
$checklnk = get_option( 'nw_lng' );
$newstitle = strtoupper(get_option( 'posts_title' )); 
$newsfooter = get_option( 'posts_footer' );
$titlec = urlencode( $newstitle );
$footerec = urlencode( $newsfooter );
?>
<div class= "NWCGNIG_newschat-box">
  <h5><?php echo "<b>$newstitle</b>"; ?></h5>
 <?php while ( $query->have_posts() ) : $query->the_post() ; 
  ?>
  <h4><?php the_title ();  ?></h4>

   <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
 <?php the_permalink(); ?>
 </a> 

<?php endwhile; 
?> <br><br>
<p class="NWCGNIG_newschat-footer"><i> <?php if ($checkweb =="1") echo "<br><br>Visit " . get_bloginfo ( 'name' ) . " - "  . home_url() ?> <br>
<?php if ($checklnk =="2") echo "<br>Newsletter Page - " . home_url() . "/newsletter-chat"; echo "<br>$newsfooter."; ?>
  </i></p>
</div>

<div class="NWCGNIG_post_newsletter NWCGNIG_center-align">
<a href="https://api.whatsapp.com/send?text=<?php echo "*$titlec*";while ( $query->have_posts() ) : $query->the_post();$newstite = the_title ('','',false);echo "%20%0A%0A$newstite ";

endwhile;

if ($checkweb =="1") echo "%0A%0A*Visit " . get_bloginfo ( 'name' ) . "* - "  . home_url(); 
      if ($checklnk =="2") echo "%0A%0A*Newsletter Page* - " . home_url() . "/newsletter-chat"; echo "%0A%0A$footerec";

 ?>" >
   
    <b>Post on WhatsApp</b></a></div>  <div class="NWCGNIG_center-align">Ensure Headlines do not contain special characters as <b>&</b> and <b>#</b>. </div>
</div>


<?php
}
else {
  ?>
  <div class= "NWCGNIG_newschat-box">
  <p class="alert-text">No Posts Published Yet! Check Back Later.</p>
 
</div>
<?php
}
}
function NWCGNIG_newschat_shortcode() {
	
	add_shortcode( 'NewsChatNG', 'NWCGNIG_newsletter_prev' );
	
}
add_action( 'init', 'NWCGNIG_newschat_shortcode' );
function NWCGNIG_add_newspage() {
    // Create post object
    $my_post = array(
      'post_title'    => wp_strip_all_tags( 'Newsletter Chat' ),
      'post_content'  => ' [NewsChatNG] ',
      'post_status'   => 'publish',
      'post_author'   => 1,
      'post_type'     => 'page',
    );

    // Insert the post into the database
    wp_insert_post( $my_post );
}
register_activation_hook(__FILE__, 'NWCGNIG_add_newspage');
function NWCGNIG_chat_settings_page() {
?>
<div class="wrap">
    <div style="width:100%">
<h1 class="NWCGNIG_center-align">Newsletter Chat</h1>
<h2 class="NWCGNIG_center-align">By Geeky Nigeria</h2>
		<?php settings_errors(); ?> 
<div class="NWCGNIG_center-align">

	<p class="NWCGNIG_settings-intro">Share Today's Posts to your WhatsApp subscribers (Groups & Contacts) </p><br></div>
		
									  <?php 
$today = getdate();
$args = array(
    'ignore_sticky_posts' => 1,
    'posts_per_page' => -1, //all posts 
    'date_query' => array(
        array(
            'year'  => $today['year'],
            'month' => $today['mon'],
            'day'   => $today['mday'],
        ),
    ),
);

$today_posts = new WP_Query( $args );

$count = $today_posts->post_count;
 if ($count==0) { ?>
        <table>        
        <tr valign="top">   
        <th scope="row" class="NWCGNIG_admin-option">You have not published posts today!</th>
      </tr></table>
<?php
        }
        else { 

$sitetitle = get_bloginfo ( 'name' ); 
          ?>
          <form method="post" action="options.php">
      <?php settings_fields( 'newschat_settings' ); ?>
      <?php do_settings_sections( 'newschat_settings' ); 
      ?>
               <div class="NWCGNIG_option-format">
                   
                   <h1 class="NWCGNIG_center-align">You have published <?php echo $count; ?> Posts Today.</h1>
        <p class="NWCGNIG_center-align NWCGNIG_settings-intro">Set your Newsletter settings below, then visit page at - <a class = "NWCGNIG_page-link" href="<?php echo home_url() . "/newsletter-chat"; ?>"><?php echo home_url() . "/newsletter-chat"; ?></a>  </p><br><br>
                  
                   
                   
                   <span class="NWCGNIG_newsletter-label">Include Website link?</span> <br><br>

  <input type="checkbox" id="NWCGNIG_web" name="nw_cng" value="1" <?php checked(1, get_option('nw_cng'), true); ?> />
  <label for="NWCGNIG_web" class="NWCGNIG_settings-intro">Yes</label>
<br><br><br><br>

 <span class="NWCGNIG_newsletter-label">Include Newsletter Page link ?</span> <br><br>

  <input type="checkbox" id="NWCGNIG_link" name="nw_lng" value="2" <?php checked(2, get_option('nw_lng'), true); ?> />
  <label for="NWCGNIG_link" class="NWCGNIG_settings-intro">Yes</label>
<br><br><br><br>
                   
      
          <span class="NWCGNIG_newsletter-title">Title</span> <br><br>

        <input type="text" class="NWCGNIG_newsletter-value" size = "30" name="posts_title" value="<?php if (get_option( 'posts_title' ) == "" ) echo "LATEST HEADLINE NEWS FROM $sitetitle"; else echo get_option( 'posts_title' ); ?>" />
<br><br>
          <span class="NWCGNIG_newsletter-title">Footer</span> <br><br>
     <input type="text" class="NWCGNIG_newsletter-footer" size = "30" name="posts_footer" value="<?php if (get_option( 'posts_footer' ) == "" ) echo 'That is all for today'; else echo get_option( 'posts_footer' ); ?>" /><br><br>
     
          <span class="NWCGNIG_newsletter-title">Page </span> 

        <p class="NWCGNIG_page-link-section"><b><a class = "NWCGNIG_page-link" href="<?php echo home_url() . "/newsletter-chat"; ?>"><?php echo home_url() . "/newsletter-chat"; ?></a></b>  </p></div>
            <div class="NWCGNIG_center-align"><?php submit_button(); ?></div>
<p class="NWCGNIG_final-tip">Share responsibily.</p><br><br>
<?php
} ?>       
</form>
</div>
</div>
<?php } 


function NWCGNIG_newschat_headlines() {
?>
<div class="wrap">
<h1 class="NWCGNIG_center-align">Newsletter Chat Headlines</h1>
<div class="NWCGNIG_center-align">
<br><br>

<?php

$today = getdate();
    $args = array(
    'date_query' => array(
        array(
            'year'  => $today['year'],
            'month' => $today['mon'],
            'day'   => $today['mday'],

        ),
    ),
);
$query = new WP_Query( $args );
$whatsnum = $query->post_count;


   if ( $query->have_posts()) {
       
$checkweb = get_option( 'nw_cng' );
$checklnk = get_option( 'nw_lng' );
$newstitle = strtoupper(get_option( 'posts_title' )); 
$newsfooter = get_option( 'posts_footer' );
$titlec = urlencode( $newstitle );
$footerec = urlencode( $newsfooter );
?>

    <div class="NWCGNIG_center-align NWCGNIG_warning">Ensure Headlines do not contain special characters as <b>&</b> and <b>#.</b></div><br>
<div class= "NWCGNIG_newschat-box">
 <?php while ( $query->have_posts() ) : $query->the_post() ; 
  ?>
  <h4><?php the_title (); ?></h4>
<?php endwhile; 
?> <br>
<div class="NWCGNIG_center-align"><a href="<?php echo home_url() . "/newsletter-chat"; ?>" ><b>Visit your newsletter page</b></a></div>
</div>


</div>

<?php
}
else {
  ?>
  <div class= "NWCGNIG_newschat-box">
  <p class="alert-text">No Posts Published Yet! Check Back Later.</p>
 
</div>
<?php
}
?>
</div>
<?php
}

function NWCGNIG_newschat_support() {
?>
<div class="wrap">
<h1 class="NWCGNIG_center-align">Newsletter Chat Support</h1>
<div class="NWCGNIG_center-align">
<br><br>
<p class="NWCGNIG_final-tip">If you want to learn more, send an email to <b>info@geeky.com.ng</b><br>
</p>
</div>
<?php
?> </div>
<?php } 
?>
