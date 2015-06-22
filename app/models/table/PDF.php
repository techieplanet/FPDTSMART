<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PDF
 *
 * @author Swedge
 */

require_once('Helper2.php');
class PDF {
    //put your code here
    
    public function insertLocationIds(){
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $helper = new Helper2();
        $lastPullDate = $helper->getLatestPullDate();
        
        try{
            $select = $db->select()->from(array('pdf_reports'), array('id'))->where("date='$lastPullDate'");
            $result = $db->fetchAll($select);
            if(!empty($result)) return;
            
            $select = $db->select()->from(array('location'), array('id'));
            $result = $db->fetchAll($select);
            //var_dump($result); exit;
            
            //insert the national first. location id 0 used for national
            $bind = array('location_id'=>0, 'date' => $lastPullDate);
            $db->insert('pdf_reports', $bind);
            
            foreach ($result as $location){
                $bind = array('location_id'=>$location['id'], 'date' => $lastPullDate);
                $db->insert('pdf_reports', $bind);
            }

            return 1;
        } catch(Exception $e){
            print $e->getMessage(); exit;
        }
    }
    
    public function getNextLocationDetails(){
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $helper = new Helper2();
        $lastPullDate = $helper->getLatestPullDate();
        
        try{
            $select = $db->select()
                        ->from(array('pr'=>'pdf_reports'), array('id', 'location_id'))
                        ->joinInner(array('l'=>'location'), 'pr.location_id=l.id OR pr.location_id = 0', array('tier'))
                        ->where("date = '$lastPullDate' AND file_generated = 0")
                        ->limit(1);
            
            $result = $db->fetchRow($select);
            
            return array('report_id'=>$result['id'], 
                         'location_id' => $result['location_id'],
                         'tier' => $result['tier']
                    );
        } catch (Exception $ex) {
            print $ex->getMessage(); exit;
        }
    }
    
    
    
}
