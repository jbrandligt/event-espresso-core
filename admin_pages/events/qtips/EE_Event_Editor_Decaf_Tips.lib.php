<?php
if ( ! defined('EVENT_ESPRESSO_VERSION')) { exit('NO direct script access allowed'); }

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
 * EE_Event_Editor_Decaf_Tips	
 *
 * Qtip config for the event editor.
 *
 * @package		Event Espresso
 * @subpackage	/admin_pages/events/qtips/EE_Event_Editor_Decaf_Tips.helper.php
 * @author		Darren Ethier
 *
 * ------------------------------------------------------------------------
 */
class EE_Event_Editor_Decaf_Tips extends EE_Qtip_Config {


	protected function _set_tips_array() {
		$this->_qtipsa = array(
			0 => array(
				'content_id' => 'dtt-evt-start-label',
				'target' => '.event-datetime-DTT_EVT_start_label',
				'content' => $this->_get_event_start_label_info()
				),
			5 => array(
				'content_id' => 'dtt-evt-end-label',
				'target' => '.event-datetime-DTT_EVT_end_label',
				'content' => $this->_get_event_end_label_info()
				),
			10 => array(
				'content_id' => 'dtt-reg-limit-label',
				'target' => '.event-datetime-DTT_reg_limit_label',
				'content' => $this->_get_event_datetime_DTT_reg_limit_label_info()
				),
			15 => array(
				'content_id' => 'dtt-sold-label',
				'target' => '.event-datetime-DTT_sold_label',
				'content' => $this->_get_event_datetime_DTT_sold_label_info()
				),
			20 => array(
				'content_id' => 'tkt-name-label',
				'target' => '.TKT_name_label',
				'content' => $this->_get_event_ticket_TKT_name_label_info()
				),
			25 => array(
				'content_id' => 'tkt-goes-on-sale-label',
				'target' => '.TKT_goes_on_sale_label',
				'content' => $this->_get_event_ticket_TKT_goes_on_sale_label_info()
				),
			30 => array(
				'content_id' => 'tkt-sell-until-label',
				'target' => '.TKT_sell_until_label',
				'content' => $this->_get_event_ticket_TKT_sell_until_label_info()
				),
			35 => array(
				'content_id' => 'tkt-price-label',
				'target' => '.TKT_price_label',
				'content' => $this->_get_event_ticket_TKT_price_label_info()
				),
			40 => array(
				'content_id' => 'tkt-qty-label',
				'target' => '.TKT_qty_label',
				'content' => $this->_get_event_ticket_TKT_qty_label_info()
				),
			45 => array(
				'content_id' => 'tkt-sold-label',
				'target' => '.TKT_sold_label',
				'content' => $this->_get_event_ticket_TKT_sold_label_info()
				),
			50 => array(
				'content_id' => 'tkt-regs-label',
				'target' => '.TKT_regs_label',
				'content' => $this->_get_event_ticket_TKT_regs_label_info()
				),
			);
	}


	private function _get_event_start_label_info() {
		return '<p>' . __('This shows when this event datetime starts.', 'event_espresso') . '</p>';
	}
	private function _get_event_end_label_info() {
		return '<p>' . __('This shows when this event datetime ends.', 'event_espresso') . '</p>';
	}
	private function _get_event_datetime_DTT_reg_limit_label_info() {
		return '<p>' . __('This field allows you to set a maximum number of tickets that you want to make available for an event datetime.', 'event_espresso') . '</p>';
	}
	private function _get_event_datetime_DTT_sold_label_info() {
		return '<p>' . __('This shows the number of tickets that have been sold that have access to this event datetime.', 'event_espresso') . '</p>';
	}
	private function _get_event_ticket_TKT_name_label_info() {
		return '<p>' . __('This is the name of this ticket option.', 'event_espresso') . '</p>';
	}
	private function _get_event_ticket_TKT_goes_on_sale_label_info() {
		return '<p>' . __('This shows when the first ticket is available for sale.', 'event_espresso') . '</p>';
	}
	private function _get_event_ticket_TKT_sell_until_label_info() {
		return '<p>' . __('This shows the date that ticket sales end for this ticket.', 'event_espresso') . '</p>';
	}
	private function _get_event_ticket_TKT_price_label_info() {
		return '<p>' . __('This is the price for this ticket.', 'event_espresso') . '</p>';
	}
	private function _get_event_ticket_TKT_qty_label_info() {
		return '<p>' . __('This field shows the quantity of tickets that are available for this type of ticket.', 'event_espresso') . '</p>';
	}
	private function _get_event_ticket_TKT_sold_label_info() {
		return '<p>' . __('This shows the number of tickets that have been sold for this ticket.', 'event_espresso') . '</p>';
	}
	private function _get_event_ticket_TKT_regs_label_info() {
		return '<p>' . __('This shows the number of registrations that have occurred from ticket sales.', 'event_espresso') . '</p>';
	}
}