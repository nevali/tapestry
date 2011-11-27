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

abstract class TapestryPage extends Page
{
	protected $modelClass = 'Tapestry';
	protected $defaultSkin = 'tapestry';
	protected $owner = null;
	protected $accounts = null;

	protected function getObject()
	{
		if(true !== ($r = parent::getObject()))
		{
			return $r;
		}
		$this->owner = $this->model->properties();
		$this->accounts = $this->model->accounts();
		return true;
	}

	protected function assignTemplate()
	{
		parent::assignTemplate();
		$this->vars['owner'] = $this->owner;
		$this->vars['accounts'] = $this->accounts;
	}
}
