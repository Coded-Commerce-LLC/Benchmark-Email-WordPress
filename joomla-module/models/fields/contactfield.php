<?php

/**
 * This file creates the sign up form fields used by Joomla.
 *
 * @package	mod_benchmarkemaillite
 * @license	GNU General Public License version 3; see LICENSE.txt
 *
 */

// No direct access to this file
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.form.formfield' );

class JFormFieldContactField extends JFormField {

	protected $type = 'ContactField';

	public function getInput() {

		// Load Module Translations
		$language = JFactory::getLanguage();
		$language_tag = $language->getTag();
		$language->load( 'mod_benchmarkemaillite', JPATH_SITE . '/modules/mod_benchmarkemaillite', $language_tag, true );

		// Output Field
		return
			'<select id="' . $this->id . '" name="' . $this->name . '">'
			. '<option value="">Select an option</option>'
			. '<option ' . ( $this->value ==  'First Name' ? 'selected="selected"' : '' ) . ' value="First Name">' . __( 'MOD_BENCHMARKEMAILLITE_FIELD_FIRST_NAME' ) . '</option>'
			. '<option ' . ( $this->value ==  'Middle Name' ? 'selected="selected"' : '' ) . ' value="Middle Name">' . __( 'MOD_BENCHMARKEMAILLITE_FIELD_MIDDLE_NAME' ) . '</option>'
			. '<option ' . ( $this->value ==  'Last Name' ? 'selected="selected"' : '' ) . ' value="Last Name">' . __( 'MOD_BENCHMARKEMAILLITE_FIELD_LAST_NAME' ) . '</option>'
			. '<option ' . ( $this->value ==  'Email' ? 'selected="selected"' : '' ) . ' value="Email">' . __( 'MOD_BENCHMARKEMAILLITE_FIELD_EMAIL' ) . '</option>'
			. '<option ' . ( $this->value ==  'Address' ? 'selected="selected"' : '' ) . ' value="Address">' . __( 'MOD_BENCHMARKEMAILLITE_FIELD_ADDRESS' ) . '</option>'
			. '<option ' . ( $this->value ==  'City' ? 'selected="selected"' : '' ) . ' value="City">' . __( 'MOD_BENCHMARKEMAILLITE_FIELD_CITY' ) . '</option>'
			. '<option ' . ( $this->value ==  'State' ? 'selected="selected"' : '' ) . ' value="State">' . __( 'MOD_BENCHMARKEMAILLITE_FIELD_STATE' ) . '</option>'
			. '<option ' . ( $this->value ==  'Zip' ? 'selected="selected"' : '' ) . ' value="Zip">' . __( 'MOD_BENCHMARKEMAILLITE_FIELD_ZIP' ) . '</option>'
			. '<option ' . ( $this->value ==  'Country' ? 'selected="selected"' : '' ) . ' value="Country">' . __( 'MOD_BENCHMARKEMAILLITE_FIELD_COUNTRY' ) . '</option>'
			. '<option ' . ( $this->value ==  'Phone' ? 'selected="selected"' : '' ) . ' value="Phone">' . __( 'MOD_BENCHMARKEMAILLITE_FIELD_PHONE' ) . '</option>'
			. '<option ' . ( $this->value ==  'Fax' ? 'selected="selected"' : '' ) . ' value="Fax">' . __( 'MOD_BENCHMARKEMAILLITE_FIELD_FAX' ) . '</option>'
			. '<option ' . ( $this->value ==  'Cell Phone' ? 'selected="selected"' : '' ) . ' value="Cell Phone">' . __( 'MOD_BENCHMARKEMAILLITE_FIELD_CELL' ) . '</option>'
			. '<option ' . ( $this->value ==  'Company Name' ? 'selected="selected"' : '' ) . ' value="Company Name">' . __( 'MOD_BENCHMARKEMAILLITE_FIELD_B_NAME' ) . '</option>'
			. '<option ' . ( $this->value ==  'Job Title' ? 'selected="selected"' : '' ) . ' value="Job Title">' . __( 'MOD_BENCHMARKEMAILLITE_FIELD_B_TITLE' ) . '</option>'
			. '<option ' . ( $this->value ==  'Business Phone' ? 'selected="selected"' : '' ) . ' value="Business Phone">' . __( 'MOD_BENCHMARKEMAILLITE_FIELD_B_PHONE' ) . '</option>'
			. '<option ' . ( $this->value ==  'Business Fax' ? 'selected="selected"' : '' ) . ' value="Business Fax">' . __( 'MOD_BENCHMARKEMAILLITE_FIELD_B_FAX' ) . '</option>'
			. '<option ' . ( $this->value ==  'Business Address' ? 'selected="selected"' : '' ) . ' value="Business Address">' . __( 'MOD_BENCHMARKEMAILLITE_FIELD_B_ADDRESS' ) . '</option>'
			. '<option ' . ( $this->value ==  'Business City' ? 'selected="selected"' : '' ) . ' value="Business City">' . __( 'MOD_BENCHMARKEMAILLITE_FIELD_B_CITY' ) . '</option>'
			. '<option ' . ( $this->value ==  'Business State' ? 'selected="selected"' : '' ) . ' value="Business State">' . __( 'MOD_BENCHMARKEMAILLITE_FIELD_B_STATE' ) . '</option>'
			. '<option ' . ( $this->value ==  'Business Zip' ? 'selected="selected"' : '' ) . ' value="Business Zip">' . __( 'MOD_BENCHMARKEMAILLITE_FIELD_B_ZIP' ) . '</option>'
			. '<option ' . ( $this->value ==  'Business Country' ? 'selected="selected"' : '' ) . ' value="Business Country">' . __( 'MOD_BENCHMARKEMAILLITE_FIELD_B_COUNTRY' ) . '</option>'
			. '<option ' . ( $this->value ==  'Notes' ? 'selected="selected"' : '' ) . ' value="Notes">' . __( 'MOD_BENCHMARKEMAILLITE_FIELD_NOTES' ) . '</option>'
			. '<option ' . ( $this->value ==  'Extra 3' ? 'selected="selected"' : '' ) . ' value="Extra 3">' . __( 'MOD_BENCHMARKEMAILLITE_FIELD_EXTRA3' ) . '</option>'
			. '<option ' . ( $this->value ==  'Extra 4' ? 'selected="selected"' : '' ) . ' value="Extra 4">' . __( 'MOD_BENCHMARKEMAILLITE_FIELD_EXTRA4' ) . '</option>'
			. '<option ' . ( $this->value ==  'Extra 5' ? 'selected="selected"' : '' ) . ' value="Extra 5">' . __( 'MOD_BENCHMARKEMAILLITE_FIELD_EXTRA5' ) . '</option>'
			. '<option ' . ( $this->value ==  'Extra 6' ? 'selected="selected"' : '' ) . ' value="Extra 6">' . __( 'MOD_BENCHMARKEMAILLITE_FIELD_EXTRA6' ) . '</option>'
			. '</select>';
	}
}