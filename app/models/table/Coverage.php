<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Coverage
 *
 * @author Swedge
 */
require_once('Facility.php');
require_once('Helper2.php');
require_once('CoverageHelper.php');

class Coverage {
    //put your code here
    
    
    /*
     * TA:17:17: 01/15/2015
     * get trained persons details
     DB query to take number of HW trained in �LARC� in 2014

     select count(distinct person_to_training.person_id) from person_to_training
     left join training on training.id = person_to_training.training_id
     where training.training_title_option_id=1 and training.training_end_date like '2014%';
     */
    public function fetchCummulativeTrainedWorkers($year_amount, $geoList, $tierValue ) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter ();
        $output = array ();
        $helper = new Helper2();
        $trainingTypesArray = array('fp', 'larc');
        
        //get the last DHIS2 pull date from commodity table and use the year for year here
        //$latestDate = $helper->getPreviousMonthDates(1);
        $latestDate = $helper->getLatestPullDate();
        $year = date('Y', strtotime($latestDate));
        
        $tierText = $helper->getLocationTierText($tierValue);
        $tierFieldName = $helper->getTierFieldName($tierText);
                

        for($i = $year_amount; $i > 0; $i--) {
            $data = array ();
            $endDateWhere = "t.training_end_date like '" . $year . "%'";
            
            foreach ($trainingTypesArray as $training_type){
                $trainingTypeWhere = "tto.system_training_type = '" . $training_type . "'";
                $longWhereClause = $endDateWhere . ' AND ' . $trainingTypeWhere . ' AND ' . 
                                   $tierFieldName . ' IN (' . $geoList . ')';
                
                $select = $db->select ()
                        ->from ( array ('p' => 'person' ), array ('COUNT(DISTINCT(p.id)) as count'))
                        ->joinInner(array('ptt'=>'person_to_training'), 'ptt.person_id=p.id')
                        ->joinInner(array ('t' => "training" ), "t.id = ptt.training_id" )
                        ->joinInner(array('tto' => 'training_title_option' ), 'tto.id = t.training_title_option_id')
                        ->joinInner(array ('flv' => "facility_location_view" ), 'flv.id = p.facility_id', array('flv.lga', 'flv.state', 'flv.geo_zone') )
                        ->where($longWhereClause)
                        //->group($tierFieldName)
                        ->order(array($tierText));
                
                $sql = $select->__toString();
                $sql = str_replace('`ptt`.*,', '', $sql); 
                $sql = str_replace('`t`.*,', '', $sql);
                $sql = str_replace('`tto`.*,', '', $sql);
                //echo $sql; exit;

                $result = $db->fetchAll ( $sql );
                $data = $result [0] ['count'];

                $output[$year][$training_type] = $data;
            }//end inner loop
            
            $year--;
        }//outer loop

        ksort($output); 

        //accamulate data: add previous years to the current year
        $startYear = $year + 1; //set to lowest considered year after the loop above

        foreach($output as $year => $value){
            if($year == $startYear) continue;
            $output[$year]['larc'] = $output[$year]['larc'] + $output[$year-1]['larc'];
            $output[$year]['fp'] = $output[$year]['fp'] + $output[$year-1]['fp'];
        }

