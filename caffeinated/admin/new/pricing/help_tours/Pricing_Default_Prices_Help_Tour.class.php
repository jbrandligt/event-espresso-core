<?php
if (!defined('EVENT_ESPRESSO_VERSION') )
	exit('NO direct script access allowed');

/**
 * Event Espresso
 *
 * Event Registration and Management Plugin for Wordpress
 *
 * @package		Event Espresso
 * @author		Seth Shoultes
 * @copyright	(c)2009-2012 Event Espresso All Rights Reserved.
 * @license		http://eventespresso.com/support/terms-conditions/  ** see Plugin Licensing **
 * @link		http://www.eventespresso.com
 * @version		4.0
 *
 * ------------------------------------------------------------------------
 *
 * Pricing_Default_Prices_Help_Tour
 *
 * This is the help tour object for the Default Prices page
 *
 *
 * @package		Pricing_Default_Prices_Help_Tour
 * @subpackage	caffeinated/admin/new/pricing/help_tours/Pricing_Default_Prices_Help_Tour.core.php
 * @author		Darren Ethier
 *
 * ------------------------------------------------------------------------
 */
class Pricing_Default_Prices_Help_Tour extends EE_Help_Tour {

	public function __construct() {
		$this->_label = __('Default Prices Tour', 'event_espresso');
		$this->_slug = 'default-prices-joyride';
		parent::__construct();
	}


	protected function _set_tour_stops() {
		$this->_stops = array(
			10 => array(
				'content' => $this->_start(),
				),
			20 => array(
				'id' => 'event-espresso_page_pricing-search-input',
				'content' => $this->_search_stop(),
				'options' => array(
					'tipLocation' => 'left',
					'tipAdjustmentY' => -50,
					'tipAdjustmentX' => -15
					)
				),
			30 => array(
				'id' => 'name',
				'content' => $this->_name_column_stop(),
				'options' => array(
					'tipLocation' => 'top',
					'tipAdjustmentY' => -40
					)
				),
			40 => array(
				'id' => 'type',
				'content' => $this->_type_column_stop(),
				'options' => array(
					'tipLocation' => 'top',
					'tipAdjustmentY' => -40
					)
				),
			50 => array(
				'id' => 'description',
				'content' => $this->_description_column_stop(),
				'options' => array(
					'tipLocation' => 'top',
					'tipAdjustmentY' => -40
					)
				),
			60 => array(
				'id' => 'amount',
				'content' => $this->_amount_column_stop(),
				'options' => array(
					'tipLocation' => 'left',
					'tipAdjustmentY' => -50,
					'tipAdjustmentX' => 0,
					)
				),
			70 => array(
				'id' => 'the-list',
				'content' => $this->_tbody_stop(),
				'options' => array(
					'tipLocation' => 'top',
					'nubPosition' => 'left',
					'tipAdjustmentY' => 100,
					'tipAdjustmentX' => 200
					)
				),
			
			80 => array(
				'id' => 'contextual-help-link',
				'content' => $this->_end(),
				'button_text' => __('End Tour', 'event_espresso'),
				'options' => array(
					'tipLocation' => 'left',
					'tipAdjustmentY' => -20,
					'tipAdjustmentX' => 10
					)
				)
			);
	}


	protected function _start() {
		$content = '<h3>' . __('Welcome to the Prices Admin Pages!', 'event_espresso') . '</h3>';
		$content .= '<p>' . __('An introduction to the Price Admin page!', 'event_espresso') . '</p>';
		return $content;
	}

	

	protected function _search_stop() {
		return '<p>' . __('Fields that will be searched with the value from the search are: Price Name, Price Description, Price Amount, or Price Type name', 'event_espresso') . '</p>';
	}


	protected function _name_column_stop() {
		return '<p>' . __('about the Price name column', 'event_espresso') . '</p>';
	}


	protected function _type_column_stop() {
		return '<p>' . __('about the Price type column', 'event_espresso') . '</p>';
	}


	protected function _description_column_stop() {
		return '<p>' . __('about the Price description column', 'event_espresso') . '</p>';
	}


	protected function _amount_column_stop() {
		return '<p>' . __('about the Price amount column', 'event_espresso') . '</p>';
	}


	protected function _tbody_stop() {
		return '<p>' . __('about the price table in general including things like how you can sort etc.', 'event_espresso') . '</p>';
	}

	protected function _end() {
		return '<p>' . __('That\'s it for the tour through the Price Admin!  At any time you can restart this tour by clicking on this help dropdown and then clicking the Default Prices Tour button.  All the best with your events!', 'event_espresso') . '</p>';
	}
}