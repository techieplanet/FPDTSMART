<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Dashboard
 *
 * @author Swedge
 */
require_once 'Helper2.php';
class Dashboard {
    //put your code here
    
    public function fetchConsumptionByMethod(){
        $db = Zend_Db_Table_Abstract::getDefaultAdapter ();
        $output = array ();
        $helper = new Helper2();
        
        $latestDate = $helper->getLatestPullDate();
        
        $where = "(commodity_type='fp' OR commodity_type = 'larc') AND date = '$latestDate'";
        $select = $db->select()
                     ->from(array('c'=>'commodity'), array('SUM(consumption) as consumption'))
                     ->joinRight(array('cno'=>'commodity_name_option'), 'cno.id = c.name_id', 
                                                array('commodity_name as method'))
                     ->where($where)
                     ->group('commodity_name')
                     ->order(array('display_order'));
        $result = $db->fetchAll($select);
        
        return $result;               
    }
    
    
    /*TP: Rewriting the fetchCSDetails method
       * This time we will modularize and make use of views that will already filter the date
       * This method get the last DHIS2 download date and use it as argument for 
       * the 3 categories of calls: 
       */
      public function fetchCoverageSummary(){          
           //$output = $this->fetchCSDetails1(null);
           //var_dump($output); 
           
           $db = Zend_Db_Table_Abstract::getDefaultAdapter();
	   $output = array(); $helper = new Helper2(); $params = array();
            
           $latestPullDate = $helper->getLatestPullDate();
	    $params['last_date'] = $latestPullDate;
            
            //$result = $db->fetchAll($select);
            $params['total_facility_count_month'] = $helper->getAllReportingFacsCount($latestPullDate);
            
            $params['larc_facility_count'] = $helper->getReportingFacilityWithTrainedHW($latestPullDate, 'larc');
            $params['fp_facility_count'] = $helper->getReportingFacilityWithTrainedHW($latestPullDate, 'fp');
            
            $params['larc_consumption_facility_count'] = $helper->getReportingConsumptionFacilities($latestPullDate, 'larc');
            $params['fp_consumption_facility_count'] = $helper->getReportingConsumptionFacilities($latestPullDate, 'fp');
            
            $params['larc_stock_out_facility_count'] = $helper->getReportingStockedOutFacilitiesWithTrainedHWCount($latestPullDate, 'larc');
            $params['fp_stock_out_facility_count'] = $helper->getReportingStockedOutFacilitiesWithTrainedHWCount($latestPullDate, 'fp');
            
            //var_dump($params); exit;
            
            //do your calculations
            $output = $helper->coverageCalculations($params);
            
            //var_dump($output); exit;
            
            return $output;
            
      }
    
}


?>
