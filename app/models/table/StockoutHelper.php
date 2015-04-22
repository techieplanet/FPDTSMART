<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of StockoutHelper
 *
 * @author Swedge
 */
class StockoutHelper {
    //put your code here
    
        public function getStockoutFacsWithTrainedHWCountByLocation($longWhereClause, $geoList, $tierText, $tierFieldName){
                $db = Zend_Db_Table_Abstract::getDefaultAdapter ();
                $helper = new Helper2();
                
                $select = $db->select()
                            ->from(array('c' => 'commodity'),
                              array('COUNT(DISTINCT(c.facility_id)) AS fid_count'))
                            ->joinInner(array('cno'=>'commodity_name_option'), 'cno.id = c.name_id')
                            ->joinInner(array('fwtc'=>'facility_worker_training_counts_view'), 'c.facility_id = facid')
                            ->joinInner(array('flv' => 'facility_location_view'), 'flv.id = c.facility_id', array('lga', 'state',  'geo_zone'))
                            ->where($longWhereClause)
                            ->group($tierFieldName)
                            ->order(array($tierText));
                        
              $sql = $select->__toString();
              $sql = str_replace('`cno`.*,', '', $sql);
              $sql = str_replace('`fwtc`.*,', '', $sql);
              //echo 'CS: ' . $sql . '<br/>'; exit;

              $result = $db->fetchAll($sql);
              
              $locationNames = $helper->getLocationNames($geoList);
              $locationDataArray = $helper->filterLocations($locationNames, $result, $tierText);
               
            //var_dump($locationDataArray); exit;
            return $locationDataArray;
       }
       
       
       public function getFacsProvidingButStockedout($mainWhereClause, $subWhereClause, $geoList, $tierText, $tierFieldName){
                $db = Zend_Db_Table_Abstract::getDefaultAdapter ();
                $helper = new Helper2();
                
                $subselect = $db->select()
                              ->from(array('c' => 'commodity'), array('DISTINCT(c.facility_id) AS providingfacs'))
                              ->joinInner(array('cno' => 'commodity_name_option'), 'c.name_id = cno.id')
                              ->where($subWhereClause);
                
                $select = $db->select()
                            ->from(array('c' => 'commodity'),
                              array('COUNT(DISTINCT(c.facility_id)) AS fid_count'))
                            ->joinInner(array('cno' => 'commodity_name_option'), 'cno.id = c.name_id')
                            ->joinInner(array('flv' => 'facility_location_view'), 'flv.id = c.facility_id', array('lga', 'state',  'geo_zone'))
                            ->where($mainWhereClause . ' AND c.facility_id IN (' . $subselect . ')')
                            ->group($tierFieldName)
                            ->order(array($tierText));                          

                $sql = $select->__toString();
                $sql = str_replace('AS `count`,', 'AS `count`', $sql);
                $sql = str_replace('`c`.*,', '', $sql);
                $sql = str_replace('`cno`.*,', '', $sql);
                $sql = str_replace('`cno`.*', '', $sql);
                $sql = str_replace('`flv`.*', '', $sql);
                $sql = str_replace('`providingfacs`,', '`providingfacs`', $sql);
                //echo 'Stocked out Providing: ' . $sql . '<br/>'; exit;

               $result = $db->fetchAll($sql);
               
              //filter for only valid values
              $locationNames = $helper->getLocationNames($geoList);
              $locationDataArray = $helper->filterLocations($locationNames, $result, $tierText);
               
            //var_dump($locationDataArray); exit;
            return $locationDataArray;
       }
}

?>
