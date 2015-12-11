<?php

    error_reporting(E_ALL ^ E_NOTICE);
  require_once (__DIR__ .  '/../../../etc/sl_ini.php');

    if($live){
        require_once('/var/local/noble/circ/circ_counts.php');
    }

  //Queries come from ajax calls in item.js
  $q = $_GET['query'];
  //$q = urlencode($q);
  //$test_query = preg_replace("(/)", "+",$q);
  $offset = $_GET['start'];
  $limit = $_GET['limit']; 
  $library = $_SESSION['school'];
  $search_type = $_GET['search_type'];

  global $NOBLE_URL;

  if($search_type == 'recordId'){
    $url = "http://evergreen.noblenet.org/opac/extras/supercat/retrieve/marcxml/record/$q";
  }else{
    $url = "$NOBLE_URL/$library/marcxml/$search_type/?searchTerms=$q&count=$limit&startPage=$offset";
  }

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
  $libs = array('BEVERLY','BUNKERHILL','DANVERS','ENDICOTT','EVERETT','GLOUCESTER','GORDON','LYNNFIELD','LYNN','MARBLEHEAD','MELROSE','MERRIMACK','MIDDLESEX','MONTSERRAT','NORTHSHORE','NORTHERNESSEX','PEABODY','READING','REVERE','SALEM','SALEMSTATE','SAUGUS','STONEHAM','SWAMPSCOTT','WAKEFIELD','WINTHROP','PANO','PANA','PANB','PANC', 'PANG', 'PANI', 'PANK','PANP');
    $j = json_encode($xml);
    $item = json_decode($j);
    $shelfrank = 1;
      if($live){
          foreach($libs as $library){
              $shelfrank = $shelfrank + getNOBLECirculationCount(array($item->mods->recordInfo->recordIdentifier),$library)[$item->mods->recordInfo->recordIdentifier];
          }
      }

  $xml->record->shelfrank = $shelfrank;
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