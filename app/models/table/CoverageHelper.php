<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CoverageHelper
 *
 * @author Swedge
 */
require_once 'Helper2.php';

class CoverageHelper {

    //public function getTrainedHWCoverageCount($training_type, $start_year,$year_amount, $locationWhereClause, $locationNames, $groupFieldName, $havingName, $geoList, $tierText){
      public function getTrainedHWByLocationCount($training_type, $start_year, $year_amount, $locationNames, $geoList, $tierText, $tierFieldName){
                $db = Zend_Db_Table_Abstract::getDefaultAdapter ();
                $year = $start_year;
                
                    
                    $locationDataArray = array();
                    for($i = $year_amount; $i > 0; $i--) {
                        $endDateWhere = "YEAR(t.training_end_date) <= '" . $year . "'";
                        $trainingTypeWhere = "tto.system_training_type = '" . $training_type . "'";
                        $longWhereClause = $endDateWhere . ' AND ' . $trainingTypeWhere . ' AND ' . 
                                   $tierFieldName . ' IN (' . $geoList . ')';
//                      
                        
                        $select = $db->select ()
                        ->from ( array ('p' => 'person' ), array ('COUNT(DISTINCT(p.id)) as count'))
                        ->joinInner(array('ptt'=>'person_to_training'), 'ptt.person_id=p.id', array())
                        ->joinInner(array ('t' => "training" ), "t.id = ptt.training_id", array() )
                        ->joinInner(array('tto' => 'training_title_option' ), 'tto.id = t.training_title_option_id', array())
                        ->joinInner(array ('flv' => "facility_location_view" ), 'flv.id = p.facility_id', array('flv.lga', 'flv.state', 'flv.geo_zone') )
                        ->where($longWhereClause)
                        ->group($tierFieldName)
                        ->order(array($tierText));

                        //echo $select->__toString(); exit;

                        $result = $db->fetchAll ( $select );  
                        
                        //filter for only valid values
                          if(!empty($result)){
                            foreach($locationNames as $key=>$locationName){
                                $locationValue = '';
                                foreach($result as $coverageEntry){
                                    //echo 'tier: ' . $tierText . '<br/>';
                                    //var_dump($coverageEntry); exit;
                                    if($locationName == $coverageEntry[$tierText]){                                    
                                        $locationValue = $coverageEntry['count']; 
                                        break;
                                    }
                                }
                                
                                if($locationValue == '')
                                    $locationValue = 0;
                                
                                //$locationDataArray[$locationName][$year][$training_type] = $locationValue;
                                $locationDataArray[$locationName][$year] = $locationValue;
                            }
                        }
                        else{
                            foreach($locationNames as $key=>$locationName)
                                //$locationDataArray[$locationName][$year][$training_type] = 0;
                                $locationDataArray[$locationName][$year] = 0;
                        }
                        
                        $year --;
                    }
                    
                    
                    //accamulate data: add previous years to the current year
//                    $start = $year + 1; //set to lowest considered year after the loop above
//                    
////                    echo '<br/><br/>accummulate<br/>';
//                    foreach($locationNames as $location){
//                        ksort($locationDataArray[$location]);
//                        foreach ($locationDataArray[$location] as $year=>$value){
//                            if($year == $start) continue;
//                            //var_dump($locationDataArray[$location][$year]); exit;
//                            //$locationDataArray[$location][$year][$training_type] += $locationDataArray[$location][$year-1][$training_type];
//                            $locationDataArray[$location][$year] += $locationDataArray[$location][$year-1];
//                        }
//		    }
                    
                //sort the years
                foreach($locationNames as $location)
                    ksort($locationDataArray[$location]);
                
                //var_dump($locationDataArray); exit;    
                return array_reverse($locationDataArray, true);
            }                
            
         
        /* TP:
         * This method gets the number of facs with trained health workwes by location
         * Depending on the content of the $longWhereClause, it may be used to get
         * the count of facs trained for FP/LARC by location or total count of facs by location
         */
        public function getFacWithTrainedHWCountByLocation($longWhereClause, $geoList, $tierText, $tierFieldName){
                $db = Zend_Db_Table_Abstract::getDefaultAdapter ();
                $helper = new Helper2();
                
                //if($nationalMode) $tierFieldName = '';
                $select = $db->select()
                            ->from(array('fwtc' => 'facility_worker_training_counts_view'),
                              array('COUNT(facid) AS fid_count'))
                            ->joinInner(array('flv' => 'facility_location_view'), 'facid = flv.id', array('lga', 'state',  'geo_zone'))
                            ->where($longWhereClause)
                            ->group($tierFieldName)
                            ->order(array($tierText));
                
                //echo $select->__toString(); exit;
                
              $result = $db->fetchAll($select);
               
              //filter for only valid values
              $locationNames = $helper->getLocationNames($geoList);
              $locationDataArray = $this->filterLocations($locationNames, $result, $tierText);
              
            //var_dump($locationDataArray); exit;
            return $locationDataArray;
       }
       
       
       
