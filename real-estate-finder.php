<?php
/*
Plugin Name: Real Estate Finder
Plugin URI: http://www.onlinerel.com/wordpress-plugins/
Description: Plugin "Real Estate Finder" gives visitors the opportunity to use a large database of real estate.
Real estate search for U.S., UK, Canada,  Australia
Version: 2.4
Author: A.Kilius
Author URI: http://www.onlinerel.com/wordpress-plugins/
*/

define(real_estate_finder_URL_RSS_DEFAULT, 'http://www.sellonlineproperty.com/category/real-estate/feed/');
define(real_estate_finder_TITLE, 'Real Estate Finder');
define(real_estate_finder_MAX_SHOWN_ITEMS, 4);

add_action('admin_menu', 'real_estate_finder_menu');
function real_estate_finder_menu() {
 add_menu_page('Real Estate Finder', 'Real Estate Finder', 8, __FILE__, 'real_estate_finder_options');
}

function real_estate_finder_widget_ShowRss($args)
{
	$options = get_option('real_estate_finder_widget');
if( $options == false ) {
		$options[ 'real_estate_finder_widget_url_title' ] = real_estate_finder_TITLE;
		$options[ 'real_estate_finder_widget_RSS_count_items' ] = real_estate_finder_MAX_SHOWN_ITEMS;
	}                                                                                                               
 $RSSurl = real_estate_finder_URL_RSS_DEFAULT;                                                                      

$title = $options[ 'real_estate_finder_widget_url_title' ];
$output = '<!-- Real Estate Finder:  http://www.onlinerel.com/wordpress-plugins/ -->';
$output .= '<form name="form" method="GET" action="http://www.sellonlineproperty.com/" target="_blank">';
  $output .= '<center><b>Property:</b>  <input type="text" id="s"  name="s"  value="" />';                         
$output .= '<input type="submit" name="submit" class="submit" value="Search" /></center> </form>';
 $rss = fetch_feed( $RSSurl );
		if ( !is_wp_error( $rss ) ) :
        $maxitems = $rss->get_item_quantity($options['real_estate_finder_widget_RSS_count_items'] );
			$items = $rss->get_items( 0, $maxitems );
				endif;
	 $output .= '<b>Property For sale:</b>';	
$output .= '<ul>';	
	if($items) { 
 			foreach ( $items as $item ) :
				// Create post object                                                           
  $titlee = trim($item->get_title()); 
  $output .= '<li> <a href="';
 $output .=  $item->get_permalink();
  $output .= '"  title="'.$titlee.'" target="_blank">';
   $output .= $titlee.'</a> ';
	 $output .= '</li>'; 
   		endforeach;		
	}
			$output .= '</ul> ';	
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
if ( $_POST["real_estate_finder_widget_RSS_count_items"] ) {
		$newoptions['real_estate_finder_widget_url_title'] = strip_tags(stripslashes($_POST["real_estate_finder_widget_url_title"]));
			$newoptions['real_estate_finder_widget_RSS_count_items'] = strip_tags(stripslashes($_POST["real_estate_finder_widget_RSS_count_items"]));
	}	
if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('real_estate_finder_widget', $options);		
	}
	$real_estate_finder_widget_url_title = wp_specialchars($options['real_estate_finder_widget_url_title']);
	$real_estate_finder_widget_RSS_count_items = $options['real_estate_finder_widget_RSS_count_items'];
	
	?>

	<p><label for="real_estate_finder_widget_url_title"><?php _e('Title:'); ?> <input style="width: 350px;" id="real_estate_finder_widget_url_title" name="real_estate_finder_widget_url_title" type="text" value="<?php echo $real_estate_finder_widget_url_title; ?>" /></label></p>
 
	<p><label for="real_estate_finder_widget_RSS_count_items"><?php _e('Count Items To Show:'); ?> <input  id="real_estate_finder_widget_RSS_count_items" name="real_estate_finder_widget_RSS_count_items" size="2" maxlength="2" type="text" value="<?php echo $real_estate_finder_widget_RSS_count_items?>" /></label></p>
	 </p>
<?php
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
<p> <h3>Add the widget "Real Estate Finder"  to your sidebar from <a href="<? echo "./widgets.php";?>"> Appearance->Widgets</a> and configure the widget options.</h3>
<h3>More <a href="http://www.onlinerel.com/wordpress-plugins/" target="_blank"> WordPress Plugins</a></h3>
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