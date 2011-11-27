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

class TapestryUpdateCLI extends TapestryCommandLine
{
	protected $minArgs = 1;
	protected $maxArgs = 2;
	protected $usage = 'tapestry update ACCOUNT [FEED-URI]';

	public function main($args)
	{
		$account = $this->model->account($args[0]);
		if(!is_array($account))
		{
			$this->err('Account "' . $args[0] . '" does not exist');
			return false;
		}
		if(!strlen(@$args[1]))
		{
			$args[1] = $account['feed_uri'];
		}
		writeLn('Updating activity from ' . $args[1]);
		$this->model->updateAccountFeed($account, $args[1]);
	}
}