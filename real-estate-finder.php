<?php
/*
Plugin Name: Real Estate Finder
Plugin URI: http://www.onlinerel.com/wordpress-plugins/
Version: 1.6
Description: Plugin "Real Estate Finder" gives visitors the opportunity to use a large database of real estate.
Real estate search for U.S., Canada, UK, Australia
Author: A.Kilius
Author URI: http://www.onlinerel.com/wordpress-plugins/
*/

define(real_estate_finder_URL_RSS_DEFAULT, 'http://www.worldestatesite.com/category/property/feed/');
define(real_estate_finder_TITLE, 'Real Estate Finder');
define(real_estate_finder_MAX_SHOWN_ITEMS, 6);

function real_estate_finder_widget_ShowRss($args)
{
	//@ini_set('allow_url_fopen', 1);	
	if( file_exists( ABSPATH . WPINC . '/rss.php') ) {
		require_once(ABSPATH . WPINC . '/rss.php');		
	} else {
		require_once(ABSPATH . WPINC . '/rss-functions.php');
	}
	
	$options = get_option('real_estate_finder_widget');

	if( $options == false ) {
		$options[ 'real_estate_finder_widget_url_title' ] = real_estate_finder_TITLE;
		$options[ 'real_estate_finder_widget_RSS_count_items' ] = real_estate_finder_MAX_SHOWN_ITEMS;
	}

 $RSSurl = real_estate_finder_URL_RSS_DEFAULT;
	$messages = fetch_rss($RSSurl);
	$title = $options[ 'real_estate_finder_widget_url_title' ];
$output = '<!-- Real Estate Finder:  http://www.onlinerel.com/wordpress-plugins/ -->';
$output .= '<form name="forma" method="post" action="http://www.worldestatesite.com/real-estate-search/" target="_blank">
<b>Country: </b>
<select name="country" id="country" style="width:150px;">
<option value="USA"  >USA</option>
<option value="Canada"  >Canada</option>
<option value="UK"  >UK</option>
<option value="Australia">Australia</option>
</select> <br /><b>Location:</b><br />  
 <input type="text" style="width:160px;"  name="location" /> <br />';

 $output .= '<b>Type:</b> 
<select name="listing" style="width:150px;">
<option value="">Select</option>
<option value="foreclosure" >foreclosure</option>
<option value="for sale" >for sale</option>
<option value="for rent" >for rent</option>
<option value="new home" >new home</option>
<option value="room for rent" >room for rent</option>
<option value="sublet" >sublet</option>
</select>
 <br />
	 <b>Property:</b>
<select name="property" style="width:150px;" >
<option value="">Select</option>
<option value="apartment" >apartment</option>
<option value="commercial" >commercial</option>
<option value="condo" >condo</option>
<option value="coop" >coop</option>
<option value="farm" >farm</option>
<option value="land" >land</option>
<option value="manufactured" >manufactured</option>
<option value="multifamily" >multifamily</option>
<option value="ranch" >ranch</option>
<option value="single family" >single family</option>
<option value="tic" >tic</option>
<option value="townhouse" >townhouse</option>
</select><br />'; 
$output .= '<center><input type="submit" name="submit" class="submit" value="Search" /></center> </form><br />';
	$messages_count = count($messages->items);
	if($messages_count != 0){
	 $output .= '<b>Property For sale:</b>';	
		$output .= '<ul>';		
		for($i=0; $i<$options['real_estate_finder_widget_RSS_count_items'] && $i<$messages_count; $i++)
		{			
			$output .= '<li>';
				$output .= '<a target="_blank" href="'.$messages->items[$i]['link'].'">'.$messages->items[$i]['title'].'</a></span>';						
				$output .= '</li>';
		}
		$output .= '</ul>';
	}	
	extract($args);	
	?>
	<?php echo $before_widget; ?>
	<?php echo $before_title . $title . $after_title; ?>	
	<?php echo $output; ?>
	<?php echo $after_widget; ?>
	<?php	
}

function real_estate_finder_widget_Admin()
{
	$options = $newoptions = get_option('real_estate_finder_widget');	
	//default settings
	if( $options == false ) {
		$newoptions[ 'real_estate_finder_widget_url_title' ] = real_estate_finder_TITLE;
		$newoptions['real_estate_finder_widget_RSS_count_items'] = real_estate_finder_MAX_SHOWN_ITEMS;		
	}
	if ( $_POST["real_estate_finder_widget-submit"] ) {
		$newoptions['real_estate_finder_widget_url_title'] = strip_tags(stripslashes($_POST["real_estate_finder_widget_url_title"]));
			$newoptions['real_estate_finder_widget_RSS_count_items'] = strip_tags(stripslashes($_POST["real_estate_finder_widget_RSS_count_items"]));
	}	
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('real_estate_finder_widget', $options);		
	}
	$real_estate_finder_widget_url_title = wp_specialchars($options['real_estate_finder_widget_url_title']);
	$real_estate_finder_widget_RSS_count_items = $options['real_estate_finder_widget_RSS_count_items'];
	
	?><form method="post" action="">	

	<p><label for="real_estate_finder_widget_url_title"><?php _e('Title:'); ?> <input style="width: 350px;" id="real_estate_finder_widget_url_title" name="real_estate_finder_widget_url_title" type="text" value="<?php echo $real_estate_finder_widget_url_title; ?>" /></label></p>
 
	<p><label for="real_estate_finder_widget_RSS_count_items"><?php _e('Count Items To Show:'); ?> <input  id="real_estate_finder_widget_RSS_count_items" name="real_estate_finder_widget_RSS_count_items" size="2" maxlength="2" type="text" value="<?php echo $real_estate_finder_widget_RSS_count_items?>" /></label></p>
	
	<br clear='all'></p>
	<input type="hidden" id="real_estate_finder_widget-submit" name="real_estate_finder_widget-submit" value="1" />	
	</form>
	<?php
}

