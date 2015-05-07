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
require_once('ConsumptionHelper.php');

class Consumption {
    //put your code here

     /* TP:
         * This method gets the count of coverage of trained workers in various 
         * geo-locations and tiers. Both FP and LARC
         */
        public function fetchConsumptiomPerCommodity($commodity_id=0, $geoList, $tierValue){
            $db = Zend_Db_Table_Abstract::getDefaultAdapter ();
            $output = array (); 
            $helper = new Helper2();

            $tierText = $helper->getLocationTierText($tierValue);
            $tierFieldName = $helper->getTierFieldName($tierText);
            $locationNames = $helper->getLocationNames($geoList);
            $latestDate = $helper->getLatestPullDate();
            
            //where clauses
            if($commodity_id > 0)
                $commodityWhere = "c.name_id = $commodity_id";
            else{
                $commIDs = $helper->getCommodityNames('',true);
                $commodityWhere = "c.name_id IN (" . $commIDs . ')';
            }
            $dateWhere = 'c.date =\'' . $latestDate . '\'';
            $locationWhere = $tierFieldName . ' IN (' . $geoList . ')';
            
            //where c.name_id IN ('10', '11', '15', '18', '24', '27', '30') AND date = '2014-12-01' AND flv.geo_parent_id IN ('1811', '1812', '1813', '1814', '1815', '1816')
            $longWhereClause = $commodityWhere . ' AND ' . $dateWhere . ' AND ' . $locationWhere;
            //echo $longWhereClause; exit;
            
            $consHelper = new ConsumptionHelper();
            $consByCommodity = $consHelper->getCommConsumptionByCommodity($commodity_id, $longWhereClause, $geoList);
            
            return $consByCommodity;
        }

       
        
        public function fetchConsumptiomByGeography($commodity_id=0, $geoList, $tierValue){
            $db = Zend_Db_Table_Abstract::getDefaultAdapter ();
            $output = array (); 
            $helper = new Helper2();

            $tierText = $helper->getLocationTierText($tierValue);
            $tierFieldName = $helper->getTierFieldName($tierText);
            $locationNames = $helper->getLocationNames($geoList);
            $latestDate = $helper->getLatestPullDate();
            
            //where clauses
            if($commodity_id > 0)
                $commodityWhere = "c.name_id = $commodity_id";
            else{
                $commIDs = implode(',',$helper->getCommodityNames('', true));
                $commodityWhere = "c.name_id IN (" . $commIDs . ')';
            }
            $dateWhere = 'c.date =\'' . $latestDate . '\'';
            $locationWhere = $tierFieldName . ' IN (' . $geoList . ')';
            
            //where c.name_id IN ('10', '11', '15', '18', '24', '27', '30') AND date = '2014-12-01' AND flv.geo_parent_id IN ('1811', '1812', '1813', '1814', '1815', '1816')
            $longWhereClause = $commodityWhere . ' AND ' . $dateWhere . ' AND ' . $locationWhere;
            //echo 'geo: ' . $longWhereClause; exit;
            
            $consHelper = new ConsumptionHelper();
            $consByGeo = $consHelper->getCommConsumptionByGeography($commodity_id, $longWhereClause, $locationNames, $geoList, $tierText, $tierFieldName);
            
            //var_dump($consByGeo); exit;
            return $consByGeo;
        }
        
        
        public function fetchConsumptionByCommodityOverTime(){
            $db = Zend_Db_Table_Abstract::getDefaultAdapter ();
            $output = array (); 
            $helper = new Helper2();
            
            $dateWhere = 'c.date <= (SELECT MAX(date) FROM facility_report_rate) AND c.date >= DATE_SUB((SELECT MAX(date) FROM facility_report_rate), INTERVAL 11 MONTH)';
            $commodityWhere = "(commodity_type = 'fp' OR commodity_type = 'larc')";
            
            $longWhereClause = $dateWhere . ' AND ' . $commodityWhere;
            $commNames = explode(',',$helper->getCommodityNames('', false));
            //var_dump($commNames); exit;
            $consHelper = new ConsumptionHelper();
            $consOverTime = $consHelper->getConsumptionByCommodityOverTime($longWhereClause, $commNames);
            
            return $consOverTime;
        }
}

?>