<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Report
 *
 * @author SWEDGE
 */

class Report {
    //put your code here
    
    //TP: Getting the annual date range 
    public function getAnnualDateRange($start_year,$end_year){
        
        $starts_years = array();
                        $ends_years = array();
                        for($i=$start_year; $i<=$end_year; $i++){
                           $start_date = $i."-01-01";
                           $end_date = $i."-12-31";
                           array_push($starts_years,$start_date);
                           array_push($ends_years,$end_date);
                           }
                           return array($starts_years,$ends_years);
    }
    
    public function check_length_add_one($value){
           if(strlen($value)==1){
               $value = "0".$value;
               
           }
           return $value;
       }
    //TP: Getting the monthly date range
    public function getMonthlyDateRange($start_month,$start_year,$end_month,$end_year){
        $year_diff = $end_year-$start_year;
                        $starts_years = array();
                        $ends_years = array();
                        if($year_diff==0){
                            $month_limit = $end_month;
                            for($i=$start_month;$i<=$month_limit;$i++){
                                $i = $this->check_length_add_one($i);
                                $start_date = $end_year."-".$i."-01";
                                $end_date = $end_year."-".$i."-31";
                                array_push($starts_years,$start_date);
                                array_push($ends_years,$end_date);
                                 
                            }
                            
                             }else{
                            for($r=$start_year;$r<=$end_year;$r++){
                                if($r==$start_year){
                                    $month_start = $start_month;
                                    
                                }else{
                                    $month_start = "01";
                                }
                                
                                if($r==$end_year){
                                    $month_limit = $end_month;
                                }
                                else{
                                    $month_limit = "12";
                                }
                                
                              for($i=$month_start;$i<=$month_limit;$i++){
                                  $i = $this->check_length_add_one($i);
                                  $start_date  = $r."-".$i."-01";
                                  $end_date = $r."-".$i."-31";
                                   array_push($starts_years,$start_date);
                                array_push($ends_years,$end_date);
                              }
                                
                            }
                            
                            
                        }
                        return array($starts_years,$ends_years);
        
    }
}

?>
