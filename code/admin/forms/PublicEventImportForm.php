<?php

/**
 * PublicEventImportForm
 *
 * @author Anselm Christophersen <ac@anselm.dk>
 * @date   October 2015
 */
class PublicEventImportForm extends Form {

	public function __construct($controller, $name) {
		parent::__construct(
			$controller,
			$name,
			FieldList::create(
				FileField::create('CsvFile')
				//TODO implement properly at a later stage
				//CheckboxField::create('DryRun')
			),
			FieldList::create(
				FormAction::create('doUpload', 'Upload')
			),
			RequiredFields::create([
				'CsvFile'
			])
		);
	}

	public function doUpload($data, $form) {
		$loader = new EventCsvBulkLoader('PublicEvent');
		$fileName = $_FILES['CsvFile']['tmp_name'];

		//TODO implement properly at a later stage
		//if (isset($data['DryRun'])) {
		//	$results = $loader->preview($fileName);
		//	return;
		//}

		$results = $loader->load($fileName);
		$messages = array();
		if($results->CreatedCount()) $messages[] = sprintf('Imported %d items', $results->CreatedCount());
		if($results->UpdatedCount()) $messages[] = sprintf('Updated %d items', $results->UpdatedCount());
		if($results->DeletedCount()) $messages[] = sprintf('Deleted %d items', $results->DeletedCount());
		if(!$messages) $messages[] = 'No changes';
		$form->sessionMessage(implode(', ', $messages), 'good');

		return $this->controller->redirectBack();
	}
}