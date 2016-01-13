<?php

  require_once (__DIR__ .  '/../../../etc/sl_ini.php');

  if($live){
    require_once('/var/local/noble/circ/circ_counts.php');
  }

  //gets the id "string" of the book.  Find more in .htaccess
  $id = urlencode($_GET['query']);
  $offset = $_GET['start'];
  $limit = $_GET['limit'];
  $library = $_GET['school'];
  $direction = $_GET['direction'];
 
  //$library = "PANO";
  //$search_type = $_GET['search_type'];
  $sort = urlencode($_GET['sort']);
    
  //Searching by call number
    //$url = "http://catalog.noblenet.org/opac/extras/browse/xml-full/call_number/PANO/$q";
    $url = "http://catalog.noblenet.org/opac/extras/browse/xml-full/call_number/$library/$id";
    //$url = "http://catalog.noblenet.org/opac/extras/browse/xml-full/call_number/PANO/500%20B84s";

  $json = array();

  $contents = fetch_page($url);

  $xml = simplexml_load_string($contents, "SimpleXMLElement", LIBXML_NOCDATA);
  $book_data = $xml;

  //---------------------
  //Begin Parsing Data
  //---------------------

  $hits = sizeOf($book_data->volume);
  $items = $book_data->volume;
  

  $books_fields = array('id', 'title','creator','measurement_page_numeric','measurement_height_numeric', 'shelfrank', 'pub_date', 'title_link_friendly', 'format', 'loc_call_num_sort_order', 'link');
  
  foreach($items as $item) {
    $title = '';
    $author = '';
    $loc_sort_order = (string)$item->attributes()->label;
      
    //Call number data will be in $item->{@'attributes'}
    $it = $item->record->datafield;
      
    //Loop through all fields and compare them one by one
    foreach($it as $field){
        
        //Author(s)
        if($field->attributes()->tag == '100'){
            $creator  = array(json_decode(json_encode($field->subfield),true)[0]);
        }
        
        //Title and title_link_friendly
        if($field->attributes()->tag == '245'){
            $title = "";
                
            foreach($field->subfield as $fi){
                if($fi->attributes()->code == 'a'){
                    $title = $title . (string) $fi;
                }

                if($fi->attributes()->code == 'b'){
                    $title = $title . (string) $fi;
                }
            }
            
            $title =  preg_replace("/[^A-Za-z0-9_\s-]/", "",$title);
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
        }
        
        //physical description
        if($field->attributes()->tag == '300'){
            foreach($field->subfield as $fi){
                if($fi->attributes()->code == 'a'){
                    if(preg_match('/([1-9]*\s*)(?=p)/',$fi,$p)) {
                        $pages = (string) $p[0];
                    }
                }

                if($fi->attributes()->code == 'c'){
                    if(preg_match('/([1-9]*\s*)(?=cm)/',$fi,$height) ){
                        $height_cm = (string) $height[0];
                    }
                }
            }
        }
        
        //format
        $format = "Book";
        
        //pub_date
        if($field->attributes()->tag == '260'){
            foreach($field->subfield as $fi){
                if($fi->attributes()->code == 'c'){
                    $pub_date = (string) $fi;
                }
            }
        }
        
        //RecordId
        if($field->attributes()->tag == '901'){
            foreach($field->subfield as $fi){
                if($fi->attributes()->code == 'c'){
                    $id = (int) $fi;
                }
            }
        }
        
        //Call number; We're using the volume->label attribute for now
        //$loc_sort_order = $item->loc_call_num_sort_order'];
        /**
        if($field->attributes()->tag == '092'){
            $loc_sort_order = "";
            foreach($field->subfield as $fi){
                $loc_sort_order .= (string) $fi . ' ';
            }
        }**/
        
        //ISBN

        if($field->attributes()->tag == '020'){
            $isbn = preg_replace("/\s.*/","",$field->subfield[0][0]);
        }
        
    }
      //-------------
      // Circulation Data
      //-------------

      $libs = array('BEVERLY','BUNKERHILL','DANVERS','ENDICOTT','EVERETT','GLOUCESTER','GORDON','LYNNFIELD','LYNN','MARBLEHEAD','MELROSE','MERRIMACK','MIDDLESEX','MONTSERRAT','NORTHSHORE','NORTHERNESSEX','PEABODY','READING','REVERE','SALEM','SALEMSTATE','SAUGUS','STONEHAM','SWAMPSCOTT','WAKEFIELD','WINTHROP','PANO','PANA','PANB','PANC', 'PANG', 'PANI', 'PANK','PANP');

      if($live){
          $shelfrank = 1;
          foreach($libs as $library){
              $shelfrank = $shelfrank + getNOBLECirculationCount(array($item->recordInfo->recordIdentifier),$library)[$item->recordInfo->recordIdentifier];
          } 
          if($shelfrank >= 400){
            $shelfrank = 100;
          }else{
            $shelfrank = floor($shelfrank/4);
          }
      }else{
          $shelfrank = 30;
      }
      
      //$shelfrank = rand(1,100);
      
    $pub_date = substr($pub_date, 0, 4);
    if(!$height_cm || $height_cm > 33 || $height_cm < 20) $height_cm = 27;
    if(!$pages) $pages = 200;
    $push = true;
    
    if(!isset($isbn)){
        $hits = $hits - 1;
        $push = false;
        //we simply don't push this item if it doesn't have a valid ISBN.
    }else{
        if((!is_string($isbn)) || (strlen($isbn) != 13 && strlen($isbn) != 10)){
            $push = false;
            $hits = $hits - 1;
        }
    }
      
    $link = $www_root . "/item/" . $title_link_friendly . '/' . $id;

    $books_data   = array($id, $title, $creator, $pages, $height_cm, $shelfrank, $pub_date, $title_link_friendly, $format, $loc_sort_order, $link);
    $temp_array  = array_combine($books_fields, $books_data);
    if($push){
       array_push($json, $temp_array);
    }
  }

  //$last = $offset + 10;
    $last = $offset;
    header('Content-type: application/json');

  if(count($json) == 0 || $offset == -1) {
    echo '{"start": "-1", "num_found": ' . $hits . ', "limit": "0", "docs": ""}';
  }
  else {
      if($direction == 'center'){
          echo '{"start": ' . $last. ', "limit": "' . $limit . '", "num_found": ' . $hits . ', "docs": ' . json_encode($json) . '}';
      }else if($direction == 'up'){
          echo '{"start": ' . $last. ', "limit": "' . $limit . '", "num_found": ' . $hits . ', "docs": ' . json_encode(array_slice($json,0,round(sizeof($json)/2)-1)) . '}';
      }else if($direction == 'down'){
          echo '{"start": ' . $last. ', "limit": "' . $limit . '", "num_found": ' . $hits . ', "docs": ' . json_encode(array_slice($json,round(sizeof($json)/2),sizeof($json))) . '}';
      }
      
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
