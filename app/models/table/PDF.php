<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PDF
 *
 * @author Swedge
 */

require_once('Helper2.php');
class PDF {
    //put your code here
    
    public function insertLocationIds(){
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $helper = new Helper2();
        $lastPullDate = $helper->getLatestPullDate();
        
        try{
            $select = $db->select()->from(array('p'=>'pdf_reports'), array('id'))->where("date='$lastPullDate'");
            //echo $select->__toString();exit;
            $result = $db->fetchAll($select);
            if(!empty($result)) return;
            
            $select = $db->select()->from(array('loc'=>'location'), array('id'));
           // echo $select->__toString();exit;
                    
            $result = $db->fetchAll($select);
            //var_dump($result); exit;
            
            //insert the national first. location id 0 used for national
            $bind = array('location_id'=>0, 'date' => $lastPullDate);
            $db->insert('pdf_reports', $bind);
            
            foreach ($result as $location){
                $bind = array('location_id'=>$location['id'], 'date' => $lastPullDate);
                $db->insert('pdf_reports', $bind);
            }

            return 1;
        } catch(Exception $e){
            print $e->getMessage(); exit;
        }
    }
    
    public function getReportsWithLocationDate($locationsImplode,$startDate,$endDate){
         $db = Zend_Db_Table_Abstract::getDefaultAdapter();
         
         try{
             $whereClause = "pr.location_id IN (".$locationsImplode.") AND (date>='$startDate' AND date<='$endDate') AND file_generated='1'";
             $select = $db->select()
                        ->from(array('pr'=>'pdf_reports'), array('id', 'location_id','filename','date_generated','date','folder_name'))
                        ->joinLeft(array('l'=>'location'), 'pr.location_id=l.id OR pr.location_id = 0', array('tier','location_name','id as loc_id'))
                        ->where($whereClause)
                        ->group('pr.id');
             
                        
          
            $result = $db->fetchAll($select);
            return $result;
                          }
         catch(Exception $ex){
             print $ex->getMessage(); exit;
         }
    }
    public function update_database($date1,$date2){
         $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $sql = "UPDATE `pdf_reports` SET `file_generated`='0' WHERE `date`>='$date1' AND `date`<='$date2'";
       
        $result = $db->query($sql);
        if($result){
          echo 'It is working now';  
        }else{
            echo 'the guy no gree work jare';
        }
        
    }
    public function fetchUniqueReportData($fileId){
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $whereClause = "pr.id='$fileId'";
         try{
            $select = $db->select()
                        ->from(array('pr'=>'pdf_reports'), array('id', 'location_id','folder_name','filename'))
                        ->joinInner(array('l'=>'location'), 'pr.location_id=l.id OR pr.location_id = 0', array('tier','location_name','id as loc_id'))
                        ->where($whereClause)
                        ->limit(1);
          // echo $select->__toString();echo '<br/>';
            $result = $db->fetchRow($select);
           
           return $result;
        } catch (Exception $ex) {
            print $ex->getMessage(); exit;
        }
    }
    public function fetchFileToBeAttached($stateIds,$lastPulledDate){
        
        $FileFolderNames  = $this->fetchFileNamesFolderNames($stateIds,$lastPulledDate);
        $filePath = array();
        foreach($FileFolderNames as $fileData){
            $fileName = $fileData['filename'];
            $folderName = $fileData['folder_name'];
            $path = "pdfrepo/$folderName$fileName";
            $filePath[] = $path;
        }
        return $filePath;
    }
    public function fetchFileNamesFolderNames($stateIds,$lastPulledDate){
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        
        try{
            
            $whereClause = "pr.location_id IN (".$stateIds.") AND date='$lastPulledDate' AND pr.file_generated=1";
            $select = $db->select()
                         ->from(array('pr'=>'pdf_reports'), array('folder_name','filename'))
                         ->where($whereClause);
            //echo $select->__toString();exit;
            $result = $db->fetchAll($select);
            //print_r($result);exit;
            return $result;
        }catch (Exception $ex){
            print $ex->getMessage(); exit;
        }
    }
    public function reformatArchivedSearchData($archivedReports){
      
       $sizeof = sizeof($archivedReports);
       for($i=0;$i<$sizeof;$i++){
           if($archivedReports[$i]['location_id']=="0"){
               $archivedReports[$i]['location_name'] = "National Report";
           }
       }
       return $archivedReports;
    }
     public function getNextLocationDetailsWithTiers($tierValue="",$location_id=""){
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $helper = new Helper2();
        
        $lastPullDate = $helper->getLatestPullDate();
        $whereClause = "date = '$lastPullDate' ";
           if(!empty($location_id) || $location_id=="0"){
            $whereClause .= " AND pr.location_id='$location_id' ";
        }
        if(!empty($tierValue)){
            $whereClause .= " AND tier='$tierValue' AND pr.location_id!='0' ";
        }
        
        
       
            
        
        try{
            $select = $db->select()
                        ->from(array('pr'=>'pdf_reports'), array('id', 'location_id'))
                        ->joinInner(array('l'=>'location'), 'pr.location_id=l.id OR pr.location_id = 0', array('tier','location_name','id as loc_id'))
                        ->where($whereClause)
                        ->limit(1);
           //echo $select->__toString();echo '<br/>';
            $result = $db->fetchRow($select);
           
            return array('report_id'=>$result['id'], 
                         'location_id' => $result['loc_id'],
                         'location_name'=>$result['location_name'],
                         'tier' => $result['tier']
                    );
        } catch (Exception $ex) {
            print $ex->getMessage(); exit;
        }
    }
    
