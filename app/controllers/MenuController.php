<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once ('ReportFilterHelpers.php');
require_once ('models/table/OptionList.php');
//require_once('models/table/Course.php');
require_once ('views/helpers/CheckBoxes.php');
require_once ('models/table/MultiAssignList.php');
require_once ('models/table/TrainingTitleOption.php');
require_once ('models/table/Helper.php');

class MenuController extends ReportFilterHelpers {

	public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array()) {
		parent::__construct ( $request, $response, $invokeArgs );
	}

	public function init() {
	}

	public function indexAction() {
$this->_forward ( 'info' );
	}

	public function preDispatch() {
		$rtn = parent::preDispatch ();
		$allowActions = array ('info' );

		if (! $this->isLoggedIn ())
		$this->doNoAccessError ();

		
		return $rtn;
	}

	public function dataAction() { 	}
        public function rrateAction(){
             $this->_countrySettings = array();
		$this->_countrySettings = System::getAll();

		$this->view->assign ( 'mode', 'search' );
                require_once ('models/table/TrainingLocation.php');
		require_once('views/helpers/TrainingViewHelper.php');
$db = Zend_Db_Table_Abstract::getDefaultAdapter ();
$current_month = "03";
$current_year = date('Y');
$date_format = $current_year.'-'.$current_month.'-'.'01';
echo '<nav id="primary_nav_wrap">';
echo '<ul>';
$zones = $this->get_location_category_unique("zone");
//print_r($zones);exit;
foreach($zones as $zone){
    $zone_name = $zone['geo_zone'];
    $zone_id = $zone['geo_parent_id'];
   
    $facilities = $this->get_all_facilities_with_location("zone",$zone_id);
    $facility_ids = implode(",",$facilities);
    
   $report_rates = $this->get_all_facilities_reporte_rates($facility_ids,$date_format);
    echo '<li><a href=#"><span></span>'.$zone_name.'::'.$report_rates.'</a>';
    $states = $this->get_location_category_unique("state",$zone_id);
    echo '<ul>';
    foreach($states as $state){
        $state_name = $state['state'];
        $state_id = $state['state_id'];
        $facilities = $this->get_all_facilities_with_location("state",$state_id);
    $facility_ids = implode(",",$facilities);
    $report_rates_state = $this->get_all_facilities_reporte_rates($facility_ids,$date_format);
        echo '<li><a href="#"><span></span>'.$state_name.'::'.$report_rates_state.'</a>';
        echo '<ul>';
    
   
    $lgas = $this->get_location_category_unique("lgs",$state_id);
    foreach($lgas as $lga){
        $lga_name = $lga['lga'];
        $lga_id = $lga['lga_id'];
       $facilities = $this->get_all_facilities_with_location("lga",$lga_id);
    $facility_ids = implode(",",$facilities);
    $report_rates_lga = $this->get_all_facilities_reporte_rates($facility_ids,$date_format);
        echo '<li><a href="#"><span></span>'.$lga_name.'::'.$report_rates_lga;echo '</a></li>'; 
        
    }
    echo '</ul>';
      echo '</li>';  
    }
    echo '</ul>';
    echo '</li>';
}

echo '</ul>';
echo '</nav>';
            
        }
public function infoAction(){
    $this->_countrySettings = array();
		$this->_countrySettings = System::getAll();

		$this->view->assign ( 'mode', 'search' );
                require_once ('models/table/TrainingLocation.php');
		require_once('views/helpers/TrainingViewHelper.php');

}

public function get_location_category_unique($category,$condition=""){
      if($category=="zone"){
        $needle = "geo_parent_id,geo_zone";
        $condi = "";
        $name = "geo_zone";
    }else if($category=="state"){
        $needle = "state_id,state";
        $name = "state";
        $condi = "WHERE geo_parent_id='$condition'";
    }else{
        $needle = "lga_id,lga";
        $name = "lga";
        $condi = "WHERE state_id='$condition'";
    }
    
    $db = Zend_Db_Table_Abstract::getDefaultAdapter ();
    $sql = "SELECT DISTINCT  ".$needle." FROM facility_location_view ".$condi."  ORDER BY `$name` ASC";
  // echo $sql;exit;
    $result = $db->fetchAll($sql);
    return $result;
    
}
public function get_all_facilities_reporte_rates($facility_ids,$date_format){
    $db = Zend_Db_Table_Abstract::getDefaultAdapter ();
    $sql = "SELECT COUNT(*) as counter FROM facility_report_rate WHERE facility_id IN (".$facility_ids.") AND date='$date_format'";
    
    $result = $db->fetchAll($sql);
    //print_r($result);exit;
    return $result[0]['counter'];
    
}
public function get_all_facilities_with_location($category,$id){
    $db = Zend_Db_Table_Abstract::getDefaultAdapter ();
    if($category=="zone"){
        $needle = "geo_parent_id";
    }else if($category=="state"){
        $needle = "state_id";
    }else{
        $needle = "lga_id";
    }
    
    $sql  = "SELECT id FROM facility_location_view WHERE `$needle`='$id'";
    $result = $db->fetchAll($sql);
    $facilities = array();
    foreach($result as $facility){
        $facility_id = $facility['id'];
        $facilities[] = $facility_id;
    }
    return $facilities;
}
        
}
?>