add_action('admin_menu', 'real_estate_finder_menu');

function real_estate_finder_menu() {
	add_options_page('Real Estate Finder', 'Real Estate Finder', 8, __FILE__, 'real_estate_finder_options');
}
add_filter("plugin_action_links", 'real_estate_finder_ActionLink', 10, 2);
function real_estate_finder_ActionLink( $links, $file ) {
	    static $this_plugin;		
		if ( ! $this_plugin ) $this_plugin = plugin_basename(__FILE__); 
        if ( $file == $this_plugin ) {
			$settings_link = "<a href='".admin_url( "options-general.php?page=".$this_plugin )."'>". __('Settings') ."</a>";
			array_unshift( $links, $settings_link );
		}
		return $links;
	}
function real_estate_finder_options() {	
	?>
	<div class="wrap">
		<h2>Real Estate Finder</h2>
<p><b>Plugin "Real Estate Finder" gives visitors the opportunity to use a large database of real estate.
Real estate search for U.S., Canada, UK, Australia</b> </p>
<p> <h3>Add the widget "Real Estate Finder"  to your sidebar from <a href="<? echo "./widgets.php";?>"> Appearance->Widgets</a> and configure the widget options.</h3></p>
 <hr /> <hr />
 <h2>Blog Promotion</h2>
<p><b>If you produce original news or entertainment content, you can tap into one of the most technologically advanced traffic exchanges among blogs! Start using our Blog Promotion plugin on your site and receive 150%-300% extra traffic free! 
Idea is simple - the more traffic you send to us, the more we can send you back.</b> </p>
 <h3>Get plugin <a target="_blank" href="http://wordpress.org/extend/plugins/blog-promotion/">Blog Promotion</h3></a> 
 <hr />
  <h2>Funny photos</h2>
<p><b>Plugin "Funny Photos" displays Best photos of the day and Funny photos on your blog. There are over 5,000 photos.
Add Funny Photos to your sidebar on your blog using  a widget.</b> </p>
 <h3>Get plugin <a target="_blank" href="http://wordpress.org/extend/plugins/funny-photos/">Funny photos</h3></a> 
 <hr />
 <h2>Jobs Finder</h2>
<p><b>Plugin "Jobs Finder" gives visitors the opportunity to more than 1 million offer of employment.
Jobs search for U.S., Canada, UK, Australia</b> </p>
<h3>Get plugin <a target="_blank" href="http://wordpress.org/extend/plugins/jobs-finder/">Jobs Finder</h3></a>
 <hr />
 <h2>Funny video online</h2>
<p><b>Plugin "Funny video online" displays Funny video on your blog. There are over 10,000 video clips.
Add Funny YouTube videos to your sidebar on your blog using  a widget.</b> </p>
 <h3>Get plugin <a target="_blank" href="http://wordpress.org/extend/plugins/funny-video-online/">Funny video online</h3></a> 
 <hr />
		<h2>Recipe of the Day</h2>
<p><b>Plugin "Recipe of the Day" displays categorized recipes on your blog. There are over 20,000 recipes in 40 categories. Recipes are saved on our database, so you don't need to have space for all that information.</b> </p>
<h3>Get plugin <a target="_blank" href="http://wordpress.org/extend/plugins/recipe-of-the-day/">Recipe of the Day</h3></a>
 <hr />
  		<h2>Joke of the Day</h2>
<p><b>Plugin "Joke of the Day" displays categorized jokes on your blog. There are over 40,000 jokes in 40 categories. Jokes are saved on our database, so you don't need to have space for all that information. </b> </p>
 <h3>Get plugin <a target="_blank" href="http://wordpress.org/extend/plugins/joke-of-the-day/">Joke of the Day</h3></a>
   <hr />
 
 <h2>WP Social Bookmarking</h2>
<p><b>WP-Social-Bookmarking plugin will add a image below your posts, allowing your visitors to share your posts with their friends, on FaceBook, Twitter, Myspace, Friendfeed, Technorati, del.icio.us, Digg, Google, Yahoo Buzz, StumbleUpon.</b></p>
<p><b>Plugin suport sharing your posts feed on <a href="http://www.onlinerel.com/">OnlineRel</a>. This helps to promote your blog and get more traffic.</b></p>
<p>Advertise your real estate, cars, items... Buy, Sell, Rent. Free promote your site:
<ul>
	<li><a target="_blank" href="http://www.onlinerel.com/">OnlineRel</a></li>
	<li><a target="_blank" href="http://www.easyfreeads.com/">Easy Free Ads</a></li>
	<li><a target="_blank" href="http://www.worldestatesite.com/">World Estate Site, Sell your Home, Search Homes</a></li>
</ul>
<h3>Get plugin <a target="_blank" href="http://wordpress.org/extend/plugins/wp-social-bookmarking/">WP Social Bookmarking</h3></a>
</p>
	</div>
	<?php
}

function real_estate_finder_widget_Init()
{
  register_sidebar_widget(__('Real Estate Finder'), 'real_estate_finder_widget_ShowRss');
  register_widget_control(__('Real Estate Finder'), 'real_estate_finder_widget_Admin', 500, 250);
}
add_action("plugins_loaded", "real_estate_finder_widget_Init");
?>