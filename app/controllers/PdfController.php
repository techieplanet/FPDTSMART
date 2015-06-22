<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PDFController
 *
 * @author Swedge
 */
require_once ('ReportFilterHelpers.php');
require_once 'dompdf/dompdf_config.inc.php';
require_once('models/table/PDF.php');
require_once ('models/table/Helper2.php');
require_once('models/table/Coverage.php');
require_once('models/table/Stockout.php');
require_once('models/table/Consumption.php');
require_once('models/table/Dashboard.php');

class PdfController extends ReportFilterHelpers {
    //put your code here
    public function preDispatch() {
            parent::preDispatch ();

            if (!$this->isLoggedIn ())
                    $this->doNoAccessError ();
               
            //if (! $this->setting('module_employee_enabled')){
                    //$_SESSION['status'] = t('The employee module is not enabled on this site.');
                    //$this->_redirect('select/select');
            //}

            //if (! $this->hasACL ( 'employees_module' )) {
                    //$this->doNoAccessError ();
            //}
    }

    
    
    public function showreportsAction(){     
        //echo $_SERVER['HTTP_HOST'] . "/pdftemplate.php"; exit;
        $pdf = new PDF();
        //echo 'trying';
        //$this->_helper->layout()->disableLayout();
        //$this->_helper->viewRenderer->setNoRender(true);
        //exit;
            
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isPost()) {

           }
        }
        else {
               //echo 'before'; exit;
               //first ensure that all the locations have been registered
                $pdf->insertLocationIds();

               //get the next unflagged (with 0 for the file_generated value) location
                $reportLocation = $pdf->getNextLocationDetails();

                //call the handler for national or state or lga
                $this->createNationalCharts();
                
                
           }
    }
    

    public function  createNationalCharts(){
        
        $larc_target = 5500;
        $fp_target = 5500;
        
        list($geoList, $tierValue) = $this->buildParameters();
        
        //trained workers
        $coverage = new Coverage();
        $cumm_data = $coverage->fetchCummulativeTrainedWorkers(1, $geoList, $tierValue);
        $key = key($cumm_data); 
        $this->view->assign('cumm_data', $cumm_data);
        $this->view->assign('fp_diff', ($fp_target - $cumm_data[$key]['fp']));
        $this->view->assign('larc_diff', ($larc_target - $cumm_data[$key]['larc']));
        
        
        //method mix
        $dashboard = new Dashboard();
        $consumptionbyMethod = $dashboard->fetchConsumptionByMethod();
        $this->view->assign('consumption_by_method', $consumptionbyMethod);
        
        
        //consumption over time - implants and injectables
        $cons = new Consumption();
        $consOverTime = $cons->fetchConsumptionByCommodityOverTime();
        $this->view->assign('consumption_overtime',$consOverTime);
        
        
        //percentfacstrainedperstate
        $fp_percent_per_state = $coverage->fetchPercentFacHWTrainedPerState('fp');
        $percentData = array();
        $percentData[] = $fp_percent_per_state[0];
        $percentData = array_merge($percentData, array_reverse(array_slice($fp_percent_per_state,1,5), true));
        $percentData = array_merge($percentData, array_slice($fp_percent_per_state,count($fp_percent_per_state)-1,1));
        $this->view->assign('fp_percent_per_state',$percentData);
        
        $larc_percent_per_state = $coverage->fetchPercentFacHWTrainedPerState('larc');
        $percentData = array();
        $percentData[] = $larc_percent_per_state[0];
        $percentData = array_merge($percentData, array_reverse(array_slice($larc_percent_per_state,1,5),true));
        $percentData = array_merge($percentData, array_slice($larc_percent_per_state,count($larc_percent_per_state)-1,1));
        $this->view->assign('larc_percent_per_state',$percentData);
        
        
        //fetchPercentFacsProvidingPerState
        $fp_providing_per_state = $coverage->fetchPercentFacsProvidingPerState('fp');
        $percentData = array();
        $percentData[] = $fp_providing_per_state[0];
        $percentData = array_merge($percentData, array_reverse(array_slice($fp_providing_per_state,1,5), true));
        $percentData = array_merge($percentData, array_slice($fp_providing_per_state,count($fp_providing_per_state)-1,1));
        $this->view->assign('fp_providing_per_state',$percentData);
        
        $larc_providing_per_state = $coverage->fetchPercentFacsProvidingPerState('larc');
        $percentData = array();
        $percentData[] = $larc_providing_per_state[0];
        $percentData = array_merge($percentData, array_reverse(array_slice($larc_providing_per_state,1,5), true));
        $percentData = array_merge($percentData, array_slice($larc_providing_per_state,count($larc_providing_per_state)-1,1));
        $this->view->assign('larc_providing_per_state',$percentData);
        
        
        //facs with fp/larc trained and stocked out of fp(so7days)/larc commodities
        $stockout = new Stockout();
        
        $larc_stockout_per_state = $stockout->fetchPercentStockOutFacsWithTrainedHWPerStates('larc');
        $percentData = array();
        $percentData[] = $larc_stockout_per_state[0];
        $percentData = array_merge($percentData, array_reverse(array_slice($larc_stockout_per_state,1,5), true));
        $percentData = array_merge($percentData, array_slice($larc_stockout_per_state,count($larc_stockout_per_state)-1,1));
        $this->view->assign('larc_stockout_per_state',$percentData);
        
        $fp_stockout_per_state = $stockout->fetchPercentStockOutFacsWithTrainedHWPerStates('fp');
        $percentData = array();
        $percentData[] = $fp_stockout_per_state[0];
        $percentData = array_merge($percentData, array_reverse(array_slice($fp_stockout_per_state,1,5), true));
        $percentData = array_merge($percentData, array_slice($fp_stockout_per_state,count($fp_stockout_per_state)-1,1));
        $this->view->assign('fp_stockout_per_state',$percentData);
        
    }
    

    public function testAction (){
                $html = '';
                echo 'inside test';
                try{
                    
//                    require_once 'Zend/Loader/Autoloader.php';
//                    //require_once('Zend/dompdf/dompdf_config.inc.php');
//                    $load = Zend_Loader_Autoloader::getInstance();
//                    $load->pushAutoloader('DOMPDF_autoload','');
                    
                    
                    $html = 'This is from inside zend';
                
                    $dompdf = new DOMPDF();
                    $dompdf->load_html(trim($html));
                    $dompdf->render();
                    //$dompdf->stream("sample" . date('His') . ".pdf");
                    $pdf = $dompdf->output();
                    file_put_contents("sample" . date('His') . ".pdf", $pdf);
                    
                } catch(Exception $e){
                    $e->getMessage();
                    print '<br><br>';
                    $e->getTrace();
                }
                
                $this->view->assign('html', $html);
    }
    
    
    public function createpdfAction(){
       
       if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isPost()) {
                //echo json_encode(array('result'=>'OK')); exit;
                $helper = new Helper2();
                $lastPullDate = $helper->getLatestPullDate();
                $month = date('F', strtotime($lastPullDate));
                $year = (int)date('Y', strtotime($lastPullDate));
                
                $overTimeDates = $helper->getPreviousMonthDates(12);
                $start_month = date('F', strtotime($overTimeDates[11])). ' '. date('Y', strtotime($overTimeDates[11])); 
                $end_month = date('F', strtotime($overTimeDates[0])). ' '. date('Y', strtotime($overTimeDates[0])); 
                
               try{
                    //$html = file_get_contents("http://localhost/trainsmart/html/pdftemplate.php");
                   $html = file_get_contents("http://" . $_SERVER['HTTP_HOST'] . "/pdftemplate.php");
                    
                    $html = sprintf($html,
                                    $month, //1
                                    $year,  //2
                                    $start_month,   //3
                                    $end_month,     //4
                                    $_POST['tw_hidden'], //5
                                    $_POST['mm_hidden'],  //6
                                    $_POST['mc_hidden'],  //7
                                    $_POST['fptps_hidden'],  //8
                                    $_POST['larctps_hidden'],  //9
                                    $_POST['fpprov_hidden'],  //10
                                    $_POST['larcprov_hidden'],  //11
                                    $_POST['larcso_hidden'],  //12
                                    $_POST['fpso_hidden']  //13
                            );
                    
                    file_put_contents('pdfrepo/template_modified.txt', $html);

                   $dompdf = new DOMPDF();
                   $dompdf->load_html(trim($html));
                   $dompdf->render();
                   //$dompdf->stream("sample" . date('His') . ".pdf");

                   $pdf = $dompdf->output();
                   
                   
                   file_put_contents("pdfrepo/National_Report_$month" . "_$year.pdf", $pdf);
                   echo json_encode(array('result'=>'OK','message'=>''));
                } catch(Exception $e){
                    echo json_encode(array('result'=>'OK','message'=>$e->getMessage()));
                    exit;
                }
            }
       }
    }
}