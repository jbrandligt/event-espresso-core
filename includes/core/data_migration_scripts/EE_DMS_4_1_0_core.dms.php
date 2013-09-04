<?php
/**
 * meant to convert DBs between 3.1.26 and 4.0.0 to 4.1.0
 */
class EE_DMS_4_1_0_core extends EE_Data_Migration_Script_Base{
	public function can_migrate_from_version($version_string) {
		if($version_string < '4.0.0' &&$version_string > '3.1.26' ){
			return true;
		}else{
			return false;
		}
	}
	public function count_records_to_migrate() {
		return 10;
	}
	public function count_records_migrated() {
		return 5;
	}
	public function pretty_name() {
		return __("Core Data Migration to version 4.1.0", "event_espresso");
	}
	public function schema_changes_before_migration() {
		return true;
	}
	public function schema_changes_after_migration() {
		return true;
	}
	
	
	public function migration_step($num_records_to_migrate = 50) {
		return array(EE_Data_Migration_Script_Base::status_completed,'all done 4.1.0');
	}
}