    public function getNextLocationDetails(){
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $helper = new Helper2();
       
        $lastPullDate = $helper->getLatestPullDate();
          
        
        try{
            $select = $db->select()
                        ->from(array('pr'=>'pdf_reports'), array('id', 'location_id'))
                        ->joinInner(array('l'=>'location'), 'pr.location_id=l.id OR pr.location_id = 0', array('tier','location_name','id as loc_id'))
                        ->where("date = '$lastPullDate' AND file_generated = 0")
                      
                        ->limit(1);
        
            $result = $db->fetchRow($select);
           
            return array('report_id'=>$result['id'], 
                         'location_id' => $result['loc_id'],
                         'location_name'=>$result['location_name'],
                         'tier' => $result['tier']
                    );
        } catch (Exception $ex) {
            print $ex->getMessage(); exit;
        }
    }
    
     public function fetchPercentFacHWTrainedPerLocation($training_type,$tierValue,$geoList){
                $db = Zend_Db_Table_Abstract::getDefaultAdapter ();
                $output = array(array('location'=>'National', 'percent'=>0));
                $helper = new Helper2();
                
               
                $freshVisit = false;
                $locations = $geoList;
            $latestDate = $helper->getLatestPullDate();
            if(!isset($tierValue) || $tierValue==""){
            $tierValue = 2;
            }
           
            if(!isset($geoList) || $geoList==""){
                
            $geoList = implode(",",$helper->getLocationTierIDs($tierValue));
           //$geoList = implode(',',$geoList);
            }else{
                
                $geoList = $locations;
            }
                $cacheManager = new CacheManager();
            
            
                if($training_type == 'fp')
                    $cacheValue = $cacheManager->getIndicator(CacheManager::PERCENT_FACS_TRAINED_FP, $latestDate);
                else if($training_type == 'larc')
                    $cacheValue = $cacheManager->getIndicator(CacheManager::PERCENT_FACS_TRAINED_LARC, $latestDate);
                
                //needed variables
                $tierText = $helper->getLocationTierText($tierValue);
                $tierFieldName = $helper->getTierFieldName($tierText);
                $latestDate = $helper->getLatestPullDate();

                //where clauses
                if($training_type == 'fp')
                    $tt_where = "fptrained > 0";
                else if($training_type == 'larc')
                    $tt_where = 'larctrained > 0';

                $locationWhere = $tierFieldName . ' IN (' . $geoList . ')';
                $longWhereClause = $tt_where . ' AND ' . $locationWhere;

                $coverageHelper = new CoverageHelper();                
                $facility = new Facility();

                //$numerators = $coverageHelper->getFacWithTrainedHWCountByLocation($longWhereClause, $geoList, $tierText, $tierFieldName);
                //$denominators = $coverageHelper->getFacWithTrainedHWCountByLocation($locationWhere, $geoList, $tierText, $tierFieldName);
                $denominators = $facility->getFacilityCountByLocation($locationWhere, $geoList, $tierText, $tierFieldName);
                //var_dump($numerators); exit;
                    
                $sumsArray = $helper->sumNumersAndDenoms($numerators, $denominators);
                
                //$arrayToSort = array_slice($sumsArray['output'], 1);
                $sortedArray = $helper->msort($sumsArray['output']);

                //get month national data and put in first array element
                $cacheValue = json_decode($cacheValue, true);
                if($cacheValue)
                    $output[0]['percent'] = $cacheValue[0]['percent'];
                
                //$location_id = $this->fetchlocaid($locname);
              //$sortedArray[$i]['location_id'] = $location_id;
                
                $sortedArray = $helper->addlocationnames($sortedArray);
                $output = array_merge($output, $sortedArray);
                
                //var_dump($output); exit;
                return $output;
        }
       
