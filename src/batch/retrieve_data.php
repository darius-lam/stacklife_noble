<?php

/****************************
 * This script hits the Awesome API to retrieve the n recently awesomed items.
 * We grab the hollis ID of each awesome item and use it to get details of the item from the 
 * LibraryCloud API. We package that up and serialize it ot a JSON object that is used display
 * a StackView stack on the homepage.
 
 
 To DO:
 Get shelfrank?
 ****************************/


    /**$sl_home = dirname(dirname(dirname(__FILE__)));
    require_once ($sl_home . '/etc/sl_ini.php');**/

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 2,
        /**CURLOPT_URL => "http://librarylab.law.harvard.edu/awesome/api/item/recently-awesome?limit=200"
        We may want to randomize a search query here, since Noblenet does not support "recently awesomed" data**/
        CURLOPT_URL => "http://catalog.noblenet.org/opac/extras/opensearch/1.1/NOBLE/mods/keyword/?searchTerms=artificial+intelligence&count=20"
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    
    $xml = simplexml_load_string($response, "SimpleXMLElement", LIBXML_NOCDATA);
    $json = json_encode($xml);
    $noble_response = json_decode($json);

    // For each Awesome item, let's get details from the LC API
    $static_docs = array();
    
    foreach ($noble_response->mods as $item) {
                
        /**$curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => 'http://librarycloud.harvard.edu/v1/api/item/?filter=id_inst:' . $doc->hollis_id
        ));
        $response = curl_exec($curl);
        curl_close($curl);

        $lc_response = json_decode($response);**/
        
                
        // Do we need to set default like this, or does StackView do that for us?
        $static_doc = array('title' => 'Uknown Title', 'creator' => array(), 'measurement_page_numeric' => 0, 'measurement_height_numeric' => 0, 'pub_date' => 0);

        // The labels in LC and the Awesome API don't always match. Let's align those here.        
        if (property_exists($item->titleInfo, 'title') && !empty($item->titleInfo->title)) {
            $static_doc['title'] = $item->titleInfo->title;
        }

        if (property_exists($item->name, 'namePart') && !empty($item->name->namePart)) {
            $static_doc['creator'] = $item->name->namePart;
        }
        
        //really ugly down here.  
        if (property_exists($item->physicalDescription, 'extent') && !empty($item->physicalDescription->extent)) {
            $physical_attributes = explode(' ',preg_replace('/[^0-9]/',' ',$item->physicalDescription->extent));
            $height = NULL;
            $pages = NULL;
            
            foreach($physical_attributes as $val){
                intval($val);
                if($val != 0){
                    $pages = $val;
                    $static_doc['measurement_page_numeric'] = $pages;
                    break;
                }
            }
            
            foreach(array_reverse($physical_attributes) as $val){
                $val = intval($val);
                if($val!= 0){
                    $height = $val;
                    
                    $static_doc['measurement_height_numeric'] = $height;
                    break;
                }
            }
        }

        if (property_exists($item, 'shelfrank') && !empty($item->shelfrank)) {
            $static_doc['shelfrank'] = $item->shelfrank;
        }
        
        //set shelfrank to 0 for now
        $static_doc['shelfrank'] = 0;
             
        if (property_exists($item->originInfo, 'dateIssued') && !empty($item->originInfo->dateIssued[1]) && intval($item->originInfo->dateIssued[1]) != 0) {
            $static_doc['pub_date'] = intval($item->originInfo->dateIssued[1]);
        }

        if (property_exists($item, 'typeOfResource') && !empty($item->typeOfResource)) {
            $static_doc['format'] = $item->typeOfResource;
        }
        
        $title_nf = $item->titleInfo->title;
        $title_link_friendly = strtolower($title_nf);
        //Make alphanumeric (removes all other characters)
        $title_link_friendly = preg_replace("/[^a-z0-9_\s-]/", "",$title_link_friendly);
        //Clean up multiple dashes or whitespaces
        $title_link_friendly = preg_replace("/[\s-]+/", " ", $title_link_friendly);
        //Convert whitespaces and underscore to dash
        $title_link_friendly = preg_replace("/[\s_]/", "-", $title_link_friendly);
        
        $static_doc['link'] = "/item/" . $title_link_friendly . '/' . $item->recordInfo->recordIdentifier;

        $static_docs[] = $static_doc;
    }
    
    $complete_object = array();
    $complete_object['start'] = -1;
    $complete_object['limit'] = 0;
    $complete_object['num_found'] = count($static_docs);
    $complete_object['docs'] = $static_docs;
    $serialized_object = json_encode($complete_object);

    // Let's make sure we have at least 10 items and then we'll write them out to a static JSON file
    if ($complete_object['num_found'] > 10) {
        $file_path = './owhl-web-app/data.json';
        // Write the contents back to the file
        file_put_contents($file_path, $serialized_object);
    }
?>