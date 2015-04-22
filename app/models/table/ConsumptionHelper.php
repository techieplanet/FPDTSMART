<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ConsumptionHelper
 *
 * @author Swedge
 */
require_once 'Helper2.php';

class ConsumptionHelper {
    //put your code here
    
    public function getCommConsumptionByCommodity($commID=0, $longWhere, $geoList){
        $db = Zend_Db_Table_Abstract::getDefaultAdapter ();
        $helper = new Helper2(); $output = array();
        
        $select = $db->select()
                     ->from(array('c'=>'commodity'), array('SUM(consumption) AS consumption'))
                     ->joinInner(array('cno' => 'commodity_name_option'), 'c.name_id = cno.id', array('commodity_name as method'))
                     ->joinInner(array('flv'=>'facility_location_view'), 'flv.id = c.facility_id', array())
                     ->where($longWhere)
                     ->group('c.name_id')
                     ->order(array('display_order'));       
         
        //echo $select->__toString(); exit;
        
        $result = $db->fetchAll($select);
        
        if(!empty($result)){
            foreach($result as $row)
                $row['consumption'] = empty($row['consumption']) ? $row['consumption'] : 0;
        }
        else{
            $commodityName = $helper->getCommodityName($commID);
            $result = array('method' => $commodityName, 'consumption'=>0);
        }
        
        //var_dump($result); exit;
        return $result;
   }
   
   //public function getCommConsumptionByLocation($longWhere, $locationNames, $geoList, $tierText, $tierFieldName){
    public function getCommConsumptionByGeography($commID=0, $longWhereClause, $locationNames, $geoList, $tierText, $tierFieldName){
        $db = Zend_Db_Table_Abstract::getDefaultAdapter ();
        $helper = new Helper2(); $output = array();
        
        $select = $db->select()
                     ->from(array('c'=>'commodity'), array('SUM(consumption) AS consumption'))
                     ->joinInner(array('cno' => 'commodity_name_option'), 'c.name_id = cno.id', array('commodity_name as method'))
                     ->joinInner(array('flv'=>'facility_location_view'), 'flv.id = c.facility_id', array('lga', 'state', 'geo_zone'))
                     ->where($longWhereClause)
                     ->group($tierFieldName)
                     ->order(array($tierText));       
         
        //echo $select->__toString(); exit;
         
        $result = $db->fetchAll($select);
        
        if(!empty($result))
            $methodName = $result[0]['method'];
        else{
            $methodName = $helper->getCommodityName($commID);
        }
        //var_dump($result); exit;
        
        $locationdata = $this->filterLocations($locationNames, $result, $tierText);
        //var_dump($locationdata); exit;
        return $output = array('method'=>$methodName, 'locationdata'=>$locationdata);
   }
   
   
   
   public function filterLocations($locationNames, $result, $tierText){
           $locationDataArray = array();
           if(!empty($result)){
                  //echo 'not empty: ' . $tierText; exit;
                    //var_dump($locationNames);exit;
                foreach($locationNames as $key=>$locationName){
                    $locationValue = '';
                    foreach($result as $entry){
                        //echo 'tier: ' . $tierText . '<br/>';
                        //var_dump($coverageEntry); exit;
                        if($locationName == $entry[$tierText]){                                    
                            $locationValue = $entry['consumption']; 
                            break;
                        }
                    }

                    if(empty($locationValue))
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
   
       public function getConsumptionByCommodityOverTime($longWhere, $commNames){
           $db = Zend_Db_Table_Abstract::getDefaultAdapter ();
            $helper = new Helper2(); $output = array();

            $select = $db->select()
                         ->from(array('c'=>'commodity'), 
                                 array('SUM(consumption) AS consumption', 'MONTHNAME(date) as month_name', 'YEAR(date) as year'))
                         ->joinInner(array('cno' => 'commodity_name_option'), 'c.name_id = cno.id', array('commodity_name as method'))
                         ->where($longWhere)
                         ->group(array('commodity_name', 'date'))
                         ->order(array('display_order', 'date DESC'));       

            //echo $select->__toString(); exit;

            $result = $db->fetchAll($select);
            //var_dump($result); exit;
            //get the month names
            $monthNames = array();  $i =0;
            while($i<12){
                $monthNames[] = $result[$i]['month_name'];
                 $i++;
            }
            
            for($i=0; $i<count($monthNames); $i++){
                $monthName = $monthNames[$i];
                $output[$monthName] = array();
                $j = $i;
                foreach($commNames as $comm){
                    $output[$monthName][$comm] = $result[$j]['consumption'];
                    $j += 12;
                }
            }
            
            //var_dump($output); exit;
            return $output;
       }
}

?>
