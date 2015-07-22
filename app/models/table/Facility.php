<?php
/*
 * Created on Feb 14, 2008
 * 
 *  Built for web  
 *  Fuse IQ -- todd@fuseiq.com
 *  
 */

require_once ('ITechTable.php');
require_once ('Helper2.php');

class Facility extends ITechTable {
	protected $_name = 'facility';
	protected $_primary = 'id';
	
	/**
	 * Returns false if the name already exists
	 */
	public static function isUnique($facilityName, $id = false) {
		$fac = new Facility ( );
		$select = $fac->select ();
		$select->where ( "facility_name = ?", $facilityName );
		if ( $id )
		  $select->where ( "id != ?", $id);
		if ($fac->fetchRow ( $select ))
			return false;
		
		return true;
	
	}
	
	public static function isReferenced($id) {
		require_once ('Person.php');
		
		$person = new Person ( );
		$select = $person->select ();
		$select->where ( "facility_id = ?", $id );
		$select->where ( "is_deleted = 0" );
		if ($person->fetchRow ( $select ))
			return true;
		
		return false;
	}
	
	 /**
   * Selects all facilities along with province and district
   *
   * @param unknown_type $num_tiers
   * @return unknown
   */
	public static function selectAllFacilities($num_tiers = 4) {
		$db = Zend_Db_Table_Abstract::getDefaultAdapter ();

		list($field_name,$location_sub_query) = Location::subquery($num_tiers, false, false);
		$sql = 'SELECT facility.id ,
	            facility.facility_name,
	            facility.location_id,
	             '.implode(',',$field_name).'
	          FROM facility JOIN ('.$location_sub_query.') as l ON facility.location_id = l.id 
	          WHERE facility.is_deleted = 0 ORDER BY province_name'.($num_tiers > 2?', district_name':'').', facility_name';

		return $db->fetchAll($sql);  
	}
 
 	public static function saveSponsors ( $id, $sponsor_array, $sponsor_date_array, $sponsor_end_date_array )
 	{
		// save facility_sponsors table data)
		if (empty($sponsor_array))
			return false;

		$stable = new ITechTable ( array ('name' => 'facility_sponsors' ) );
		$select = $stable->select()->where( 'facility_id = '.$id );
		$rows = $stable->fetchAll($select);
		$existing_rows = array();
		foreach ($rows as $i => $row) {
			if ($i > count($sponsor_array))
				$row->delete();              // i.e. theres 5 existing rows but we are now saving 4
			else
				$existing_rows[] = $row;     // re-use row. no ->seek() or getRow()?? i'll use an array
		}

		foreach($sponsor_array as $i => $sponsid)
		{
			try{
			// define database row
			if ($existing_rows && count($existing_rows) > $i) { // use existing row if there is one
				#$existing_rows->getRow($i); // no ->seek() or getRow()??
				#$row = $existing_rows->current();
				$row = $existing_rows[$i];
			}
			else {
				$row = $stable->createRow();
			}

			if (empty($sponsid)){          // remove unused rows
				$row->delete();
				continue;
			}
			// fill row
			$row->facility_id                = $id;
			$row->facility_sponsor_phrase_id = $sponsid;
			$row->start_date                 = isset($sponsor_date_array[$i]) && !empty($sponsor_date_array[$i]) ? date('Y-m-d', strtotime($sponsor_date_array[$i])) : '0000-00-00';
			$row->end_date                   = isset($sponsor_end_date_array[$i]) && !empty($sponsor_end_date_array[$i]) ? date('Y-m-d', strtotime($sponsor_end_date_array[$i])) : '0000-00-00';
			$row->is_default = 0;
			$row->is_deleted = 0;
			// save
			$row->save();
			}catch(Exception $e){
				return false;
			}
		}
		return true;
 	}
 	
