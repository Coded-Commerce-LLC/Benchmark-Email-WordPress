<?php

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.form.formfield' );

class JFormFieldContactList extends JFormField {

	protected $type = 'ContactList';

	public function getInput() {

		// Joomla! WordPress Bootup, Also Sets API Key
		require_once( JPATH_ADMINISTRATOR . '/components/com_benchmarkemaillite/controllers/joomla-wp-bootup.php' );

		// Look Up Their Lists
		$contact_lists = benchmarkemaillite_api::lists();

		// Create Dropdown Showing Their Lists
		$output = '<select id="' . $this->id . '" name="' . $this->name . '">';
		$output .= '<option value="">Select an option</option>';
		foreach( $contact_lists as $val ) {
			$selected = $this->value == $val['id'] ? 'selected="selected"' : '';
			$output .= sprintf(
				'<option value="%d" %s>%s</option>',
				$val['id'], $selected, $val['listname']
			);
		}
		$output	.= '</select>';
		return $output;
	}
}