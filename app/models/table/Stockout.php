<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Stockout
 *
 * @author Swedge
 */
require_once('Facility.php');
require_once('Helper2.php');
require_once('StockoutHelper.php');
class Stockout {
    //put your code here
    
    public function fetchPercentStockOutFacsWithTrainedHW($training_type, $geoList, $tierValue){
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		
                $output = array(array('location'=>'National', 'percent'=>0)); 
                $helper = new Helper2();
                
                //needed variables
                $tierText = $helper->getLocationTierText($tierValue);
                $tierFieldName = $helper->getTierFieldName($tierText);
                $latestDate = $helper->getLatestPullDate();
                
                //where clauses
                if($training_type == 'fp'){
                    $tt_where = "fptrained > 0";
                    $commodityWhere = "commodity_alias = 'so_fp_seven_days'";
                }
                else if($training_type == 'larc'){
                    $tt_where = 'larctrained > 0';
                    $commodityWhere = "commodity_type = 'larc'";
                }
                
                
                $dateWhere = "c.date = '$latestDate'";
                $reportingWhere = 'facility_reporting_status = 1';
                $locationWhere = $tierFieldName . ' IN (' . $geoList . ')';
                $stockoutWhere = "stock_out='Y'";
                $longWhereClause = $reportingWhere . ' AND ' . $dateWhere . ' AND ' . 
                                    $tt_where . ' AND ' . $commodityWhere . ' AND ' .
                                    $stockoutWhere. ' AND ' . $locationWhere;
                
                $stockoutHelper = new StockoutHelper();                
                $numerators = $stockoutHelper->getStockoutFacsWithTrainedHWCountByLocation($longWhereClause, $geoList, $tierText, $tierFieldName);
                
                //change long where
                $longWhereClause = $reportingWhere . ' AND ' . $dateWhere . ' AND ' . 
                                    $tt_where . ' AND ' . $locationWhere;
                $denominators = $stockoutHelper->getStockoutFacsWithTrainedHWCountByLocation($longWhereClause, $geoList, $tierText, $tierFieldName);
             
                $nationalNumerator = 0; $nationalDenominator = 0; 
                foreach ($numerators as $location=>$numer){
                    $nationalNumerator += $numer;
                    $nationalDenominator += $denominators[$location];
                    
                    $output[] = array(
                                'location' => $location,
                                'percent' => $denominators[$location] > 0 ? $numer / $denominators[$location] : 0
                    );
                }
                
                $output[0]['percent'] = $nationalNumerator / $nationalDenominator;
                
                //var_dump($output); exit;
                return $output;
    }
    
    
    public function fetchPercentFacsProvidingButStockedOut($commodity_type, $geoList, $tierValue){
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		
                $output = array(array('location'=>'National', 'percent'=>0)); 
                $helper = new Helper2();
                
                //needed variables
                $tierText = $helper->getLocationTierText($tierValue);
                $tierFieldName = $helper->getTierFieldName($tierText);
                $latestDate = $helper->getLatestPullDate();
                
                //where clauses
                if($commodity_type == 'fp'){
                    $commodityTypeWhere = "commodity_type = 'fp'";
                    $commodityAliasWhere = "commodity_alias = 'so_fp_seven_days'";
                }
                else if($commodity_type == 'larc'){
                    $commodityTypeWhere = "commodity_type = 'larc'";
                    $commodityAliasWhere = "'1=1'";
                    //$commodityAliasWhere = "commodity_alias = 'larc'";
                }
                
                
                $dateWhere = "c.date = '$latestDate'";
                //use 5 months interval because current month is inclusive
                $date6MonthsIntervalWhere = "c.date >= DATE_SUB('$latestDate', INTERVAL 5 MONTH) AND c.date <= '$latestDate'";
                $reportingWhere = 'facility_reporting_status = 1';
                $locationWhere = $tierFieldName . ' IN (' . $geoList . ')';
                $stockoutWhere = "stock_out='Y'";
                $consumptionWhere = 'consumption > 0';
                
                $mainWhereClause = $reportingWhere . ' AND ' . $dateWhere . ' AND ' . 
                                    $commodityAliasWhere . ' AND ' . $stockoutWhere . ' AND ' .
                                    $locationWhere;
                $subWhereClause = $commodityTypeWhere . ' AND ' . $consumptionWhere . ' AND ' .
                                  $date6MonthsIntervalWhere;
                
                $stockoutHelper = new StockoutHelper();                
                $numerators = $stockoutHelper->getFacsProvidingButStockedout($mainWhereClause, $subWhereClause, $geoList, $tierText, $tierFieldName);
                
                //change main where
                $mainWhereClause = $reportingWhere . ' AND ' . $dateWhere . ' AND ' . $locationWhere;
                $denominators = $stockoutHelper->getFacsProvidingButStockedout($mainWhereClause, $subWhereClause, $geoList, $tierText, $tierFieldName);
             
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
}

?>
