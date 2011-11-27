<?php

/*
 * Copyright 2011 Mo McRoberts.
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

require_once(dirname(__FILE__) . '/page.php');

class TapestryProfile extends TapestryPage
{
	protected $templateName = 'profile.phtml';
	protected $supportedTypes = array(
		'text/html' => array('q' => 1.0),
		'application/rdf+xml' => array('q' => 0.9),
		'text/turtle' => array('q' => 0.9),
		'application/xrd+xml' => array('q' => 0.9),
		);
	
	protected function getObject()
	{
		if(true != ($r = parent::getObject()))
		{
			return $r;
		}
		$stem = substr($this->request->resource, 1);
		$this->owner['rdf:about'] = new RDFURI($this->request->absoluteBase . '#me');
		$this->owner['user:link'] = new RDFURI($this->request->absoluteBase . $stem);
		$this->object = new RDFDocument();
		$this->object->add($this->owner);
		return true;
	}

	protected function perform_GET_RDF($type = 'application/rdf+xml')
	{
		$stem = substr($this->request->resource, 1);
		$doc = $this->object->subject($this->request->absoluteBase . $stem . MIME::extForType($type));
		$doc['rdf:type'] = new RDFURI(RDF::foaf.'PersonalProfileDocument');
		$doc['foaf:primaryTopic'] = $this->owner->subject();
		parent::perform_GET_RDF($type);
	}


	protected function perform_GET_Turtle($type = 'text/turtle')
	{
		$stem = substr($this->request->resource, 1);
		$doc = $this->object->subject($this->request->absoluteBase . $stem . MIME::extForType($type));
		$doc['rdf:type'] = new RDFURI(RDF::foaf.'PersonalProfileDocument');
		$doc['foaf:primaryTopic'] = $this->owner->subject();
		parent::perform_GET_RDF($type);
	}
}
