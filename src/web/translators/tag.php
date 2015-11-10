<?php

  require_once(__DIR__ . '/../../../etc/sl_ini.php');
  connect_db();
  
  $tag = $_GET['query'];
	$limit = $_GET['limit'];
	$start = $_GET['start'];
	$sql_limit = "LIMIT $start, $limit";
	$json = array();
	
	$count_query = "SELECT COUNT(DISTINCT item_id) FROM sl_tags WHERE tag = '$tag'";
	$hits = 0;
	$count_result = mysql_query($count_query);
	$row = mysql_fetch_row($count_result);

	$hits = $row[0];
	
	$userList  = "SELECT item_id FROM sl_tags WHERE tag='$tag' GROUP BY item_id $sql_limit";		
    				
  $user_result = mysql_query($userList);
  $user_books = array();

  while($row = mysql_fetch_row($user_result)) {
    $uid = $row[0];
    array_push($user_books, $uid);
  }

//Mostly copied from cloud.php
global $NOBLE_URL;

foreach($user_books as $id) {
  
            $url = "http://evergreen.noblenet.org/opac/extras/supercat/retrieve/mods/record/$id";

        $contents = fetch_page($url);

        $xml = simplexml_load_string($contents, "SimpleXMLElement", LIBXML_NOCDATA);
        $j = json_encode($xml); 

        $book_data = json_decode($j);
        
        if(is_array($book_data->mods)){
            $items = $book_data->mods;
        }else{
            $items = array($book_data->mods);
        }

        $books_fields = array('id', 'title','creator','measurement_page_numeric','measurement_height_numeric', 'shelfrank', 'pub_date', 'title_link_friendly', 'format', 'loc_call_num_sort_order', 'link');
        
        foreach($items as $item) {
            $title = '';
            $author = '';

            $shelfrank = 35;


            if(property_exists($item,'name')){
                if(is_array($item->name)){
                    foreach ($item->name as $name){
                        array_push($creator,$name->namePart);
                    }
                }else{
                    if(is_array($item->name->namePart)){
                         $creator = array($item->name->namePart[0]);
                    }else{
                        $creator = array($item->name->namePart);
                    }
                }
              }else{
                $creator = "N/a";  
              }


            if (!empty($item->titleInfo->title)) {
                $title =  preg_replace("/[^A-Za-z0-9_\s-]/", "",$item->titleInfo->title);

            }else if (!empty($item->titleInfo[0]->title)){
                $title = preg_replace("/[^A-Za-z0-9_\s-]/", "",$item->titleInfo[0]->title);

                if(property_exists($item->titleInfo[0], 'nonSort') && !empty($item->titleInfo[0]->nonSort)){
                    $title = ($item->titleInfo[0]->nonSort) . $title;
                }
            }

            $title_nf = $title;
            $title_link_friendly = strtolower($title_nf);
            //Make alphanumeric (removes all other characters)
            $title_link_friendly = preg_replace("/[^a-z0-9_\s-]/", "",$title_link_friendly);
            //Clean up multiple dashes or whitespaces
            $title_link_friendly = preg_replace("/[\s-]+/", " ", $title_link_friendly);
            //Remove spaces from end of line.
            $title_link_friendly = preg_replace("/\s+$/", "", $title_link_friendly);
            //Convert whitespaces and underscore to dash
            $title_link_friendly = preg_replace("/[\s_]/", "-", $title_link_friendly);

            if (!empty($item->physicalDescription->extent)) {

                if( preg_match('/([1-9]*\s*)(?=cm)/',$item->physicalDescription->extent,$height) ){
                    $height_cm = $height[0];
                }
                if( preg_match('/([1-9]*\s*)(?=p)/',$item->physicalDescription->extent,$p)) {
                    $pages = $p[0];
                }

            }

            if(!$height_cm || $height_cm > 33 || $height_cm < 20) $height_cm = 27;
            if(!$pages) $pages = 200;

            if(is_array($item->originInfo->dateIssued)){
                $year = intval($item->originInfo->dateIssued[1]);
            }else{
                $year = intval($item->originInfo->dateIssued);
            }

            $format = "Book";

            $year = substr($year, 0, 4);
            //$format = str_replace(" ", "", $format);

            //still don't have sort order
            //$loc_sort_order = $item->loc_call_num_sort_order'];
            $loc_sort_order= 10;

            $push = true;

            // I love inconsistent data!
            $itemid = $item->identifier;
            if (is_array($item->identifier)) {
              $itemid = $item->identifier[0];
            }

            if(!empty($itemid->{'@attributes'}->invalid) && ($itemid->{'@attributes'}->invalid == 'yes')){
                //$link = "/item/" . $title_link_friendly . '/' . $item->recordInfo->recordIdentifier;
                //$id = 000;

                $hits = $hits - 1;
                $push = false;
                //we simply don't push this item if it doesn't have a valid ISBN.
            }else{
                //may run into errors with this regex.
                //check to see if identifier field is blank
                if((!is_string($itemid))){
                    $push = false;
                    $hits = $hits - 1;
                }else{
                    $isbn = preg_replace("/\s.*/","",$itemid);
                    //check to see if isbn is either 13 or 10 characters long
                    if(strlen($isbn) != 13 && strlen($isbn) != 10){
                        $push = false;
                        $hits = $hits - 1;
                    }else{
                        $id=$item->recordInfo->recordIdentifier;
                        $link = $www_root . "/item/" . $title_link_friendly . '/' . $id;
                    }
                }
            }

            $books_data   = array($id, $title, $creator, $pages, $height_cm, $shelfrank, $year, $title_link_friendly, $format, $loc_sort_order, $link);
            $temp_array  = array_combine($books_fields, $books_data);
            if($push){
               array_push($json, $temp_array);
            }
        }
    }

  if(count($json) == 0 || $start > 1) {
    echo '{"start": "-1", "num_found": ' . $hits . ', "limit": "0", "docs": ""}'; 
  }
  else {
    echo '{"start": "1", "limit": "' . $limit . '", "num_found": ' . $hits . ', "docs": ' . json_encode($json) . '}'; 
  }
    
    mysql_close();

function fetch_page($url) {
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL,
	$url);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	$contents = curl_exec ($ch);
	
	curl_close ($ch);
	
	return $contents;
}

function connect_db() {
	global $hostName;
	global $userName;
	global $pw;
			
	if(!($link=mysql_pconnect($hostName, $userName, $pw))) 
	{
		echo "before error<br />";
		echo "error connecting to host";
		exit;
	}
	else
	
	mysql_select_db("stackview");
	
	// Following directive is essential for proper utf-8 resolution at the client
	$set_utf8_query = "SET NAMES 'utf8'";
	//print "here is set_utf8_query: [$set_utf8_query]<br />";
	$result_utf8 = mysql_query($set_utf8_query, $link);
	
	if (!$result_utf8) 
	{
		echo 'Could not run set_utf8_query: ' . mysql_error();
		//exit;
	}
}
?>