       public function getFacProvidingCount($longWhereClause, $geoList, $tierText, $tierFieldName){
                $db = Zend_Db_Table_Abstract::getDefaultAdapter ();
                $helper = new Helper2();
                
                $select = $db->select()
                            ->from(array('c' => 'commodity'),
                              array('COUNT(DISTINCT(c.facility_id)) AS fid_count'))
                            ->joinInner(array('cno' => 'commodity_name_option'), 'cno.id = c.name_id', array())
                            ->joinInner(array('flv' => 'facility_location_view'), 'flv.id = c.facility_id', array('lga', 'state',  'geo_zone'))
                            ->where($longWhereClause)
                            ->group($tierFieldName)
                            ->order(array($tierText));   

              //echo 'Providing: ' . $select->__toString() . '<br/>'; exit;

               $result = $db->fetchAll($select);
               
              //filter for only valid values
              $locationNames = $helper->getLocationNames($geoList);
              $locationDataArray = $this->filterLocations($locationNames, $result, $tierText);
               
            //var_dump($locationDataArray); exit;
            return $locationDataArray;
       }
       
       
       
       public function getCoverageCountFacWithHWProviding($longWhereClause, $locationNames, $geoList, $tierText, $tierFieldName){
                $db = Zend_Db_Table_Abstract::getDefaultAdapter();               
                
                $select = $db->select()
                        ->from(array('c' => 'commodity'),
                          array('COUNT(DISTINCT(c.facility_id)) AS fid_count'))
                        ->joinInner(array('cno' => 'commodity_name_option'), 'cno.id = c.name_id', array())
                        ->joinInner(array('flv' => 'facility_location_view'), 'flv.id = c.facility_id', array('lga', 'state',  'geo_zone'))
                        ->joinInner(array('fwtc' => 'facility_worker_training_counts_view'), 'c.facility_id = fwtc.facid', array())
                        ->where($longWhereClause)
                        ->group($tierFieldName)
                        ->order(array($tierText)); 


                //echo $sql = $select->__toString(); exit;
                
               $result = $db->fetchAll($select);
               
              //filter for only valid values
              $locationDataArray = $this->filterLocations($locationNames, $result, $tierText);
               
              //var_dump($locationDataArray); exit;
              return $locationDataArray;
       }
       
       
       public function getTrainedHWByLocationList($tt_where, $dateWhere, $locationWhereClause, $locationKey, $groupFieldName, $havingName, $geoList, $tierText){
           $db = Zend_Db_Table_Abstract::getDefaultAdapter ();                         
           $select = $db->select()
                        ->from(array('fwtc' => 'facility_worker_training_counts_view'), array('facid'))
                        ->joinInner(array('f' => 'facility'), 'fwtc.facid = f.id')
                        ->joinInner(array('frr'=>'facility_report_rate'), 'frr.facility_external_id = f.external_id')
                        ->joinInner(array('flv' => 'facility_location_view'), 'flv.id = fwtc.facid', array('lga', 'state',  'geo_zone'))
                        ->where($locationWhereClause)
                        ->where($tt_where)
                        ->where($dateWhere)
                        ->where($havingName . '=\'' . $locationKey . '\'');

                $sql = $select->__toString();
                $sql = str_replace('AS `count`,', 'AS `count`', $sql);
                          $sql = str_replace('`fwtc`.*,', '', $sql);	        
                          $sql = str_replace('`f`.*,', '', $sql);	        
                          $sql = str_replace('`frr`.*,', '', $sql);	        
              //echo 'THW: ' . $sql . '<br/>'; exit;
              
              $result = $db->fetchAll($sql);
              
              $locationFacs = array();
              foreach ($result as $row)
                $locationFacs[] = $row['facid'];
              
            return $locationFacs;
       }
       
