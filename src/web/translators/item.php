<?php
    error_reporting(E_ALL ^ E_NOTICE);

  require_once ('../../../etc/sl_ini.php');
  //Queries come from ajax calls in item.js
  $q = $_GET['query'];
  //$q = urlencode($q);
  //$test_query = preg_replace("(/)", "+",$q);
  $offset = $_GET['start'];
  $limit = $_GET['limit']; 
  $search_type = $_GET['search_type'];

  global $NOBLE_URL;

  //$url = "$NOBLE_URL/$search_type/?searchTerms=$q&count=$limit&startPage=$offset";
  $url = "$NOBLE_URL/title/?searchTerms=artificial+intelligence&count=1";

  // Get facets and filters
  // TODO: This is ugly. Clean this stuff up.
  /** $incoming = $_SERVER['QUERY_STRING'];
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

  $contents = fetch_page($url);
  //data from noble catalog is in xml, so we parse it into JSON
  $xml = simplexml_load_string($contents, "SimpleXMLElement", LIBXML_NOCDATA);
  $json = json_encode($xml); 

  echo $json;

function fetch_page($url) {

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, $url);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	$contents = curl_exec ($ch);
	
	curl_close ($ch);
	
	return $contents;
}
?>