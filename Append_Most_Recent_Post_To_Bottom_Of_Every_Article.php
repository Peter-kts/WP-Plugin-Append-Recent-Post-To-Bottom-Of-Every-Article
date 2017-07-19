<?php
/*
Plugin Name: Append Most Recent Post To Bottom Of Every Article
Description: A plugin created as part of the application process for Adweek magazine
Version: 1.0
Author: Panagiotis "Peter" Katsogiannos
Author URI: http://peterkatsogiannos.com
License: MIT
*/

//Just sets the plugin directory into a variable for ease of use
define( 'AMRPTBOEA_DIR_URL', plugin_dir_url( __FILE__ ) );

//Loads the stylesheets included in the plugin
function AMRPTBOEA_add_my_stylesheet() {
    wp_register_style( 'prefix-style', plugins_url('/css/style.css', __FILE__) );
    wp_enqueue_style( 'prefix-style' );
}

//A function to convert the default PHP time() function into a relative time
function AMRPTBOEA_relative_time($post_id) { 
    $post_date = get_the_time('U', $post_id);
    $delta = time() - $post_date;
    if ( $delta < 60 ) {
        echo 'Less than a minute ago';
    }
    elseif ($delta > 60 && $delta < 120){
        echo 'About a minute ago';
    }
    elseif ($delta > 120 && $delta < (60*60)){
        echo strval(round(($delta/60),0)), ' minutes ago';
    }
    elseif ($delta > (60*60) && $delta < (120*60)){
        echo 'About an hour ago';
    }
    elseif ($delta > (120*60) && $delta < (24*60*60)){
        echo strval(round(($delta/3600),0)), ' hours ago';
    }
    else {
        echo the_time('j\<\s\u\p\>S\<\/\s\u\p\> M y g:i a');
    }
}

//Loads the most recent post and then constructs the HTML using the pulled data from the wp_get_recent_posts() function
function AMRPTBOEA_Add_Recent_Post($content) {
    if(!is_admin()){
        wp_reset_query();
        if (is_single()) {
        	$args = array(
				'numberposts' => 1,
				'post_status' => 'publish',
			);
			$recent_posts = wp_get_recent_posts($args);
			echo $content;
			foreach ($recent_posts as $recent) {
				echo "<div id='AMRPTBOEA_Recent_Post_Container'>";
					//Loads the image thumbnail
					if ( has_post_thumbnail($recent["ID"]) ) {
						echo get_the_post_thumbnail($recent["ID"],'thumbnail');
					}
					echo "<div id='AMRPTBOEA_Recent_Post_Text'>";
						echo "<h2>" . (get_the_category($recent["ID"])[0]->name) . "</h2>";
						//The two ID's Mobile_Info and Desktop_Info are there in order to show and hide them when changing screen sizes
						echo "<h3 id='AMRPTBOEA_Mobile_Info'><div>|</div>";
						echo AMRPTBOEA_relative_time($recent["ID"]) . "</h3>";
						echo "<h1>" . get_the_title($recent["ID"]) . "</h1>";
						echo "<h3 id='AMRPTBOEA_Desktop_Info'> By <span>" . get_the_author($recent["ID"]) . "</span> ";
						echo AMRPTBOEA_relative_time($recent["ID"]) . "</h3>";
					echo "</div>";
				echo "</div>";
			}
        }
	}
}

add_action('wp_enqueue_scripts', 'AMRPTBOEA_add_my_stylesheet');
add_filter('the_content', 'AMRPTBOEA_Add_Recent_Post');

//Loads the custom fonts included in the plugin.. I've tried finding a better/cleaner way to do this but this is all I could get to work in a timely manner
echo ("
<style> @font-face {
  font-family: 'Helvetica Neue';  
  src: url('" . AMRPTBOEA_DIR_URL . "fonts/HelveticaNeueLTPro-Md.otf');  
} </style>

<style> @font-face {
  font-family: 'Helvetica Neue Light';  
  src: url('" . AMRPTBOEA_DIR_URL . "fonts/HelveticaNeueLTPro-Lt.otf');   
} </style>

<style> @font-face {
  font-family: 'Helvetica Neue Bold';  
  src: url('" . AMRPTBOEA_DIR_URL . "fonts/HelveticaNeueLTPro-Bd.otf');  
} </style>
");

//In the case your theme doesn't already have thumbnails enabled for whatever reason
add_theme_support( 'post-thumbnails' );
?>