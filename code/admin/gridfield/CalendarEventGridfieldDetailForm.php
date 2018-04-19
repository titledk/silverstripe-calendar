<?php

use SilverStripe\Forms\GridField\GridFieldDetailForm;
use SilverStripe\View\Requirements;
use SilverStripe\Forms\Form;
use SilverStripe\Control\Controller;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\GridField\GridFieldDetailForm_ItemRequest;
/**
 * CalendarEvent Gridfield DetailForm
 * Add additional features to the gridfield detail form:
 * 1. The classname 'CalendarEventGridfieldDetailForm' to be able to hook up css and js to the form elements
 * 2. Adding js/css requirements
 * 3. "Add New" button
 *
 * Draws on, and inspired by
 * https://github.com/webbuilders-group/GridFieldDetailFormAddNew/blob/master/gridfield/GridFieldDetailFormAddNew.php
 *
 * @package calendar
 * @subpackage admin
 */
class CalendarEventGridFieldDetailForm extends GridFieldDetailForm
{
}

/**
 * extension to the @see GridFieldDetailForm_ItemRequest
 */
class CalendarEventGridFieldDetailForm_ItemRequest extends GridFieldDetailForm_ItemRequest
{

    private static $allowed_actions = array(
        'edit',
        'view',
        'ItemEditForm'
    );

    /**
     * @return {Form}
     */
    public function ItemEditForm()
    {

        //Timepicker
        Requirements::css('calendar/thirdparty/timepicker/jquery.timepicker.css');
        //Requirements::javascript('calendar/thirdparty/timepicker/jquery.timepicker.js');
        //modification to allow timepicker and timeentry to work in tandem:
        Requirements::javascript('calendar/thirdparty/timepicker/jquery.timepicker-timeentry.js');

        //Timeentry
        Requirements::javascript('calendar/thirdparty/timeentry/jquery.timeentry.js');


        //CSS/JS Dependencies
        Requirements::css("calendar/css/admin/CalendarEventGridFieldDetailForm.css");
        Requirements::javascript("calendar/javascript/events/EventFields.js");
        Requirements::javascript("calendar/javascript/admin/CalendarEventGridFieldDetailForm.js");


        $form = parent::ItemEditForm();
        if (!$form instanceof Form) {
            return $form;
        }

        $form->addExtraClass('CalendarEventGridfieldDetailForm');

        if ($this->record->ID !== 0) {
            $actionFields=$form->Actions();
            $link = Controller::join_links($this->gridField->Link('item'), 'new');

            $actionFields->push(
                new LiteralField(
                    'addNew',
                    '<a href="' .$link. '" class="action action-detail ss-ui-action-constructive ' .
                    'ss-ui-button ui-button ui-widget ui-state-default ui-corner-all new new-link" data-icon="add">Add new '.
                    $this->record->i18n_singular_name(). '</a>')
            );
        }

        return $form;
    }
}