 	/*
 	 * TA:17: 09/08/2014
 	 */
 	public static function saveCommodities ( $id, $commodity_array)
 	{
 		// save commodity table data)
 		if (empty($commodity_array))
 			return false;
 	
 		$stable = new ITechTable ( array ('name' => 'commodity' ) );
 		foreach($commodity_array as $i => $com){
 			try{
 				
 					$row = $stable->createRow();

 				// fill row
 				$row->facility_id                = $id;
 				$row->consumption =  $com['consumption'];
 				$row->stock_out    = ( $com['stock_out'] == null ||  $com['stock_out'] == '' )? "N" : "Y";
 				$row->name_id  =  $com['id']; 

 				$convert_date = explode("/", $com['date']);
 				if(count($convert_date) == 2){
 					$row->date =  date('Y-m-d', strtotime( $convert_date[0] . "/01/" . $convert_date[1]));
 				}else{
 					$row->date =  date('Y-m-d', strtotime($com['date']));
 				}
 				// save
 				$row->save();
 			}catch(Exception $e){
 				print $e;
 				return false;
 			}
 		}
 		return true;
 	}
 	
 	/*
 	 * TA:17: 09/08/2014
 	*/
 	public static function deleteCommodities ( $commodity_ids)
 	{
 		if ($commodity_ids === "")
 			return false;
 	
 		$stable = new ITechTable ( array ('name' => 'commodity' ) );
 			try{		
 				$stable->delete("id in ($commodity_ids)");
 			}catch(Exception $e){
 				print $e;
 				return false;
 			}
 		return true;
 	}
 	
 	//TA:17: added 09/19/2014
 	public function ListCommodityNames(){
 		$select = $this->dbfunc()->select()
 		->from('commodity_name_option');
 		$result = $this->dbfunc()->fetchAll($select);
 		return $result;
 	}
 	
	/*
	public static function suggestionList($match = false, $limit = 100) {
		$rows = self::suggestionQuery ( $match, $limit );
		$rowArray = $rows->toArray ();
		
		return $rowArray;
	}
	*/
	/*
	protected static function suggestionQuery($match = false, $limit = 100) {
		require_once ('models/table/OptionList.php');
		$facilityTable = new OptionList ( array ('name' => 'facility' ) );
		
		$select = $facilityTable->select ()->from ( 'facility', array ('facility_name', 'd.district_name', 'p.province_name', 'id', 'district_id', 'province_id' ) )->setIntegrityCheck ( false );
		$select->joinLeft ( array ('d' => 'location_district' ), 'facility.district_id = d.id', 'd.district_name' );
		$select->joinLeft ( array ('p' => 'location_province' ), 'facility.province_id = p.id', 'p.province_name' );
		
		//look for char start
		if ($match) {
			$select->where ( 'facility_name LIKE ? ', $match . '%' );
		}
		$select->where ( 'facility.is_deleted = 0' );
		$select->order ( 'facility_name ASC' );
		
		if ($limit)
			$select->limit ( $limit, 0 );
		
		$rows = $facilityTable->fetchAll ( $select );
		
		return $rows;
	}
*/

        
        
/************************************** TP methods ****************************************/
        public function getAllFacilityCount(){
            $db = Zend_Db_Table_Abstract::getDefaultAdapter ();
            
            $select = $db->select()->from(array('f'=>'facility'), 'COUNT(id) AS count');
            //echo $select->__toString(); exit;
            $result = $db->fetchRow($select);
            return $result['count'];
        }
        public function getAllFacilityCountByLocation($locationWhere){
              $db = Zend_Db_Table_Abstract::getDefaultAdapter ();
            
            $select = $db->select()->from(array('flv'=>'facility_location_view'), 'COUNT(id) AS count')
                    ->where($locationWhere);
            //echo $select->__toString(); exit;
            $result = $db->fetchRow($select);
            return $result['count'];
        }
        
