<?php
/**
 * Calendar Config
 *
 * NOTE: This module will not function properly without having been initialized through the
 * project specific _config.php file. At the minimum, you need to add the following line:
 *
 * CalendarConfig::init();
 *
 * As calendar implementations often differ substantially, the calendar module
 * can be configured through this file.
 * The configuration shown here is the calendar with all basic features enabled.
 * This will seldom be the case, and hence it's expected that the configuration is amended
 * when the module is instantiated in the project _config.php file.
 *
 *
 * ...and YES, I know SilverStripe 3.1 has a built-in config system, this
 * has been coded prior to this though - pull requests welcome ;)
 *
 * @package calendar
 * @subpackage core
 */
class CalendarConfig {

	/**
	 * Base calendar settings
	 * All basic features are enabled
	 * @var type
	 */
	protected static $settings = array(
		'enabled' => true,
		//the Silverstripe version can be set to skip the need for several branches
		//can be: default, 3.0, 3.1 (other will be added later)
		'ssversion' => 'default',
		//the events subpackage is needed and cannot be disabled,
		//but it can be configured
		'events' => array(
			//allow setting an event as "all day" through a checkbox
			'enable_allday_events' => true,
			//by default the calendar enforces end date/time (or duration), and either won't validate if
			//none is set, or set a default end date/time/duration
			'force_end' => true
		),
		//the admin subpackage is enabled by default and is currently
		//not configuratble
		'admin' => array(),
		//the pagetypes subpackage is enabled by default
		'pagetypes' => array(
			'enable_eventpage' => true,
			'calendarpage' => array(
				'eventlist' => true,
				'calendarview' => true, //fullcalendar
				'search' => true,
				'index' => 'eventlist',
				'fullcalendar_js_settings' => "
					header: {
						left: 'prev, next',
						//center: 'title'
						right: 'title'
						//left: 'prev, next',
						//center: 'title',
						//right: 'month,basicWeek'
					}
				"
			)
		),
		'calendars' => array(
			'enabled' => true,
			'colors' => true,
			//allowing calendars to be shaded
			//this can be used with calendars containing secondary information
			'shading' => false
		),
		'categories' => array(
			'enabled' => true,
			//colors not yet implemented:
			//'colors' => true
		),
		'registrations' => array(
			'enabled' => false,
		),
		'debug' => array(
			//this should only be enabled for debugging
			'enabled' => false,
		),
		'colors' => array(
			'enabled' => true,

			'basepalette' => array(
				'#4B0082' => '#4B0082',
				'#696969' => '#696969',
				'#B22222' => '#B22222',
				'#A52A2A' => '#A52A2A',
				'#DAA520' => '#DAA520',
				'#006400' => '#006400',
				'#40E0D0' => '#40E0D0',
				'#0000CD' => '#0000CD',
				'#800080' => '#800080',
			)
		)
	);

	/**
	 * Config setter & getter
	 * This serves as a settings setter and getter at the same time
	 */
	public static function settings($settings = NULL) {
		if ($settings) {
			//set mode
			self::$settings = self::mergeSettings(self::$settings, $settings);
		} else {
			//get mode
		}

		//always return settings
		return self::$settings;
	}

	/**
	 * method for merging setting files
	 */
	protected static function mergeSettings($Arr1, $Arr2) {
		foreach ($Arr2 as $key => $Value) {
			if (array_key_exists($key, $Arr1) && is_array($Value)) {
				$Arr1[$key] = self::mergeSettings($Arr1[$key], $Arr2[$key]);
			} else {
				$Arr1[$key] = $Value;
			}
		}
		return $Arr1;
	}

	/**
	 * Getter for subpackage specific settings
	 * @param string $subpackage
	 * @return array
	 */
	public static function subpackage_settings($subpackage) {
		$s = self::settings();
		if (isset($s[$subpackage])) {
			return $s[$subpackage];
		}
	}
	/**
	 * Getter that checks if a specific subpackage is enabled
	 * A subpackage is seen as being enabled if
	 * 1. it exists in setting
	 * 2. it
	 *   a) either doesn't have an 'enabled' attribute (then it's enabled by default)
	 *   b) or has an 'enabled' attribute that's set to true
	 * @param string $subpackage
	 * @return boolean
	 */
	public static function subpackage_enabled($subpackage) {
		$s = self::subpackage_settings($subpackage);
		if ($s) {
			//settings need to be an array
			if (is_array($s)) {
				if (isset($s['enabled'])) {
					if ($s['enabled'] == true) {
						return true;
					} else {
						return false;
					}
				} else {
					//if 'enabled' is not defined, the package is enabled - as per definition above
					return true;
				}
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	/**
	 * Getter for a specific setting from a subpackage
	 * @param string $subpackage
	 * @param string $setting
	 */
	public static function subpackage_setting($subpackage, $setting) {
		$s = self::subpackage_settings($subpackage);
		if (isset($s[$setting])) {
			return $s[$setting];
		}
	}


	/**
	 * Calendar initialization
	 * Should be called from the project _config.php file
	 * @param array|null $settings
	 */
	public static function init($settings = null) {
		if (is_array($settings)) {
			//merging settings (and setting the global settings)
			//settings should be submitted via an array
			$settings = self::settings($settings);
		} else {
			$settings = self::settings();
		}

		if ($settings['enabled']) {
			$ssversion = self::subpackage_settings('ssversion');

			//Enabling calendars
			if (self::subpackage_enabled('calendars')) {
				if ($ssversion == '3.0') {
					Object::add_extension('Event','EventCalendarExtension');
				} else {
					Event::add_extension('EventCalendarExtension');
				}
				$s = self::subpackage_settings('calendars');
				if ($s['colors']) {
					if ($ssversion == '3.0') {
						Object::add_extension('Calendar','CalendarColorExtension');
					} else {
						Calendar::add_extension('CalendarColorExtension');
					}
				}
				if ($s['shading']) {
					if ($ssversion == '3.0') {
						Object::add_extension('Calendar','ShadedCalendarExtension');
					} else {
						Calendar::add_extension('ShadedCalendarExtension');
					}
				}

			}
			//Enabling categories
			if (self::subpackage_enabled('categories')) {
				if ($ssversion == '3.0') {
					Object::add_extension('Event','EventCategoryExtension');
				} else {
					Event::add_extension('EventCategoryExtension');
				}
			}
			//Enabling Event Page
			if (self::subpackage_setting('pagetypes','enable_eventpage')) {
				if ($ssversion == '3.0') {
					Object::add_extension('Event','EventHasEventPageExtension');
				} else {
					Event::add_extension('EventHasEventPageExtension');
				}
			}
			//Enabling debug mode
			if (self::subpackage_enabled('debug')) {
				if ($ssversion == '3.0') {
					Object::add_extension('Event','EventDebugExtension');
				} else {
					Event::add_extension('EventDebugExtension');
				}
			}
			//Enabling registrations
			if (self::subpackage_enabled('registrations')) {
				if ($ssversion == '3.0') {
					Object::add_extension('Event','EventRegistrationExtension');
				} else {
					Event::add_extension('EventRegistrationExtension');
				}
			}

			//Adding URL Segment extension to Calendar (currently done for all but could be made configurable later)
			Object::add_extension('Calendar', 'DoURLSegmentExtension');
			if ($ssversion == '3.0') {
				Object::add_extension('Calendar', 'DoURLSegmentExtension');
			} else {
				Calendar::add_extension('DoURLSegmentExtension');
			}

		}
	}


}
