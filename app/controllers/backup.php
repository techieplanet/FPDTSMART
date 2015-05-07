<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


 /*
                             * TP: trying to check where the header is. We want to start our reading from where the header starts. We skip the logo and titles
                             */
                            
                            //print_r($row[34]);
                            $row = $row[$p];
                           if($row[4]=="" || $row[4]==" "){
                               
                               continue;
                               
                           }
				$values = array();
				if (! is_array($row) )
					continue;		   // sanity?
				if (! isset($cols) ) { // set headers (field names)
					$cols = $row;	   // first row is headers (field names)
					continue;
                                        
				}
                                echo '<br/>';
                               // exit;
				$countValidFields = "";
				if (! empty($row) ) {  // add
					foreach($row as $i=>$v){ // proccess each column
                                            
                                           print_r( $v);
					/*	
                                            if ( empty($v) && $v !== '0' )
							continue;
						if ( $v == 'n/a') // has to be able to process values from a data export
							$v = NULL;
                                                //$countValidFields = 0;
						$countValidFields++;
                                               // echo $v.'<br/>';
						$delimiter = strpos($v, ','); // is this field a comma seperated list too (or array)?
						if ($delimiter && $v[$delimiter - 1] != '\\')	// handle arrays as field values(Export), and comma seperated values(import manual entry), and strings or int
							$values[$cols[$i]] = explode(',', $this->sanitize($v));
						else
							$values[$cols[$i]] = $this->sanitize($v);
                                               // echo 'This is the value '.$v.'<br/>';
                                               // echo $values[$cols[$i]].'<br/>';*/
					}
                                       // exit;
                                        echo '<br/>';
				}
				// done now all fields are named and in $values[my_field]
				if ( $countValidFields ) {
					//validate
					if ( isset($values['uuid']) ){ unset($values['uuid']); }
					if ( isset($values['id']) )  { unset($values['id']); }
					if ( isset($values['is_deleted']) ) { unset($values['is_deleted']); }
					if ( isset($values['created_by']) ) { unset($values['created_by']); }
					if ( isset($values['modified_by']) ){ unset($values['modified_by']); }
					if ( isset($values['timestamp_created']) ){ unset($values['timestamp_created']); }
					if ( isset($values['timestamp_updated']) ){ unset($values['timestamp_updated']); }
					if ( ! $this->hasACL('approve_trainings') ){ unset($values['approved']); }
					$values['birthdate'] = $this->_date_to_sql($values['birthdate']);
					$values['facility_id'] = $values['facility_id'] ? $values['facility_id'] : 0;

					//locations
					$regionNames = array (t('Region A (Province)'), t('Region B (Health District)'), t('Region C (Local Region)'), t('Region D'), t('Region E'), t('Region F'), t('Region G'), t('Region H'), t('Region I') );
					$num_location_tiers = $this->setting('num_location_tiers');
					$bSuccess = true;
					$facility_id = null;
					$fac_location_id = null;

					if ( $values['facility_name'] ) { // something set for facility (name or id) (id is duplicated to name to support importing from a data export.... TODO clean this up now that both fields are supported in this function)

						if (! $values['facility_id']) { // get the id somehow

							if(is_array($values['facility_name']))
								$values['facility_id'] = $values['facility_name'][0]; //
							else if ( is_numeric($values['facility_name']) && !trim( $values[ t('Region A (Province)') ] ) ) // bugfix: numbers w/ no province = ID, numbers + location data = Fac Name all numbers... its in facility_name b/c of data export
								$values['facility_id'] = $values['facility_name']; // support export'ed values. (remap)
							else // lookup id
							{
								// verify location, do not allow insert
								$tier = 1;
								for ($i=0; $i <= $num_location_tiers; $i++) { // find locations
									$regionName = $regionNames[$i]; // first location field in csv row // could use this too: $values[t('Region A (Province)')]
									if ( empty($values[$regionName]) || $bSuccess == false )
										continue;
									$fac_location_id = $db->fetchOne(
										"SELECT id FROM location WHERE location_name = '". $values[$regionName] . "'"
										. ($fac_location_id ? " AND parent_id = $fac_location_id " : '')
										. " LIMIT 1");
									if (! $fac_location_id) {
										$bSuccess = false;
										break;
									}
									$tier++;
								}

								// lookup facility
								if ($fac_location_id) {
									$facility_id = $db->fetchOne( "SELECT id FROM facility WHERE location_id = $fac_location_id AND facility_name = '".$values['facility_name']."' LIMIT 1" );
									$values['facility_id'] = $facility_id ? $facility_id : 0;
								} else {
									$errs[] = t('Error locating region or city:').' '.$values[$regionName].' '.t('Facility').': '.$values['facility_name'].space.t("This person will have no assigned facility if the save is successful.");
								}
								if (! $values['facility_id'] && $bSuccess) { // found region(bSuccess) but not facility
									$errs[] = t('Error locating facility:').space.$values['facility_name'].space.t("This person will have no assigned facility if the save is successful.");
								}
							}
						}
					} else {
						if (! $values['facility_id'])
							$errs[] = t('Error locating facility:').$values['facility_name'].space.t("This person will have no assigned facility if the save is successful.");
					}
					$bSuccess = true; //reset, we allow saving with no facility.

					//dupecheck
					$dupe = new Person();
					$select = $dupe->select()->where('facility_id = "' . $values['facility_id'] . '" and first_name = "' . $values['first_name'] . '" and last_name = "'.$values['last_name'].'"');
					if( $values['facility_id'] && $dupe->fetchRow($select) ) {
						$errs[] = t ( 'A person with this name already exists in the database, the user was not added.' ).space.t('Name').': '.$values['first_name'].space.$values['last_name'];
						$bSuccess = false;
					}
					if(! $bSuccess)
						continue;

					//field mapping (Export vs import)
					if ( isset($values["qualification_phrase"]) )            $values["primary_qualification_option_id"] = $values["qualification_phrase"];
					if ( isset($values["primary_qualification_phrase"]) )    $values["primary_qualification_option_id"] = $values["primary_qualification_phrase"];
					if ( isset($values["primary_responsibility_phrase"]) )   $values["primary_responsibility_option_id"] = $values["primary_responsibility_phrase"];
					if ( isset($values["secondary_responsibility_phrase"]) ) $values["secondary_responsibility_option_id"] = $values["secondary_responsibility_phrase"];
					if ( isset($values["highest_edu_level_phrase"]) )        $values["highest_edu_level_option_id"] = $values["highest_edu_level_phrase"];
					if ( isset($values["attend_reason_phrase"]) )            $values["attend_reason_option_id"] = $values["attend_reason_phrase"];
					if ( isset($values["custom_1"]) )                        $values["person_custom_1_option_id"] = $values["custom_1"];
					if ( isset($values["custom_2"]) )                        $values["person_custom_2_option_id"] = $values["custom_2"];
					//save
					try {
						//$values['title_option_id']                    = $this->_importHelperFindOrCreate('person_title_option',           'title_phrase',           $values['title_option_id']); //title_option_id multiAssign (insert via helper)
						//$values['suffix_option_id']                   = $this->_importHelperFindOrCreate('person_suffix_option',          'suffix_phrase',          $values['suffix_option_id']);
						$values['primary_qualification_option_id']    = $this->_importHelperFindOrCreate('person_qualification_option',   'qualification_phrase',   $values['primary_qualification_option_id']);
						$values['primary_responsibility_option_id']   = $this->_importHelperFindOrCreate('person_responsibility_option',  'responsibility_phrase',  $values['primary_responsibility_option_id']);
						$values['secondary_responsibility_option_id'] = $this->_importHelperFindOrCreate('person_secondary_responsibility_option',  'responsibility_phrase', $values['secondary_responsibility_option_id']);
						$values['attend_reason_option_id']            = $this->_importHelperFindOrCreate('person_attend_reason_option',   'attend_reason_phrase',   $values['attend_reason_option_id']);
						$values['person_custom_1_option_id']          = $this->_importHelperFindOrCreate('person_custom_1_option',        'custom1_phrase',         $values['person_custom_1_option_id']);
						$values['person_custom_2_option_id']          = $this->_importHelperFindOrCreate('person_custom_2_option',        'custom2_phrase',         $values['person_custom_2_option_id']);
						$values['highest_level_option_id']            = $this->_importHelperFindOrCreate('person_education_level_option', 'education_level_phrase', $values['highest_level_option_id']);
						//$values['courses']                            = $this->_importHelperFindOrCreate('???',         '?????', null, $values['courses']);
						$personrow = $personObj->createRow();
						$personrow = ITechController::fillFromArray($personrow, $values);
						$row_id = $personrow->save();
					} catch (Exception $e) {
						$errored = 1;
						$errs[]  = nl2br($e->getMessage()).' '.t ( 'ERROR: The person could not be saved.' );
					}
					if(! $row_id){
						$errored = 1;
						$errs[] = t('That person could not be saved.').space.t("Name").": ".$values['first_name'].space.$values['last_name'];
					}
					//sucess - done
                                }
		
?>
