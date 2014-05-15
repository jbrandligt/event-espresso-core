<?php

if (!defined('EVENT_ESPRESSO_VERSION'))
	exit('No direct script access allowed');

/**
 *
 * EE_Register_Addon_Test
 *
 * @package			Event Espresso
 * @subpackage		
 * @author				Mike Nelson
 *
 */
/**
 * @group core/libraries/plugin-api
 * @group agg
 */
class EE_Register_Addon_Test extends EE_UnitTestCase{
	private $_mock_addon_path;
	static function setUpBeforeClass() {
		require_once(EE_TESTS_DIR.'mocks/addons/new-addon/EE_New_Addon.class.php');
		parent::setUpBeforeClass();
	}
	public function __construct($name = NULL, array $data = array(), $dataName = '') {
		$this->_mock_addon_path = EE_TESTS_DIR.'mocks/addons/new-addon/';
		parent::__construct($name, $data, $dataName);
	}
	//test registering a bare minimum addon, and then deregistering it
	function test_register_mock_addon(){
		$min_registration_args = array(
			'version'=>'1.0.0',
			'min_core_version'=>'4.0.0',
			'base_path'=>$this->_mock_addon_path,
			
		);
		//we're registering the addon WAAAY after EE_System has set thing up, so 
		//registering this first time should throw an E_USER_NOTICE
		try{
			EE_Register_Addon::register('New_Addon', $min_registration_args);
			$this->assertTrue(false,'We should have had a warning saying that we are setting up the ee addon at the wrong time');
		}catch(PHPUnit_Framework_Error_Notice $e){
			$this->assertTrue(True);
		}
		try{
			EE_Registry::instance()->addons->EE_New_Addon;
			$this->fail('The addon New_Addon should not have been registered because its called at the wrong time');
		}catch(PHPUnit_Framework_Error_Notice $e){
			$this->assertEquals(EE_UnitTestCase::error_code_undefined_property,$e->getCode());
		}
		
		//try again, this time make it look like we're calling it at the right time
		$this->_pretend_hooking_in_addon_at_right_time();
		EE_Register_Addon::register('New_Addon', $min_registration_args);
		$this->assertAttributeNotEmpty('EE_New_Addon',EE_Registry::instance()->addons);
		
		//now verify we can deregister it
		EE_Register_Addon::deregister('New_Addon');
		try{
			EE_Registry::instance()->addons->EE_New_Addon;
			$this->fail('EE_New_Addon is still registered. Deregister failed');
		}catch(PHPUnit_Framework_Error_Notice $e){
			$this->assertEquals(EE_UnitTestCase::error_code_undefined_property,$e->getCode());
		}
		$this->_stop_pretending_hooking_in_at_right_time();
	}
	
	/**
	 * Modifies the $wp_actions global to make it look like certian actions were and weren't
	 * performed, so that EE_Register_Addon is deceived into thinking it's the right
	 * time to register an addon
	 * @global array $wp_actions
	 */
	private function _pretend_hooking_in_addon_at_right_time(){
		global $wp_actions;
		unset($wp_actions['AHEE__EE_System___detect_if_activation_or_upgrade__begin']);
		$wp_actions['AHEE__EE_System__load_espresso_addons'] = 1;
	}
	/**
	 * Restores the $wp_actions global to how ti should have been before we
	 * started pretending we hooked in at the right time
	 * @global array $wp_actions
	 */
	private function _stop_pretending_hooking_in_at_right_time(){
		global $wp_actions;
		$wp_actions['AHEE__EE_System___detect_if_activation_or_upgrade__begin'] = 1;
		unset($wp_actions['AHEE__EE_System__load_espresso_addons']);
	}
}

// End of file EE_Register_Addon_Test.php