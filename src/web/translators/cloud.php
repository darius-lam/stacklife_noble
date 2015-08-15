<?php

  require_once ('../../../etc/sl_ini.php');
  
  //gets the id "string" of the book.  Find more in .htaccess
  $q = $_GET['query'];

  $q = urlencode($q);
  $offset = $_GET['start'];
  $limit = $_GET['limit']; 
  $search_type = $_GET['search_type'];
  $sort = urlencode($_GET['sort']);

  global $LIBRARYCLOUD_URL;

  //$url = "$LIBRARYCLOUD_URL?key=$LIBRARYCLOUD_KEY&filter=$search_type:$q&limit=$limit&start=$offset&sort=$sort";
  $url = "$NOBLE_URL/$search_type/?searchTerms=$q&count=$limit&startPage=$offset";
   //$url = "$NOBLE_URL/title/?searchTerms=artificial+intelligence&count=20";


  // Get facets and filters
  // TODO: This is ugly. Clean this stuff up.
  /**$incoming = $_SERVER['QUERY_STRING'];
  $facet_list = array();
  foreach (explode('&', $incoming) as $pair) {
      list($key, $value) = explode('=', $pair);
      if ($key == 'facet') {
          $url = $url . "&facet=" . $value;
    }
  }
  
  $filter_list = array();
  $filter_string = '';
    foreach (explode('&', $incoming) as $pair) {
        list($key, $value) = explode('=', $pair);
        if ($key == 'filter') {
            $url = $url . "&filter=" . $value;
      }
    }
  **/

  $json = array();

  $contents = fetch_page($url);

  $xml = simplexml_load_string($contents, "SimpleXMLElement", LIBXML_NOCDATA);
  $j = json_encode($xml); 

  $book_data = json_decode($j, true);

  $hits = $book_data['totalResults'];
  
  $items = $book_data['mods'];
 
  //$facets = $book_data["facets"];
    
  $books_fields = array('id', 'title','creator','measurement_page_numeric','measurement_height_numeric', 'shelfrank', 'pub_date', 'title_link_friendly', 'format', 'loc_call_num_sort_order', 'link');
    
  foreach($items as $item) {
    $title = '';
    $author = '';
    
    //CHANGE TO ISBN
    $id = $item['recordInfo']['recordIdentifier'];
  
    $title_nf = $item['titleInfo']['title'];
    $title_link_friendly = strtolower($title_nf);
    //Make alphanumeric (removes all other characters)
    $title_link_friendly = preg_replace("/[^a-z0-9_\s-]/", "",$title_link_friendly);
    //Clean up multiple dashes or whitespaces
    $title_link_friendly = preg_replace("/[\s-]+/", " ", $title_link_friendly);
    //Remove spaces from end of line.
    $title_link_friendly = preg_replace("/\s+$/", "", $title_link_friendly);
    //Convert whitespaces and underscore to dash
    $title_link_friendly = preg_replace("/[\s_]/", "-", $title_link_friendly);
      
    $shelfrank = 35;
      
      
    if(is_array($item['name'])){
        foreach ($item['name'] as $name){
            $creator = $name['namePart'];
        }
    }else{
        $creator = array($item['name']['namePart']);
    }
      
    if (!empty($item['titleInfo']['title'])) {
        $title =  preg_replace("/[^A-Za-z0-9_\s-]/", "",$item['titleInfo']['title']);

    }else if (!empty($item['titleInfo'][0]['title'])){
        $title = preg_replace("/[^A-Za-z0-9_\s-]/", "",$item['titleInfo'][0]['title']);

        if(property_exists($item['titleInfo'][0], 'nonSort') && !empty($item['titleInfo'][0]['nonSort'])){
            $title = ($item['titleInfo'][0]['nonSort']) . $title;
        }
    }
      
    if (!empty($item['physicalDescription']['extent'])) {
        
        if( preg_match('/([1-9]*\s*)(?=cm)/',$item['physicalDescription']['extent'],$height) ){
            $height_cm = $height[0];
        }
        if( preg_match('/([1-9]*\s*)(?=p)/',$item['physicalDescription']['extent'],$p)) {
            $pages = $p[0];
        }
        
    }
      
    if(!$height_cm || $height_cm > 33 || $height_cm < 20) $height_cm = 27;
    if(!$pages) $pages = 200;
      
    
    $year = intval($item['originInfo']['dateIssued'][1]);
   
    $format = $item['typeOfResource'];
    //need to fix
    if($format == "text"){
        $format = "Book";
    }
      
    $year = substr($year, 0, 4);
    //$format = str_replace(" ", "", $format);
    
    //still don't have sort order
    //$loc_sort_order = $item['loc_call_num_sort_order'];
    $loc_sort_order= 10;
      

    if(!empty($item['identifier'][0]['@attributes']['invalid']) && ($item['identifier'][0]['@attributes']['invalid'] == 'yes')){
        $link = "/item/" . $title_link_friendly . '/' . $item['recordInfo']['recordIdentifier'];   
    }else{
        //may run into errors with this regex.
        $isbn = preg_replace("/\s.*/","",$item['identifier'][0]);
        $link = "/item/" . $title_link_friendly . '/' . $isbn;
    }
    
      
    $books_data   = array($id, $title, $creator, $pages, $height_cm, $shelfrank, $year, $title_link_friendly, $format, $loc_sort_order, $link);
    $temp_array  = array_combine($books_fields, $books_data);
    array_push($json, $temp_array);
  }
    
  $last = $offset + 10;
    
    header('Content-type: application/json');

  if(count($json) == 0 || $offset == -1) {
    echo '{"start": "-1", "num_found": ' . $hits . ', "limit": "0", "docs": ""}'; 
  }
  else {
    //echo '{"start": ' . $last. ', "limit": "' . $limit . '", "num_found": ' . $hits . ', "docs": ' . json_encode($json) . ', "facets": ' . json_encode($facets) . '}'; 
      echo '{"start": ' . $last. ', "limit": "' . $limit . '", "num_found": ' . $hits . ', "docs": ' . json_encode($json) . '}'; 
  }

function fetch_page($url) {

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, $url);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	$contents = curl_exec ($ch);
	
	curl_close ($ch);
	
	return $contents;
}
?>
