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

require_once(dirname(__FILE__) . '/model.php');

class TapestryApp extends Proxy
{
	protected $sendNegotiateHeaders = false;

	protected $supportedTypes = array(
		'text/html' => array('q' => '1.0'),
		'application/rdf+xml' => array('q' => '0.9'),
		'application/xrd+xml' => array('q' => '0.9'),
		'application/atom+xml' => array('q' => '0.9'),
		'text/turtle' => array('q' => '0.9'),
		);
	
	public function __construct()
	{
		$this->sapi['http']['profile'] = array('file' => 'profile.php', 'class' => 'TapestryProfile', 'title' => 'Profile', 'page_type' => 'profile');
		$this->sapi['http']['activity'] = array('file' => 'activity.php', 'class' => 'TapestryActivity', 'title' => 'Activity', 'page_type' => 'activity');
		$this->sapi['http']['.well-known'] = array('file' => 'well-known.php', 'class' => 'TapestryWellKnown');
		return parent::__construct();
	}
	
	protected function getObject()
	{
		$obj = $this->request->consume();
		if(!strcmp($obj, 'index') || !strlen($obj))
		{
			return true;
		}
		return $this->error(Error::OBJECT_NOT_FOUND);
	}

	protected function perform_GET_HTML($type = 'text/html')
	{		
		$this->request->redirect($this->request->base . 'activity' . $this->request->explicitSuffix, 303);
	}

	protected function perform_GET_RDF($type = 'application/rdf+xml')
	{		
		$this->request->redirect($this->request->base . 'profile' . $this->request->explicitSuffix, 303);
	}

	protected function perform_GET_Turtle($type = 'text/turtle')
	{		
		$this->request->redirect($this->request->base . 'profile' . $this->request->explicitSuffix, 303);
	}

	protected function perform_GET_XRD($type = 'application/xrd+xml')
	{		
		$this->request->redirect($this->request->base . 'profile' . $this->request->explicitSuffix, 303);
	}

}
