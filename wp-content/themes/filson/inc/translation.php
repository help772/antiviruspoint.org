<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																		Option Translation
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_t($id) {
	global $smof_data;
	$t = !empty( $smof_data['t_'.$id]) ?  $smof_data['t_'.$id]  : hexwp_translation($id);
	return $t;
} 

/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																		Translation
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_translation($id= false) {
 	$translation = array(
	'all'					=>  esc_html__('All', 'hexwp'),
	'full_list'				=>  esc_html__('View All', 'hexwp'),
 	'category'					=>  esc_html__('All Categories', 'hexwp'),
	'homepage'					=>  esc_html__('Homepage', 'hexwp'),
	'archives'					=>  esc_html__('Archives', 'hexwp'),
	'author'					=>  esc_html__('Author', 'hexwp'),
	'page'						=>  esc_html__('Page', 'hexwp'),
	'404'						=>  esc_html__('Error 404', 'hexwp'),
	'of'						=>  esc_html__('of', 'hexwp'),
	'first'						=>  esc_html__('First', 'hexwp'),
	'last'						=>  esc_html__('Last', 'hexwp'),
	'previous'					=>  esc_html__('Previous', 'hexwp'),
	'next'						=>  esc_html__('Next', 'hexwp'),
	'edit'						=>  esc_html__('Edit', 'hexwp'),
	'by'						=>  esc_html__('By', 'hexwp'),
	'0'							=>  esc_html__('0', 'hexwp'),
	'1'							=>  esc_html__('1', 'hexwp'),
	'commetsoff'				=>  esc_html__('Comments Off', 'hexwp'),
	'read_more'					=>  esc_html__('Read More', 'hexwp'),
	'view_more'					=>  esc_html__('View More', 'hexwp'),
	'load_more'					=>  esc_html__('Load more', 'hexwp'),
	'welcome'					=>  esc_html__('Welcome', 'hexwp'),
	'dashboard'					=>  esc_html__('Dashboard', 'hexwp'),
	'profile'					=>  esc_html__('Your Profile', 'hexwp'),
	'logout'					=>  esc_html__('Log Out', 'hexwp'),
	'login'						=>  esc_html__('Log In', 'hexwp'),
	'singin'						=>  esc_html__('Sing In', 'hexwp'),
	'account'					=>  esc_html__('Account', 'hexwp'),
	'menu'					=>  esc_html__('Menu', 'hexwp'),
	'mywishlist'					=>  esc_html__('My Wishlist', 'hexwp'),
	'username'					=>  esc_html__('Username', 'hexwp'),
	'password'					=>  esc_html__('Password', 'hexwp'),
	'remember'					=>  esc_html__('Remember Me', 'hexwp'),
	'register'					=>  esc_html__('Register', 'hexwp'),
	'lostpassword'				=>  esc_html__('Lost Your Password?', 'hexwp'),
	'view'						=>  esc_html__('View', 'hexwp'),
	'views'						=>  esc_html__('Views', 'hexwp'),
	'tags'						=>  esc_html__('Tags', 'hexwp'),
	'years'						=>  esc_html__('Years', 'hexwp'),
	'year'						=>  esc_html__('Year', 'hexwp'),
	'months'					=>  esc_html__('Months', 'hexwp'),
	'month'						=>  esc_html__('Month', 'hexwp'),
	'days'						=>  esc_html__('Days', 'hexwp'),
	'day'						=>  esc_html__('Day', 'hexwp'),
	'hours'						=>  esc_html__('Hours', 'hexwp'),
	'hour'						=>  esc_html__('Hour', 'hexwp'),
	'minutes'					=>  esc_html__('Mins', 'hexwp'),
	'minute'					=>  esc_html__('Minute', 'hexwp'),
	'seconds'					=>  esc_html__('Secs', 'hexwp'), 
	'second'					=>  esc_html__('Second', 'hexwp'), 
 	'ago'						=>  esc_html__('ago', 'hexwp'),
	'pages'						=>  esc_html__('Pages', 'hexwp'),
	'share'						=>  esc_html__('Share this Post', 'hexwp'),
	'at'						=>  esc_html__('at', 'hexwp'),
	'yourcomment'				=>  esc_html__('Your Comment is Awaiting Moderation.', 'hexwp'),
 	'pingback'					=>  esc_html__('Pingback', 'hexwp'),
	'search'					=>  esc_html__('Search', 'hexwp'),
	'search_for'				=>  esc_html__('Search Results for', 'hexwp'),
	'search_for'				=>  esc_html__('Search Results for', 'hexwp'),
	'availability'				=>  esc_html__('Availability', 'hexwp'),
	'nocommentsyet'				=>  esc_html__('No Comments Yet', 'hexwp'),
	'commentalready'			=>  esc_html__('Comment', 'hexwp'),
	'commentsalready'			=>  esc_html__('Comments', 'hexwp'),
 	'commentsclosed'			=>  esc_html__('Comments are Closed.', 'hexwp'),
 	'tagarchives'				=>  esc_html__('Tag Archives', 'hexwp'),
 	'opps404'					=>  esc_html__('Oops, This Page Could Not Be Found!','hexwp'),
 	'opps404_dese'				=>  esc_html__('The page you are looking for might have been removed, had its name changed, or is temporarily unavailable..','hexwp'),
 	'sorry'						=>  esc_html__("Sorry, but nothing matched your search terms. Please try again with some different keywords.", 'hexwp'),
 	'compare'					=>  esc_html__("Compare", 'hexwp'),
	'instock'					=>  esc_html__("In Stock", 'hexwp'),
	'outstock'					=>  esc_html__('Out of Stock', 'hexwp'),
	'new'						=>  esc_html__("New", 'hexwp'),
	'hot'						=>  esc_html__("Hot", 'hexwp'),
	'featured'					=>  esc_html__("Featured", 'hexwp'),
	'sale'						=>  esc_html__('Sale', 'hexwp'), 
	'cart'					=>  esc_html__('My Cart', 'hexwp'),
	'mycart'					=>  esc_html__('My Cart', 'hexwp'),
	'searchitem'				=>  esc_html__('Search...', 'hexwp'),
 	'salet'						=>  esc_html__('Sale!', 'hexwp'),
	'subtotal'					=>  esc_html__('Subtotal', 'hexwp'),	 
	'noproducts'				=>  esc_html__('No products in the cart.', 'hexwp'),
	'relatedproducts'			=>  esc_html__('Related products', 'hexwp'),
	'callusfree'				=>  esc_html__('Call Us Free', 'hexwp'), 
	'or'						=>  esc_html__('or', 'hexwp'),
	'myaccount'						=>  esc_html__('My Account', 'hexwp'),
	'real_name'					=>  esc_html__('Real Name', 'hexwp'),
	'website'					=>  esc_html__('Website', 'hexwp'),	 
	'phone'						=>  esc_html__('Phone', 'hexwp'),
	'phonenumber'						=>  esc_html__('Phone number', 'hexwp'),
	'contact_us'						=>  esc_html__('Contact us', 'hexwp'),
	'noresults'						=>  esc_html__('No results', 'hexwp'),
  	'skills'					=>  esc_html__('Skills', 'hexwp'),
	'removethisitem'			=>  esc_html__('Remove this item', 'hexwp'),
	'outofstock'			=>  esc_html__('Out of Stock', 'hexwp'),
	'favorite'			=>  esc_html__('Favorite', 'hexwp'),
	'noresults'						=>  esc_html__('No Results', 'hexwp'),	
		'full_list'			=>  esc_html__('View All', 'hexwp'),
	'category_more'			=>  esc_html__('More Category', 'hexwp'),
	'category_close'			=>  esc_html__('Close', 'hexwp'),
	
	 
  	);
 	if($id =='array'){
		return $translation;
 	}else{
		return isset($translation[$id])?$translation[$id]:'';
 	}
}?>