<?php

use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\GridField\GridFieldDataColumns;
use SilverStripe\Forms\GridField\GridFieldEditButton;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\FieldList;
/**
 * CalendarsForm
 *
 * @package calendar
 * @subpackage admin
 */
class CalendarsForm extends CMSForm
{

    /**
     * Contructor
     * @param type $controller
     * @param type $name
     */
    public function __construct($controller, $name)
    {

        //Administering calendars
        if (CalendarConfig::subpackage_enabled('calendars')) {

            //Configuration for calendar grid field
            $gridCalendarConfig = GridFieldConfig_RecordEditor::create();
            $gridCalendarConfig->removeComponentsByType(GridFieldDataColumns::class);
            $gridCalendarConfig->addComponent($dataColumns = new GridFieldDataColumns(), GridFieldEditButton::class);

            $c = singleton('Calendar');
            $summaryFields = $c->summaryFields();

            //$summaryFields = array(
            //	'Title' => 'Title',
            //	//'SubscriptionOptIn' => 'Opt In',
            //	//'Shaded' => 'Shaded'
            //);


            $s = CalendarConfig::subpackage_settings('calendars');


            //show shading info in the gridfield
            if ($s['shading']) {
                $summaryFields['Shaded'] = 'Shaded';
            }

            $dataColumns->setDisplayFields($summaryFields);

            //settings for the case that colors are enabled
            if ($s['colors']) {
                $dataColumns->setFieldFormatting(array(
                    "Title" => '<div style=\"height:20px;width:20px;display:inline-block;vertical-align:middle;margin-right:6px;background:$Color\"></div> $Title'
                ));
            }



            $GridFieldCalendars = new GridField(
                'Calendars', '',
                PublicCalendar::get(),
                $gridCalendarConfig
            );



            $fields = new FieldList(
                $GridFieldCalendars
            );
            $actions = new FieldList();
            $this->addExtraClass('CalendarsForm');
            parent::__construct($controller, $name, $fields, $actions);
        }
    }
}