       public function getCoverageCountFacWithHWProviding_new($tt_where, $dateWhere, $locationWhereClause, $locationNames, $groupFieldName, $havingName, $geoList, $tierText){
                $db = Zend_Db_Table_Abstract::getDefaultAdapter ();
                
                $facsList = array();
                foreach ($locationNames as $locationKey=>$location){
                    $facsList[$location] = $this->getTrainedHWByLocationList($tt_where, $dateWhere, $locationWhereClause, $locationKey, $groupFieldName, $havingName, $geoList, $tierText);
                    var_dump($facsList); exit;
                }
                
                $result = $db->fetchAll($sql);
               
              //filter for only valid values
              $locationDataArray = $this->filterLocations($locationNames, $result, $tierText);
               
              var_dump($locationDataArray); exit;
              return $locationDataArray;
       }
       
       
       public function getAllFacsWithTrainedHWCount($where){
           $db = Zend_Db_Table_Abstract::getDefaultAdapter ();
           $select = $db->select()
                        ->from(array('fwtc' => 'facility_worker_training_counts_view'),
                          array('COUNT(DISTINCT(facid)) AS fid_count'))
                        ->where($where);
           
           $result = $db->fetchRow($select);
           return $result['fid_count'];
       }
       
       
       public function getAllFacsProvidingCount($where){
           $db = Zend_Db_Table_Abstract::getDefaultAdapter ();
           $select = $db->select()
                                ->from(array('f' => 'facility'),
                                  array('COUNT(DISTINCT(f.id)) AS fid_count'))
                                ->joinInner(array('frr' => 'facility_report_rate'), 'f.external_id = frr.facility_external_id')
                                ->joinInner(array('c' => 'commodity'), 'f.id = c.facility_id AND consumption > 0')
                                ->joinInner(array('cno' => 'commodity_name_option'), 'cno.id = c.name_id')
                                ->where($where);
                                
                $sql = $select->__toString();
                $sql = str_replace('AS `count`,', 'AS `count`', $sql);
                          $sql = str_replace('`frr`.*,', '', $sql);	        
                          $sql = str_replace('`c`.*,', '', $sql);
                          $sql = str_replace('`cno`.*', '', $sql);
              //echo 'National Providing: ' . $sql . '<br/>'; exit;
           
           $result = $db->fetchRow($select);
           return $result['fid_count'];
      }
      
      
      public function getAllFacsProvidingWithTrainedHWCount($longWhereClause){
           $db = Zend_Db_Table_Abstract::getDefaultAdapter ();
           $select = $db->select()
                        ->from(array('c' => 'commodity'),
                          array('COUNT(DISTINCT(c.facility_id)) AS fid_count'))
                        ->joinInner(array('cno' => 'commodity_name_option'), 'cno.id = c.name_id', array())
                        ->joinInner(array('flv' => 'facility_location_view'), 'flv.id = c.facility_id', array('lga', 'state', 'geo_zone'))
                        ->joinInner(array('fwtc' => 'facility_worker_training_counts_view'), 'c.facility_id = fwtc.facid', array())
                        ->where($longWhereClause);
                      
            //echo 'National Providing With trained: ' . $select->__toString() . '<br/>'; exit;
           
           $result = $db->fetchRow($select);
           return $result['fid_count'];
      }
       

