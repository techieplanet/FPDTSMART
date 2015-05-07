<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CoverageController
 *
 * @author Swedge
 */
require_once ('ReportFilterHelpers.php');
require_once ('models/table/Helper2.php');
require_once('models/table/Coverage.php');
class CoverageController extends ReportFilterHelpers {
    //put your code here
    
    //TP: rewrote most part of this method 2/4/2015
	public function cummhwtrainedAction() {
            $coverage = new Coverage();
            $helper = new Helper2();
            
            //get the parameters
            list($geoList, $tierValue) = $this->buildParameters();
                
            //If no GEO selection made 
	    if( !isset($_POST["region_c_id"]) && !isset($_POST["district_id"]) && !isset($_POST["province_id"]) ) { 
                $cumm_data = $coverage->fetchCummulativeTrainedWorkers(3, $geoList, $tierValue);
                $this->view->assign('cumm_data', $cumm_data);
	    }
            else{
                $cumm_data = $coverage->fetchCummulativeTrainedWorkers(3, $geoList, $tierValue);
                $this->view->assign('cumm_data', $cumm_data);
                //var_dump($cumm_data); exit;
                
                $locationNames = $helper->getLocationNames($geoList);
                $larc_cumm_location = $coverage->fetchCummulativeTrainedWorkersByLocation('larc',3, $geoList, $tierValue);
                $fp_cumm_location= $coverage->fetchCummulativeTrainedWorkersByLocation('fp',3, $geoList, $tierValue);
                
                $this->view->assign('fp_cumm_location', $fp_cumm_location);
                $this->view->assign('larc_cumm_location', $larc_cumm_location);
                $this->view->assign('cumm_locations', $helper->getLocationNames($geoList));
            }
                
            //GNR:use max commodity date
            $sDate = $helper->fetchTitleDate();
            $this->view->assign('tp_date', $sDate['month_name'] . ', '.$sDate['year']);

            //locations
            $this->view->assign('cumm_data', $cumm_data);
            $this->viewAssignEscaped ('locations', Location::getAll());
              
    }
    
    
    public function facswithhwprovidingAction() {
	    $coverage = new Coverage();
	    $helper = new Helper2();
            
            $this->view->assign('title',$this->t['Application Name'].space.t('CHAI').space.t('Dashboard'));
	     
            //get the parameters
            list($geoList, $tierValue) = $this->buildParameters();
            
            //set date limit
            $dateWhere = 'c.date = \'' . $helper->getLatestPullDate() . '\'';
            //$dateWhere = $helper->getLatestPullDate();
            
            //get the location names
            $locationNames = $helper->getLocationNames($geoList);
            
            $fp_coverage = $coverage->fetchFacsWithHWProviding('fp', 'fp', $dateWhere, $locationNames, $geoList, $tierValue);
            $larc_coverage = $coverage->fetchFacsWithHWProviding('larc', 'larc', $dateWhere, $locationNames, $geoList, $tierValue);
            
            $this->view->assign('fp_data',$fp_coverage);
            $this->view->assign('larc_data',$larc_coverage);
            	
	    //$this->view->assign('date', date('F Y', strtotime("-1 months"))); //TA:17:18: take last month
	    //GNR:use max commodity date
	    $sDate = $helper->fetchTitleDate();
	    $this->view->assign('date', $sDate['month_name'].' '.$sDate['year']); 
	    
	    $this->viewAssignEscaped ('locations', Location::getAll() );
	      
	} //dashAction13
        
        
        public function percentfacsprovidingAction() {
            $coverage = new Coverage();
            $helper = new Helper2();
	    
	    $this->view->assign('title',$this->t['Application Name'].space.t('CHAI').space.t('Dashboard'));
            
            //get the parameters
            list($geoList, $tierValue) = $this->buildParameters();

            $fp_coverage = $coverage->fetchPercentFacsProviding('fp', $geoList, $tierValue);
            $larc_coverage = $coverage->fetchPercentFacsProviding('larc', $geoList, $tierValue);
            $inj_coverage = $coverage->fetchPercentFacsProviding('injectables', $geoList, $tierValue);
            
    //            var_dump($fp_coverage);
    //            echo '<br/><br/>';
    //            var_dump($larc_coverage); 
    //            echo '<br/><br/>';
    //            var_dump($inj_coverage); exit;            


            $this->view->assign('fp_data',$fp_coverage);
            $this->view->assign('larc_data',$larc_coverage);
            $this->view->assign('inj_data',$inj_coverage);

            //$this->view->assign('date', date('F Y', strtotime("-1 months"))); //TA:17:18: take last month
            //GNR:use max commodity date
            $sDate = $helper->fetchTitleDate();
            $this->view->assign('date', $sDate['month_name'].' '.$sDate['year']); 

            $this->viewAssignEscaped ('locations', Location::getAll() );

       }
   
   
   
        
        public function percentfacswithtrainedhwAction() {
            $coverage = new Coverage();
            $helper = new Helper2();
	    
	    $this->view->assign('title',$this->t['Application Name'].space.t('CHAI').space.t('Dashboard'));
            
            //get the parameters
            list($geoList, $tierValue) = $this->buildParameters();                
	    	
            $fp_coverage = $coverage->fetchPercentFacHWTrained('fp', $geoList, $tierValue);
            $larc_coverage = $coverage->fetchPercentFacHWTrained('larc', $geoList, $tierValue);
            
//            var_dump($fp_coverage);
//            echo '<br/><br/>';
//            var_dump($larc_coverage); exit;
            
            $this->view->assign('fp_data',$fp_coverage);
            $this->view->assign('larc_data',$larc_coverage);
            
            //$this->view->assign('date', date('F Y', strtotime("-1 months"))); //TA:17:18: take last month
	    //GNR:use max commodity date
	    //$tDate = new DashboardCHAI();
	    $sDate = $helper->fetchTitleDate();
	    $this->view->assign('date', $sDate['month_name'].' '.$sDate['year']); 
	    	
	    $this->viewAssignEscaped ('locations', Location::getAll() );

	}
}

?>
