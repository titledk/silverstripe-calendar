<?php
/**
 * CalendarEvent Page Gridfield DetailForm
 * @package calendar
 * @subpackage admin
 */
class CalendarEventPageGridFieldDetailForm extends CalendarEventGridFieldDetailForm {

}

/**
 * extension to the @see CalendarEventGridFieldDetailForm_ItemRequest
 */
class CalendarEventPageGridFieldDetailForm_ItemRequest extends CalendarEventGridFieldDetailForm_ItemRequest {

	private static $allowed_actions = array(
		'edit',
		'view',
		'ItemEditForm'
	);


	/**
	 * @return {Form}
	 */
	public function ItemEditForm() {

		$form = parent::ItemEditForm();

		if ($form) {
			$fields = $form->Fields();
			$fields->removeByName('EventPage');
		}

		return $form;
	}

}
