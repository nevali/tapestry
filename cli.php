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

class TapestryCLI extends App
{
	public function __construct()
	{
		parent::__construct();
		$this->sapi['cli']['add-account'] = array('file' => 'cli-add-account.php', 'class' => 'TapestryAccountAddCLI', 'description' => 'Add a new account');
		$this->sapi['cli']['accounts'] = array('file' => 'cli-accounts.php', 'class' => 'TapestryAccountsCLI', 'description' => 'List active accounts');
		$this->sapi['cli']['set'] = array('file' => 'cli-set.php', 'class' => 'TapestrySetCLI', 'description' => 'Set properties');
		$this->sapi['cli']['update'] = array('file' => 'cli-update.php', 'class' => 'TapestryUpdateCLI', 'description' => 'Update activity from feeds');
	}
}

abstract class TapestryCommandLine extends CommandLine
{
	protected $modelClass = 'Tapestry';
}