        /*
         * Returns the count of all facilities returning for the currrent month
         */
        public function getAllCurrentReportingFacilityCount(){
            $db = Zend_Db_Table_Abstract::getDefaultAdapter ();
            
            $select = $db->select()
                        ->from(array('f'=>'facility'), 'COUNT(f.id) AS count')
                        ->joinInner(array('frr' => 'facility_report_rate'), 'f.external_id = frr.facility_external_id');
            $result = $db->fetchRow($select);
            return $result['count'];
        }
        
        
        /*
       Returns all facilities for a given location
         */
        public function getFacilityByLocation($locationWhere){
            $db = Zend_Db_Table_Abstract::getDefaultAdapter ();
            
            $select = $db->select()
                    ->from(array('flv'=>'facility_location_view'))
                    ->where($locationWhere);
             $result = $db->fetchAll($select);
             return $result;
            
        }
        /*
         * Returns the count of all facilities returning for the currrent month
         * that have trained health worker in a cetain training type.
         */
        public function getCurrentReportingFacsWithTrainedHWCount($trainingTypeWhere, $dateWhere, $geoList, $tiertext, $tierFieldName, $national=false){
            $db = Zend_Db_Table_Abstract::getDefaultAdapter ();
            $tiertext = !$national ? $tiertext : array();
            
            $select = $db->select()
                        ->from(array('c'=>'commodity'), 'COUNT(DISTINCT(facility_id)) AS count')
                        ->joinInner(array('fwtc' => 'facility_worker_training_counts_view'), 'c.facility_id = fwtc.facid')
                        ->joinInner(array('flv' => 'facility_location_view'), 'flv.id = c.facility_id')
                        ->where($trainingTypeWhere . ' AND facility_reporting_status = 1')
                        ->where($dateWhere)
                        ->where($tierFieldName . ' IN (' . $geoList . ')')
                        ->group($tiertext);
            
            
            
            $sql = $select->__toString();
            $sql = str_replace('AS `count`,', 'AS `count`', $sql);
            $sql = str_replace('`f`.*,', '', $sql);	        
            $sql = str_replace('`frr`.*,', '', $sql);	        
            $sql = str_replace('`fwtc`.*', '', $sql);
            
            //echo 'sql: ' . $sql; exit;
            $result = $db->fetchRow($select);
            return $result['count'];
        }        


        /*
         * Returns the count of all facilities returning for the currrent month
         * from the commodity table
         */
        public function getAllCommodityReportingFacWithHWCount($trainingTypeWhere){
            $db = Zend_Db_Table_Abstract::getDefaultAdapter ();
            
            $select = $db->select()
                        ->from(array('f'=>'facility'), 'COUNT(f.id) AS count')
                        ->joinInner(array('c' => 'commodity'), 'f.id = c.facility_id')
                        ->joinInner(array('fwtc' => 'facility_worker_training_counts_view'), 'f.id = fwtc.facid')
                        ->where($trainingTypeWhere)
                        ->where('( (MONTH(c.date)) = (SELECT MONTH(MAX(c.date)))  AND YEAR(c.date) = (SELECT YEAR(MAX(c.date))) )');
            
            $sql = $select->__toString();
            $sql = str_replace('AS `count`,', 'AS `count`', $sql);
            $sql = str_replace('`f`.*,', '', $sql);	        
            $sql = str_replace('`frr`.*,', '', $sql);	        
            $sql = str_replace('`fwtc`.*', '', $sql);
            
            //echo 'sql: ' . $sql; exit;
            $result = $db->fetchRow($select);
            return $result['count'];
       }
       
       
       public function getFacilityCountByLocation($longWhereClause, $geoList, $tierText, $tierFieldName){
                $db = Zend_Db_Table_Abstract::getDefaultAdapter ();
                $helper = new Helper2();
                
                $select = $db->select()
                            ->from(array('flv' => 'facility_location_view'), array('COUNT(id) AS fid_count','lga', 'state',  'geo_zone'))
                            ->where($longWhereClause)
                            ->group($tierFieldName)
                            ->order(array($tierText));
                
             //echo 'FCL: ' . $select->__toString() . '<br/>'; exit;

              $result = $db->fetchAll($select);
              
              $locationNames = $helper->getLocationNames($geoList);
              $locationDataArray = $helper->filterLocations($locationNames, $result, $tierText);
               
            //var_dump($locationDataArray); exit;
            return $locationDataArray;
       }
       
       
        
}
