<?php
require_once( dirname( dirname( dirname( dirname( __FILE__ )))) . '/wp-load.php' );
global $wpdb;

$file = __DIR__ .'/results.json';
if(isset($_REQUEST['action'])){
$args = array(
    'hide_empty' => false,
);

$product_categories = get_terms( 'product_cat', $args );
$jsonfull = array();
$i = 0;
foreach($product_categories as $cat){
	
	//Products and Attributes
	$jsonitems = array();
	$args = array( 'post_type' => 'product', 'posts_per_page' => 2, 'product_cat' => $cat->slug );
	$pitem = new WP_Query( $args );
	$p = 0;
	while ( $pitem->have_posts() ) : $pitem->the_post(); global $product;
		$json = array();
		$props = array();
		
		$tickets = new WC_Product_Variable( $product->id);
		$variables = $tickets->get_available_variations();
		$attributes = $product->get_attributes();

		if($product->is_type( 'variable' )){
			$at = 0;
			foreach($attributes as $att)
			{ 	
				$int = wc_attribute_taxonomy_id_by_name( str_replace( "pa_", "", $att['name'] ) );
				$attrtax = $wpdb->get_row( "SELECT * FROM " . $wpdb->prefix . "woocommerce_attribute_taxonomies WHERE attribute_id = '$int'" );
				$attval = wc_get_product_terms( $product->id, $att['name'], array( 'fields' => 'names' ) );
				
				$pro['instructions'] = $attrtax->attribute_label;
				$pro['fieldName'] = $attrtax->attribute_name;
				$pro['prop_type'] = $attrtax->attribute_type;
				$pro['view_type'] = $attrtax->attribute_type;
			
				$atvals = array();
				$atv = 0;
				foreach($attval as $val)
				{
					$atvl = array("title"=>$val,"price"=>"0");
					$atvals[$atv] = (object)$atvl;
					$atv++;
				}
				$pro['options'] = $atvals;		
				$pro['dblink'] = (object) array("ignore_price"=>"");
				$pro['loop'] = '';

				$props[$at] = (object)$pro;
				$at++;
			}
		}

		$json['_id'] = $product->id;
		$json['title'] = $product->post->post_title;
		$json['props'] = $props;
		$json['price'] = $product->price;
		$json['discount'] = '';
		$json['items'] = array();
		$json['only_title'] = '';
		$json['hide'] = ($product->post->post_status != 'publish' ? 1 : '');
		$json['image_url'] = wp_get_attachment_url( get_post_thumbnail_id($product->id) );
		$jsonitems[$p] = (object)$json;
		$p++;
		
	endwhile;
	wp_reset_query();
	
	// Final Array
	$props = array();
	$json = array();
	$json['_id'] = $cat->term_id;
	$json['title'] = $cat->name;
	$json['props'] = $props;
	$json['price'] = '';
	$json['discount'] = '';
	$json['items'] = $jsonitems;
	
	//Final Data
	$jsonfull[$i] = (object)$json;
	$i++;
	
}

$jsonfull =  (object) array("menu"=>$jsonfull);

// Update JSON file data
$fp = fopen($file, 'w');
fwrite($fp, json_encode($jsonfull));
fclose($fp);
header("location:".admin_url("admin.php?page=wpej-product-export&m=1"));
}
else
{
	// Download file
	ignore_user_abort(true);
	set_time_limit(0); // disable the time limit for this script
	 
	$path = __DIR__ .'/'; // change the path to fit your websites document structure
	 
	$dl_file = preg_replace("([^\w\s\d\-_~,;:\[\]\(\).]|[\.]{2,})", '', 'results.json'); // simple file name validation
	$dl_file = filter_var($dl_file, FILTER_SANITIZE_URL); // Remove (more) invalid characters
	$fullPath = $path.$dl_file;
	 
	if ($fd = fopen ($fullPath, "r")) {
		$fsize = filesize($fullPath);
		$path_parts = pathinfo($fullPath);
		$ext = strtolower($path_parts["extension"]);
		switch ($ext) {
			case "pdf":
			header("Content-type: application/pdf");
			header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\""); // use 'attachment' to force a file download
			break;
			// add more headers for other content types here
			default;
			header("Content-type: application/octet-stream");
			header("Content-Disposition: filename=\"".$path_parts["basename"]."\"");
			break;
		}
		header("Content-length: $fsize");
		header("Cache-control: private"); //use this to open files directly
		while(!feof($fd)) {
			$buffer = fread($fd, 2048);
			echo $buffer;
		}
	}
	fclose ($fd);
	exit;
}

?>