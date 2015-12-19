<?php
/**
 * Event Registration Controller
 *
 * @package calendar
 * @subpackage registrations
 */
class EventRegistrationController extends Controller {

	private static $allowed_actions = array(
		'registerform',
		'paymentregisterform'
	);


	public function init() {
		parent::init();
	}


	public function registerform() {
		$form = EventRegistrationForm::create(
			$this,
			'registerform'
		);

		if (isset($_GET['complete'])) {
			$form->setDone();
		}

		return $form;
	}

	public function paymentregisterform() {
		$form = PaymentRegistrationForm::create(
			$this,
			'paymentregisterform'
		);

		if (isset($_GET['complete'])) {
			$form->setDone();
		}
		return $form;
	}


	/**
	 * AJAX Json Response handler
	 *
	 * @param array|null $retVars
	 * @param boolean $success
	 * @return \SS_HTTPResponse
	 */
	public function handleJsonResponse($success = false, $retVars = null) {
		$result = array();
		if ($success) {
			$result = array(
				'success' => $success
			);
		}
		if ($retVars) {
			$result = array_merge($retVars, $result);
		}

		$response = new SS_HTTPResponse(json_encode($result));
		$response->addHeader('Content-Type', 'application/json');
		return $response;
	}


}