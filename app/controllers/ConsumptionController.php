<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ConsumptionController
 *
 * @author Swedge
 */
require_once ('ReportFilterHelpers.php');
require_once ('models/table/Helper2.php');
require_once('models/table/Consumption.php');
class ConsumptionController extends ReportFilterHelpers {
    //put your code here
    
    public function showitAction(){
        //var_dump($_POST); exit;
        $helper = new Helper2();
        $this->view->assign('text', 'this is a method');
        $this->view->assign('comms', $helper->getCommodities());
        $this->viewAssignEscaped ('locations', Location::getAll() );
    }
    
    
    
    public function consumptionAction(){
        $helper = new Helper2();
        $cons = new Consumption();
        //$this->view->assign('title',$this->t['Application Name'].space.t('CHAI').space.t('Dashboard'));

        if( !isset($_POST["comm_id"]) || $_POST["comm_id"] == 0)
            $commodityID = 0;
        else
            $commodityID = $_POST['comm_id'];
        echo $commodityID; exit;
        
        //get the parameters
        list($geoList, $tierValue) = $this->buildParameters();

        
        $all = false;
        $commodityID = 0;
        
        if($all){
            
        }
        else if($commodityID > 0){
//            if( !isset($_POST["region_c_id"]) && !isset($_POST["district_id"]) && !isset($_POST["province_id"]) ) { 
//                $consByCommodity = $cons->fetchConsumptiomPerCommodity($commodityID, $geoList, $tierValue);
//                $this->view->assign('consumption_by_method', $consByCommodity);
//            }
        }
        else{ //when no option selected
          if( !isset($_POST["region_c_id"]) && !isset($_POST["district_id"]) && !isset($_POST["province_id"]) ) { 
                $consByCommodity = $cons->fetchConsumptiomPerCommodity($commodityID, $geoList, $tierValue);
                $consOverTime = $cons->fetchConsumptionByCommodityOverTime();
                
                $this->view->assign('consumption_by_method',$consByCommodity);
                $this->view->assign('consumption_overtime',$consOverTime);
            }  
        }
        
        //else
            {
            //echo 'cons cons'; exit;
            //$consByGeo
            //$consByCommodity = $cons->fetchConsumptiomPerCommodity($commodityID, $geoList, $tierValue);
            //$consByGeo = $cons->fetchConsumptiomByGeography($commodityID, $geoList, $tierValue);
            //$methodName = $consByGeo['method'];
            //$consByGeoData = $consByGeo['locationdata'];
            
            //var_dump($consByCommodity);
            //var_dump($consByGeoData); exit;

            
            //$this->view->assign('consumption_by_method',$consByCommodity);
            //$this->view->assign('consumption_by_geo',$consByGeoData);
            //$this->view->assign('method',$methodName);
        }

        //$this->view->assign('date', date('F Y', strtotime("-1 months"))); //TA:17:18: take last month
        //GNR:use max commodity date
        $sDate = $helper->fetchTitleDate();
        $this->view->assign('date', $sDate['month_name'].' '.$sDate['year']); 
        
        $overTimeDates = $helper->getPreviousMonthDates(12);
        $this->view->assign('start_date', date('F', strtotime($overTimeDates[0])). ' '. date('Y', strtotime($overTimeDates[0]))); 
        $this->view->assign('end_date', date('F', strtotime($overTimeDates[11])). ' '. date('Y', strtotime($overTimeDates[11]))); 
        
        //this will provide the commodities list for the drop down
        $this->view->assign('comms', $helper->getCommodities());
        
        $this->viewAssignEscaped ('locations', Location::getAll() );

    }
    
    
    public function consbygeographyAction() {
        
        $helper = new Helper2();

        $this->view->assign('title',$this->t['Application Name'].space.t('CHAI').space.t('Dashboard'));

        if( !isset($_POST["comm_id"]) || $_POST["comm_id"] == 0)
            $commodityID = 0;
        else
            $commodityID = $_POST['comm_id'];
        //echo $commodityID; exit;
        
        //get the parameters
        list($geoList, $tierValue) = $this->buildParameters();

        //get the location names
        //$locationNames = $helper->getLocationNames($geoList);

        $cons = new Consumption();
        if( !isset($_POST["region_c_id"]) && !isset($_POST["district_id"]) && !isset($_POST["province_id"]) ) { 
            $consByCommodity = $cons->fetchConsumptiomPerCommodity($commodityID, $geoList, $tierValue);
            $this->view->assign('consumption_by_method',$consByCommodity);
        }
        else{
            //echo 'cons cons'; exit;
            //$consByGeo
            $consByCommodity = $cons->fetchConsumptiomPerCommodity($commodityID, $geoList, $tierValue);
            //$consByGeo = $cons->fetchConsumptiomByGeography($commodityID, $geoList, $tierValue);
            //$methodName = $consByGeo['method'];
            //$consByGeoData = $consByGeo['locationdata'];
            
            //var_dump($consByCommodity);
            //var_dump($consByGeoData); exit;

            
            $this->view->assign('consumption_by_method',$consByCommodity);
            //$this->view->assign('consumption_by_geo',$consByGeoData);
            //$this->view->assign('method',$methodName);
        }
        
        

        //$this->view->assign('date', date('F Y', strtotime("-1 months"))); //TA:17:18: take last month
        //GNR:use max commodity date
        $sDate = $helper->fetchTitleDate();
        $this->view->assign('date', $sDate['month_name'].' '.$sDate['year']); 
            
        //this will provide the commodities list for the drop down
        $this->view->assign('comms', $helper->getCommodities());
        
        $this->viewAssignEscaped ('locations', Location::getAll() );

  } // dash996Action
}

?>
