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

class TapestryAccountsCLI extends TapestryCommandLine
{
	public function main($args)
	{
		$accounts = $this->model->accounts();
		foreach($accounts as $acct)
		{
			writeLn(sprintf('%s %-12s %s', $acct['uuid'], $acct['title'], $acct['account_id']));
		}
		return true;
	}
}