        public function fetchPercentFacHWTrainedPerState($training_type,$tierValue,$geoList){
                $db = Zend_Db_Table_Abstract::getDefaultAdapter ();
                $output = array(array('location'=>'National', 'percent'=>0));
                $helper = new Helper2();
                
               
                $freshVisit = false;
                $locations = $geoList;
            $latestDate = $helper->getLatestPullDate();
            
           
         
                $cacheManager = new CacheManager();
            
            
                if($training_type == 'fp')
                    $cacheValue = $cacheManager->getIndicator(CacheManager::PERCENT_FACS_TRAINED_FP, $latestDate);
                else if($training_type == 'larc')
                    $cacheValue = $cacheManager->getIndicator(CacheManager::PERCENT_FACS_TRAINED_LARC, $latestDate);
                
                //needed variables
                $tierText = $helper->getLocationTierText($tierValue);
                $tierFieldName = $helper->getTierFieldName($tierText);
                $latestDate = $helper->getLatestPullDate();

                //where clauses
                if($training_type == 'fp')
                    $tt_where = "fptrained > 0";
                else if($training_type == 'larc')
                    $tt_where = 'larctrained > 0';

                $locationWhere = $tierFieldName . ' IN (' . $geoList . ')';
                $longWhereClause = $tt_where . ' AND ' . $locationWhere;

                $coverageHelper = new CoverageHelper();                
                $facility = new Facility();

                $numerators = $coverageHelper->getFacWithTrainedHWCountByLocation($longWhereClause, $geoList, $tierText, $tierFieldName);
                //$denominators = $coverageHelper->getFacWithTrainedHWCountByLocation($locationWhere, $geoList, $tierText, $tierFieldName);
                $denominators = $facility->getFacilityCountByLocation($locationWhere, $geoList, $tierText, $tierFieldName);
                //var_dump($numerators); exit;
                    
                $sumsArray = $helper->sumNumersAndDenoms($numerators, $denominators);
                
                //$arrayToSort = array_slice($sumsArray['output'], 1);
                $sortedArray = $helper->msort($sumsArray['output']);

                //get month national data and put in first array element
                $cacheValue = json_decode($cacheValue, true);
                if($cacheValue)
                    $output[0]['percent'] = $cacheValue[0]['percent'];
                
                //$location_id = $this->fetchlocaid($locname);
              //$sortedArray[$i]['location_id'] = $location_id;
                
                $sortedArray = $helper->addlocationnames($sortedArray);
                $output = array_merge($output, $sortedArray);
                
                //var_dump($output); exit;
                return $output;
        }
      