      function getReportingFacsWithTrainedHWOvertime($longWhereClause){
          $db = Zend_Db_Table_Abstract::getDefaultAdapter ();
          
          $select = $db->select()
                       ->from(array('fwtc'=> 'facility_worker_training_counts_view'), 
                               array('COUNT(DISTINCT(facid)) as fid_count'))
                       ->joinInner(array('frr'=>'facility_report_rate'), 'facid = frr.facility_id', array('MONTHNAME(date) as month_name', 'YEAR(date) as year'))
                       ->joinInner(array('flv'=>'facility_location_view'), 'facid = flv.id', array())
                       ->where($longWhereClause)
                       ->group('date')
                       ->order(array('date'));
          
          //echo $select->__toString(); exit;
          
          $result = $db->fetchAll($select);
          return $result;
          
      }
      
      
      public function getFacWithHWProvidingOverTime($longWhereClause){
                $db = Zend_Db_Table_Abstract::getDefaultAdapter();               
                
                $select = $db->select()
                        ->from(array('c' => 'commodity'),
                          array('COUNT(DISTINCT(c.facility_id)) AS fid_count'))
                        ->joinInner(array('cno' => 'commodity_name_option'), 'cno.id = c.name_id', array('MONTHNAME(date) as month_name', 'YEAR(date) as year'))
                        ->joinInner(array('fwtc' => 'facility_worker_training_counts_view'), 'c.facility_id = fwtc.facid', array())
                        ->joinInner(array('flv'=>'facility_location_view'), 'c.facility_id = flv.id', array())
                        ->where($longWhereClause)
                        ->group('date')
                        ->order(array('date')); 


                //echo $sql = $select->__toString(); exit;

               $result = $db->fetchAll($select);
               
              //var_dump($result); exit;
              return $result;
       }
       
       
       public function getFacProvidingOverTime($longWhereClause, $geoList, $tierText, $tierFieldName){
                $db = Zend_Db_Table_Abstract::getDefaultAdapter ();
                $helper = new Helper2();
                
                $select = $db->select()
                            ->from(array('c' => 'commodity'),
                              array('COUNT(DISTINCT(c.facility_id)) AS fid_count', 'MONTHNAME(date) as month_name', 'YEAR(date) as year'))
                            ->joinInner(array('cno' => 'commodity_name_option'), 'cno.id = c.name_id', array())
                            ->joinRight(array('flv' => 'facility_location_view'), 'flv.id = c.facility_id', array('lga', 'state',  'geo_zone'))
                            ->where($longWhereClause)
                            ->group(array($tierFieldName, 'date'))
                            ->order(array($tierText, 'date'));   
                
              //echo 'Providing: ' . $select->__toString() . '<br/>'; exit;
                
              $result = $db->fetchAll($select);
              
              //$locationNames = $helper->getLocationNames($geoList);
              //$locationDataArray = $this->filterLocations($locationNames, $result, $tierText);
              
            //var_dump($locationDataArray); exit;
            return $result;
       }
       
       /* TP: 
        * This method will return number of facilities that are 
        * reporting in the months covered in the date range and locations provided arg
        * IT DOES NOT MATTER IF THE FACILITIES DO NOT HAVE TRAINED HW
        * NO LOCATION FILTERING.
        */
        public function getReportingFacsOvertimeByLocationNoFilter($longWhereClause, $geoList, $tierText, $tierFieldName){
                $db = Zend_Db_Table_Abstract::getDefaultAdapter ();

                $select = $db->select()
                              ->from(array('frr' => 'facility_report_rate'),
                                  array('COUNT(DISTINCT(facility_id)) AS fid_count', 'MONTHNAME(date) as month_name', 'YEAR(date) as year'))
                              ->joinInner(array('flv' => 'facility_location_view'), 'flv.id = facility_id', array('lga', 'state', 'geo_zone'))
                              ->where($longWhereClause)
                              ->group(array($tierFieldName, 'date'))
                              ->order(array($tierText, 'date'));   

              //echo $sql = $select->__toString(); exit;

              $result = $db->fetchAll($select);
              return $result;
        }
        
       
       public function filterLocations($locationNames, $result, $tierText){
           $locationDataArray = array();
           if(!empty($result)){
                  //echo 'not empty: ' . $tierText; exit;
                    //var_dump($locationNames);exit;
                foreach($locationNames as $key=>$locationName){
                    $locationValue = '';
                    foreach($result as $coverageEntry){
                        //echo 'tier: ' . $tierText . '<br/>';
                        //var_dump($coverageEntry); exit;
                        if($locationName == $coverageEntry[$tierText]){                                    
                            $locationValue = $coverageEntry['fid_count']; 
                            break;
                        }
                    }

                    if($locationValue == '')
                        $locationValue = 0;

                    $locationDataArray[$locationName] = $locationValue;
                }
            }
            else{
                //echo 'empty: ' . $tierText; exit;
                foreach($locationNames as $key=>$locationName)
                    $locationDataArray[$locationName] = 0;
            }
            
            return $locationDataArray;
       }
      
}
?>