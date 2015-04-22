<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of StockoutController
 *
 * @author Swedge
 */
require_once ('ReportFilterHelpers.php');
require_once ('models/table/Helper2.php');
require_once('models/table/Stockout.php');

class StockoutController extends ReportFilterHelpers {
    //put your code here
    
    public function percentStockoutWithTrainedHWAction() {
	    $stockout = new Stockout();
	    $helper = new Helper2();
            
            $this->view->assign('title',$this->t['Application Name'].space.t('CHAI').space.t('Dashboard'));
	     
            //get the parameters
            list($geoList, $tierValue) = $this->buildParameters();
            
            $fp_stockout = $stockout->fetchPercentStockOutFacsWithTrainedHW('fp', $geoList, $tierValue);
            $larc_stockout = $stockout->fetchPercentStockOutFacsWithTrainedHW('larc', $geoList, $tierValue);
            
            $this->view->assign('larc_data', $larc_stockout);
	    $this->view->assign('fp_data', $fp_stockout);
	    
	    //$this->view->assign('date', date('F Y', strtotime("-1 months"))); //TA:17:18: take last month
	    //GNR:use max commodity date	    
	    $sDate = $helper->fetchTitleDate();
	    $this->view->assign('date', $sDate['month_name'].' '.$sDate['year']); 
	    
	    $this->viewAssignEscaped ('locations', Location::getAll() );
	
	
	} //dashAction15
        
        
        public function percentFacsProvidingButStockedoutAction(){
            $stockout = new Stockout();
	    $helper = new Helper2();
            
            $this->view->assign('title',$this->t['Application Name'].space.t('CHAI').space.t('Dashboard'));
	     
            //get the parameters
            list($geoList, $tierValue) = $this->buildParameters();
            
            $fp_stockout = $stockout->fetchPercentFacsProvidingButStockedOut('fp', $geoList, $tierValue);
            $larc_stockout = $stockout->fetchPercentFacsProvidingButStockedOut('larc', $geoList, $tierValue);
            
            $this->view->assign('larc_data', $larc_stockout);
	    $this->view->assign('fp_data', $fp_stockout);
	    
	    //$this->view->assign('date', date('F Y', strtotime("-1 months"))); //TA:17:18: take last month
	    //GNR:use max commodity date	    
	    $sDate = $helper->fetchTitleDate();
	    $this->view->assign('date', $sDate['month_name'].' '.$sDate['year']); 
	    
	    $this->viewAssignEscaped ('locations', Location::getAll() );
        }
}

?>