         public function fetchPercentFacsProvidingPerState($commodity_type,$tierValue,$geoList){
            $db = Zend_Db_Table_Abstract::getDefaultAdapter ();

            $output = array(array('location'=>'National', 'percent'=>0)); 
            $helper = new Helper2();
            
            $locations = $geoList;
            $latestDate = $helper->getLatestPullDate();
          
           
//            if(!isset($geoList) || $geoList==""){
//                $geoList = array();
//                $geoList = $helper->getLocationTierIDs($tierValue);
//                $geoList = implode(',',$geoList);
//            }else{
//                
//                $geoList = $locations;
//            }
            
            //echo var_dump($geoList); exit;
            
            // print_r($locations);exit;
              //print_r($geoList);exit;
            $freshVisit = false;
            
            $cacheManager = new CacheManager();
            
            if($commodity_type == 'fp')
                $cacheValue = $cacheManager->getIndicator(CacheManager::PERCENT_FACS_PROVIDING_FP, $latestDate);
            else if($commodity_type == 'larc')
                $cacheValue = $cacheManager->getIndicator(CacheManager::PERCENT_FACS_PROVIDING_LARC, $latestDate);            
           
            $tierText = $helper->getLocationTierText($tierValue);
            $tierFieldName = $helper->getTierFieldName($tierText);
         
            
            
            //where clauses
            if($commodity_type == 'fp')
                $ct_where = "(commodity_type = 'fp' OR commodity_type = 'larc')";
            else if($commodity_type == 'larc')
                $ct_where = "commodity_type = 'larc'";

            $dateWhere = "c.date = '$latestDate'";
            $reportingWhere = 'facility_reporting_status = 1';
            $consumptionWhere = 'consumption > 0';
            $locationWhere = $tierFieldName . ' IN (' . $geoList . ')';

            $coverageHelper = new CoverageHelper();
            $longWhereClause = $reportingWhere . ' AND ' . $dateWhere . ' AND ' . 
                               $consumptionWhere . ' AND ' . $ct_where . ' AND ' . $locationWhere;
            
            $numerators = $coverageHelper->getFacProvidingCount($longWhereClause, $geoList, $tierText, $tierFieldName);
            //print_r($denominators);echo '<br/><br/><br/>';
            $dateWhere = "frr.date = '$latestDate'";
            $longWhereClause = $dateWhere . ' AND ' . $locationWhere;

            //send only one month date range. 
            $denominators = $helper->getReportingFacsOvertimeByLocation($longWhereClause, $geoList, $tierText, $tierFieldName);
            //print_r($denominators);echo '<br/><br/><br/>';
            //set output                    
            $sumsArray = $helper->sumNumersAndDenoms($numerators, $denominators);
           
          //  $arrayToSort = array_slice($sumsArray['output'], 1);
            $sortedArray = $helper->msort($sumsArray['output']);

            //get month national data and put in first array element
            $cacheValue = json_decode($cacheValue, true);
            if($cacheValue)
                $output[0]['percent'] = $cacheValue[0]['percent'];
            
            $sortedArray = $helper->addlocationnames($sortedArray);
            $output = array_merge($output, $sortedArray);

            //set national ave
            //var_dump($output); 
            return $output;

        }

    
     public function fetchPercentFacsProvidingPerLocation($commodity_type,$tierValue,$geoList){
            $db = Zend_Db_Table_Abstract::getDefaultAdapter ();

            $output = array(array('location'=>'National', 'percent'=>0)); 
            $helper = new Helper2();
            
            $locations = $geoList;
            $latestDate = $helper->getLatestPullDate();
            if(!isset($tierValue) || $tierValue==""){
                $tierValue = 2;
            }
           
//            if(!isset($geoList) || $geoList==""){
//                $geoList = array();
//                $geoList = $helper->getLocationTierIDs($tierValue);
//                $geoList = implode(',',$geoList);
//            }else{
//                
//                $geoList = $locations;
//            }
            
            //echo var_dump($geoList); exit;
            
            // print_r($locations);exit;
              //print_r($geoList);exit;
            $freshVisit = false;
            
            $cacheManager = new CacheManager();
            
            if($commodity_type == 'fp')
                $cacheValue = $cacheManager->getIndicator(CacheManager::PERCENT_FACS_PROVIDING_FP, $latestDate);
            else if($commodity_type == 'larc')
                $cacheValue = $cacheManager->getIndicator(CacheManager::PERCENT_FACS_PROVIDING_LARC, $latestDate);            
           
            $tierText = $helper->getLocationTierText($tierValue);
            $tierFieldName = $helper->getTierFieldName($tierText);
         
            
            
            //where clauses
            if($commodity_type == 'fp')
                $ct_where = "(commodity_type = 'fp' OR commodity_type = 'larc')";
            else if($commodity_type == 'larc')
                $ct_where = "commodity_type = 'larc'";

            $dateWhere = "c.date = '$latestDate'";
            $reportingWhere = 'facility_reporting_status = 1';
            $consumptionWhere = 'consumption > 0';
            $locationWhere = $tierFieldName . ' IN (' . $geoList . ')';

            $coverageHelper = new CoverageHelper();
            $longWhereClause = $reportingWhere . ' AND ' . $dateWhere . ' AND ' . 
                               $consumptionWhere . ' AND ' . $ct_where . ' AND ' . $locationWhere;
            
            $numerators = $coverageHelper->getFacProvidingCount($longWhereClause, $geoList, $tierText, $tierFieldName);
            //print_r($denominators);echo '<br/><br/><br/>';
            $dateWhere = "frr.date = '$latestDate'";
            $longWhereClause = $dateWhere . ' AND ' . $locationWhere;

            //send only one month date range. 
            $denominators = $helper->getReportingFacsOvertimeByLocation($longWhereClause, $geoList, $tierText, $tierFieldName);
            //print_r($denominators);echo '<br/><br/><br/>';
            //set output                    
            $sumsArray = $helper->sumNumersAndDenoms($numerators, $denominators);
           //print_r($sumsArray);exit;
          //  $arrayToSort = array_slice($sumsArray['output'], 1);
            $sortedArray = $helper->msort($sumsArray['output']);
//echo 'This is the value that entered the cacheValue '.$output[0]['percent'] ;exit;
            //get month national data and put in first array element
            $cacheValue = json_decode($cacheValue, true);
            if($cacheValue)
                $output[0]['percent'] = $cacheValue[0]['percent'];
            
            $sortedArray = $helper->addlocationnames($sortedArray);
            $output = array_merge($output, $sortedArray);

            //set national ave
            //var_dump($output); 
            return $output;

        }
        
public function fetchFacilitiesWithHWNotProviding($commodity_type, $training_type, $geoList, $tierValue, $freshVisit){
    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                
                $output = array(array('location'=>'National', 'percent'=>0));
                $helper = new Helper2();
                $latestDate = $helper->getLatestPullDate();
                
                $cacheManager = new CacheManager();
            
                if($training_type == 'fp')
                    $cacheValue = $cacheManager->getIndicator(CacheManager::PERCENT_FACS_HW_PROVIDING_FP, $latestDate);
                else if($training_type == 'larc')
                    $cacheValue = $cacheManager->getIndicator(CacheManager::PERCENT_FACS_HW_PROVIDING_LARC, $latestDate);


             
                    $tierText = $helper->getLocationTierText($tierValue);
                    $tierFieldName = $helper->getTierFieldName($tierText);
                    $locationNames = $helper->getLocationNames($geoList);
                    $consumptionWhere = 'consumption > 0';
                    $reportingWhere = 'facility_reporting_status = 1';

                    $dateWhere = "c.date = '$latestDate'";

                    //commodity type where
                    if($commodity_type == 'fp')
                        $ct_where = "(commodity_type = 'fp' OR commodity_type = 'larc')";
                    else if($commodity_type == 'larc')
                        $ct_where = "commodity_type = 'larc'";

                    //training type where
                    if($training_type == 'fp')
                        $tt_where = "fptrained > 0";
                    else if($commodity_type == 'larc')
                        $tt_where = "larctrained > 0";

                    $locationWhere = $tierFieldName . ' IN (' . $geoList . ')';

                    $coverageHelper = new CoverageHelper();
                      $facility = new Facility();
                    //concatenate conditions for numerators
                    $longWhereClause = $consumptionWhere . ' AND ' . $reportingWhere . ' AND ' . 
                                       $ct_where . ' AND ' . $tt_where . ' AND ' . $locationWhere . ' AND ' .
                                       $dateWhere;
                    $facilities = $coverageHelper->getCoverageDataFacWithHWNotProviding($longWhereClause, $locationNames, $geoList, $tierText, $tierFieldName);
                    $facilitiesByLocation = $facility->getFacilityByLocation($locationWhere);
                    
                    $facilitiesByLocationNames = array();
                    $facilities_names = array();
                    foreach($facilitiesByLocation as $facility_Location){
                        $facilitiesByLocationNames[] = $facility_Location['facility_name'];
                    }
                   
                    foreach($facilities as $fac){
    $facilities_names[] = $fac['facility_name'];
                    }
              
                $result = array_diff($facilitiesByLocationNames, $facilities_names);      


          return $result;      
}

