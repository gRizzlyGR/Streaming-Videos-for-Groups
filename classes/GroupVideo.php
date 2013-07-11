<?php

class GroupVideo extends ElggFile {
	protected function  initializeAttributes() {
		parent::initializeAttributes();

		$this->attributes['subtype'] = "video";
	}

	public function __construct($guid = null) {
		parent::__construct($guid);
	}
	
	public function getMimeType() {
		return parent::getMimeType();
	}
	
	public function setMimeType($mimetype) {
		parent::setMimeType($mimetype);
	}
	
	public function setFilename($name) {
		parent::setFilename($name);
	}
}