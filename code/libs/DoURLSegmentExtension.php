<?php
/**
 * DataObject URL Segment Extension
 * inspired by https://github.com/dospuntocero/doarticles/blob/master/code/utils/DOArticleURLSegmentDecorator.php
 */
class DoURLSegmentExtension extends DataExtension {

	static $db = array(
		'URLSegment' => 'Varchar(255)'
	);

	function onBeforeWrite() {
		$this->owner->URLSegment = singleton('SiteTree')->generateURLSegment($this->owner->Title);
	}
	
	
	function getLink(){
		$link = $this->owner->URLSegment;
		if (!$link) {
			//if no link has been generated, auto generate it
			$this->owner->write();
			$link = $this->owner->URLSegment;
		}
		return $link;
	}
	
//	function getName(){
//		$name = $this->owner->Title;
//		return $name;
//	}
	
}