 public function fetchfacsWithHWNotProviding($commodity_type, $training_type, $geoList, $tierValue, $freshVisit){
                $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                
                $output = array(array('location'=>'National', 'percent'=>0));
                $helper = new Helper2();
                $facility = new Facility();
                $latestDate = $helper->getLatestPullDate();
                
                $cacheManager = new CacheManager();
            
                if($training_type == 'fp')
                    $cacheValue = $cacheManager->getIndicator(CacheManager::PERCENT_FACS_HW_PROVIDING_FP, $latestDate);
                else if($training_type == 'larc')
                    $cacheValue = $cacheManager->getIndicator(CacheManager::PERCENT_FACS_HW_PROVIDING_LARC, $latestDate);


               
                    $tierText = $helper->getLocationTierText($tierValue);
                    $tierFieldName = $helper->getTierFieldName($tierText);
                    $locationNames = $helper->getLocationNames($geoList);
                    $consumptionWhere = 'consumption > 0';
                    $reportingWhere = 'facility_reporting_status = 1';

                    $dateWhere = "c.date = '$latestDate'";

                    //commodity type where
                    if($commodity_type == 'fp')
                        $ct_where = "(commodity_type = 'fp' OR commodity_type = 'larc')";
                    else if($commodity_type == 'larc')
                        $ct_where = "commodity_type = 'larc'";

                    //training type where
                    if($training_type == 'fp')
                        $tt_where = "fptrained > 0";
                    else if($commodity_type == 'larc')
                        $tt_where = "larctrained > 0";

                    $locationWhere = $tierFieldName . ' IN (' . $geoList . ')';

                    $coverageHelper = new CoverageHelper();

                    //concatenate conditions for numerators
                    $longWhereClause = $consumptionWhere . ' AND ' . $reportingWhere . ' AND ' . 
                                       $ct_where . ' AND ' . $tt_where . ' AND ' . $locationWhere . ' AND ' .
                                       $dateWhere;
                    $numerators = $coverageHelper->getCoverageCountFacWithHWProviding($longWhereClause, $locationNames, $geoList, $tierText, $tierFieldName);

                    //concatenate conditions for denominators
                    $dateWhere = "frr.date = '$latestDate'";
                    $longWhereClause = $tt_where . ' AND ' . $dateWhere . ' AND ' . $locationWhere;

                    //send only one month date range. 
                    $denominators = $helper->getReportingFacsWithTrainedHWOvertimeByLocation($longWhereClause, $geoList, $tierText, $tierFieldName);
                   $counter = $facility->getAllFacilityCountByLocation($locationWhere);
                    
                    foreach($numerators as $num){
                        $numerator[] = $num; 
                    } 
                    
                    $sizeof = sizeof($numerator);
                    for($r=0;$r<$sizeof;$r++){
                        $diff = $counter-$numerator[$r];
                        $percentage[] = $diff/$counter;
                        
                    }
               
                return $percentage;
     }
     
    
     public function fetchFacsWithHWProviding($commodity_type, $training_type, $geoList, $tierValue, $freshVisit){
                $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                
                $output = array(array('location'=>'National', 'percent'=>0));
                $helper = new Helper2();
                $latestDate = $helper->getLatestPullDate();
                
                $cacheManager = new CacheManager();
            
                if($training_type == 'fp')
                    $cacheValue = $cacheManager->getIndicator(CacheManager::PERCENT_FACS_HW_PROVIDING_FP, $latestDate);
                else if($training_type == 'larc')
                    $cacheValue = $cacheManager->getIndicator(CacheManager::PERCENT_FACS_HW_PROVIDING_LARC, $latestDate);


                //check if page is just being loaded
                //fresh session, month data already registered
                //just retrieve registered data
                if($cacheValue && $freshVisit){ 
                    $output = json_decode($cacheValue, true);
                }
                else{
                    $tierText = $helper->getLocationTierText($tierValue);
                    $tierFieldName = $helper->getTierFieldName($tierText);
                    $locationNames = $helper->getLocationNames($geoList);
                    $consumptionWhere = 'consumption > 0';
                    $reportingWhere = 'facility_reporting_status = 1';

                    $dateWhere = "c.date = '$latestDate'";

                    //commodity type where
                    if($commodity_type == 'fp')
                        $ct_where = "(commodity_type = 'fp' OR commodity_type = 'larc')";
                    else if($commodity_type == 'larc')
                        $ct_where = "commodity_type = 'larc'";

                    //training type where
                    if($training_type == 'fp')
                        $tt_where = "fptrained > 0";
                    else if($commodity_type == 'larc')
                        $tt_where = "larctrained > 0";

                    $locationWhere = $tierFieldName . ' IN (' . $geoList . ')';

                    $coverageHelper = new CoverageHelper();

                    //concatenate conditions for numerators
                    $longWhereClause = $consumptionWhere . ' AND ' . $reportingWhere . ' AND ' . 
                                       $ct_where . ' AND ' . $tt_where . ' AND ' . $locationWhere . ' AND ' .
                                       $dateWhere;
                    $numerators = $coverageHelper->getCoverageCountFacWithHWProviding($longWhereClause, $locationNames, $geoList, $tierText, $tierFieldName);
                    
                    //concatenate conditions for denominators
                    $dateWhere = "frr.date = '$latestDate'";
                    $longWhereClause = $tt_where . ' AND ' . $dateWhere . ' AND ' . $locationWhere;

                    //send only one month date range. 
                    $denominators = $helper->getReportingFacsWithTrainedHWOvertimeByLocation($longWhereClause, $geoList, $tierText, $tierFieldName);

                    //set output                    
                    $sumsArray = $helper->sumNumersAndDenoms($numerators, $denominators);
                     
                    $sortedArray = $helper->msort($sumsArray['output']);
                    $output = array_merge($output, $sortedArray);
                   
                    $output[0]['percent'] = $sumsArray['nationalAvg'];

                    //check if to save month national data
                    if(!$cacheValue && $freshVisit){ //fresh in month
                        //do cache insert
                        if($training_type == 'fp')
                            $alias = CacheManager::PERCENT_FACS_HW_PROVIDING_FP;
                        else if($training_type == 'larc')
                            $alias = CacheManager::PERCENT_FACS_HW_PROVIDING_LARC;

                        $dataArray = array(
                            'date_cached'=> $latestDate,
                            'indicator' => 'Percent of Facilities with a trained HW providing FP/LARC',
                            'indicator_alias' => $alias,
                            'value' => json_encode($output)
                        );
                        $cacheManager->setIndicator($dataArray);
                    }
                    else{ //inner if
                        //get month national data and put in first array element
                        $cacheValue = json_decode($cacheValue, true);
                        if($cacheValue)
                            $output[0]['percent'] = $cacheValue[0]['percent'];
                    }
                }
                    
                //echo '<br/><br/>';
                //var_dump($output); exit;
                return $output;
     }
     

    public function updatereportid($report_id,$filename,$date_generated,$folderName){
         $db = Zend_Db_Table_Abstract::getDefaultAdapter();
         try{
             $bind = array(
                 "file_generated"=>1,
                 "date_generated"=>$date_generated,
                 "folder_name"=>$folderName,
                 "filename"=>$filename
                 
             );
             $update = $db->update("pdf_reports", $bind, 'id= '.$report_id);
             return $update;
             
         }catch (Exception $ex){
             print $ex->getMessage();exit;
         }
         
    }
    
    public function fetchLocationWithLocation($location){
            $db = Zend_Db_Table_Abstract::getDefaultAdapter ();
              try{
            $select = $db->select()
                         ->from(array('loc'=>'location'), array('id'))
                         ->where("parent_id =$location");
            $result = $db->fetchAll($select);
            $results = array();
          foreach($result as $res){
              $results[] = $res['id'];
          }
          
           return $results;
              }  catch (Exception $ex){
                  print $ex->getMessage(); exit;
              }
        }
    
       
}