        //var_dump($output); exit;
        return $output;
}


     /* TP:
         * This method gets the count of coverage of trained workers in various 
         * geo-locations and tiers. Both FP and LARC
         */
        public function fetchCummulativeTrainedWorkersByLocation($training_type, $year_amount, $geoList, $tierValue){
            $db = Zend_Db_Table_Abstract::getDefaultAdapter ();
            $output = array (); 
            $helper = new Helper2();

            $tierText = $helper->getLocationTierText($tierValue);
            $tierFieldName = $helper->getTierFieldName($tierText);
            $locationNames = $helper->getLocationNames($geoList);
            //var_dump($locationNames); exit;

            //get the last DHIS2 pull date from commodity table and use the year for year here
            $latestDate = $helper->getLatestPullDate();
            $year = date('Y', strtotime($latestDate));

            $coverageHelper = new CoverageHelper();
            //$larcCoverage = $coverageHelper->getTrainedHWCoverageCount('larc', $year,$year_amount, $locationWhereClause, $locationNames, $groupFieldName, $havingName, $geoList, $tierText);
            //$larcCoverage = $coverageHelper->getTrainedHWByLocationCount('larc', $year, $year_amount, $locationNames, $geoList, $tierText, $tierFieldName);
            //$fpCoverage = $coverageHelper->getTrainedHWByLocationCount('fp', $year, $year_amount, $locationNames, $geoList, $tierText, $tierFieldName);
            
            $coverageByLocation = $coverageHelper->getTrainedHWByLocationCount($training_type, $year, $year_amount, $locationNames, $geoList, $tierText, $tierFieldName);
            return $coverageByLocation;
//            return array(
//                    //'location_names' => $locationNames,
//                    'fp' => $fpCoverage,
//                    'larc' => $larcCoverage,
//                );
        }

        
        public function fetchPercentFacHWTrained($training_type, $geoList, $tierValue){
                $db = Zend_Db_Table_Abstract::getDefaultAdapter ();
                
                $output = array(array('location'=>'National', 'percent'=>0)); 
                $helper = new Helper2();
                
                //needed variables
                $tierText = $helper->getLocationTierText($tierValue);
                $tierFieldName = $helper->getTierFieldName($tierText);
                $latestDate = $helper->getLatestPullDate();
                
                //where clauses
                if($training_type == 'fp')
                    $tt_where = "fptrained > 0";
                else if($training_type == 'larc')
                    $tt_where = 'larctrained > 0';
                
                //$dateWhere = 'c.date = (SELECT MAX(frr.date) FROM facility_report_rate';
                //$reportingWhere = 'facility_reporting_status = 1';
                $locationWhere = $tierFieldName . ' IN (' . $geoList . ')';
                $longWhereClause = $tt_where . ' AND ' . $locationWhere;
                
                $coverageHelper = new CoverageHelper();                
                $numerators = $coverageHelper->getFacWithTrainedHWCountByLocation($longWhereClause, $geoList, $tierText, $tierFieldName);
                $denominators = $coverageHelper->getFacWithTrainedHWCountByLocation($locationWhere, $geoList, $tierText, $tierFieldName);
             
                $nationalNumerator = 0; $nationalDenominator = 0; 
                foreach ($numerators as $location=>$numer){
                    $nationalNumerator += $numer;
                    $nationalDenominator += $denominators[$location];
                    
                    $output[] = array(
                                'location' => $location,
                                'percent' => $numer / $denominators[$location]
                    );
                }
                
                $output[0]['percent'] = $nationalNumerator / $nationalDenominator;
                
                //var_dump($output); exit;
                return $output;
        }

    
        
     /*
     * Percentage facilities providing FP, LARC and Injectables in the current month
     */
      public function fetchPercentFacsProviding($commodity_type, $geoList, $tierValue){
            $db = Zend_Db_Table_Abstract::getDefaultAdapter ();

            $output = array(array('location'=>'National', 'percent'=>0)); 
            $helper = new Helper2();
            $tierText = $helper->getLocationTierText($tierValue);
            $tierFieldName = $helper->getTierFieldName($tierText);
            $latestDate = $helper->getLatestPullDate();

            //where clauses
            if($commodity_type == 'fp')
                $ct_where = "commodity_type = 'fp'";
            else if($commodity_type == 'larc')
                $ct_where = "commodity_type = 'larc'";
            else if ($commodity_type == 'injectables')
                $ct_where = "commodity_alias = 'injectables'";

            $dateWhere = "c.date = '$latestDate'";
            $reportingWhere = 'facility_reporting_status = 1';
            $consumptionWhere = 'consumption > 0';
            $locationWhere = $tierFieldName . ' IN (' . $geoList . ')';
            
            
            $coverageHelper = new CoverageHelper();
            $longWhereClause = $reportingWhere . ' AND ' . $dateWhere . ' AND ' . 
                               $consumptionWhere . ' AND ' . $ct_where . ' AND ' . $locationWhere;
            $numerators = $coverageHelper->getFacProvidingCount($longWhereClause, $geoList, $tierText, $tierFieldName);
            
            $longWhereClause = $reportingWhere . ' AND ' . $dateWhere . ' AND ' . $locationWhere;
            $denominators = $coverageHelper->getFacProvidingCount($longWhereClause, $geoList, $tierText, $tierFieldName);

            //set output
            $nationalNumerator = 0; $nationalDenominator = 0; 
            foreach ($numerators as $location=>$numer){
                $nationalNumerator += $numer;
                $nationalDenominator += $denominators[$location];

                $output[] = array(
                            'location' => $location,
                            'percent' => $numer / $denominators[$location]
                );
            }

            $output[0]['percent'] = $nationalNumerator / $nationalDenominator;

            //set national ave
            //var_dump($output); exit;
            return $output;

        }


    //public function fetchPercentFacHWTrainedProvidingDetails($commodity_type, $training_type, &$locationNames, $where, $groupFieldName, $havingName, $geoList, $tierValue){
      public function fetchFacsWithHWProviding($commodity_type, $training_type, $dateWhere, &$locationNames,$geoList, $tierValue){
                $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                
                $output = array (); 
                $helper = new Helper2();
                $tierText = $helper->getLocationTierText($tierValue);
                $tierFieldName = $helper->getTierFieldName($tierText);
                $consmptionWhere = 'consumption > 0';
                $reportingWhere = 'facility_reporting_status = 1';
                
                //commodity type where
                if($commodity_type == 'fp')
                    $ct_where = "commodity_type = 'fp'";
                else if($commodity_type == 'larc')
                    $ct_where = "commodity_type = 'larc'";                    

                //training type where
                if($training_type == 'fp')
                    $tt_where = "fptrained > 0";
                else if($commodity_type == 'larc')
                    $tt_where = "larctrained > 0";

                //$facility = new Facility();
                //$totalReportingFacsWithTrainedHW = $facility->getCurrentReportingFacsWithTrainedHWCount($tt_where, $dateWhere, $geoList, $tierText, $tierFieldName, true);
                
                //echo 'total: ' . $totalReportingFacsWithHWInTrainingCount; exit;
                
                $coverageHelper = new CoverageHelper();
                
                $longWhereClause = $consmptionWhere . ' AND ' . $reportingWhere . ' AND ' . 
                                   $ct_where . ' AND ' . $tt_where . ' AND ' . $dateWhere ;
                $nationalNumerator = $coverageHelper->getAllFacsProvidingWithTrainedHWCount($longWhereClause);
                //var_dump($nationalNumerator); echo '<br/><br/>'; exit;
                $longWhereClause = $reportingWhere . ' AND ' . 
                                   $ct_where . ' AND ' . $tt_where . ' AND ' . $dateWhere ;
                $nationalDenominator = $coverageHelper->getAllFacsProvidingWithTrainedHWCount($longWhereClause);
                //var_dump($nationalDenominator); echo '<br/><br/>';
                
                //concatenate conditions for numerators
                $longWhereClause = $consmptionWhere . ' AND ' . $reportingWhere . ' AND ' . 
                                   $ct_where . ' AND ' . $tt_where . ' AND ' . $dateWhere ;
                $numers = $coverageHelper->getCoverageCountFacWithHWProviding($longWhereClause, $locationNames, $geoList, $tierText, $tierFieldName);
                //var_dump($numers); echo '<br/><br/>';
                //var_dump($numers); 
                
                //concatenate conditions for denominators
                $longWhereClause = $reportingWhere . ' AND ' . $ct_where . ' AND ' . $tt_where . ' AND ' . $dateWhere;
                $denoms = $coverageHelper->getCoverageCountFacWithHWProviding($longWhereClause, $locationNames, $geoList, $tierText, $tierFieldName);
                //var_dump($denoms); echo '<br/><br/>';
                //echo '<br/><br/>';
                //var_dump($denoms);
                
                //set the national value first
                  $output[] = array(
                        'location' => 'National',
                        'percent' => $nationalDenominator > 0 ? $nationalNumerator / $nationalDenominator : 0
                    );

                //$percentSum = 0;
                foreach ($numers as $location=>$numer){
                    $output[] = array(
                                'location' => $location,
                                'percent' => $denoms[$location] > 0 ? $numer / $denoms[$location] : 0  //avoid division by 0
                    );
                }
                //echo '<br/><br/>';
                //var_dump($output); exit;
                return $output;
     }
}

?>
