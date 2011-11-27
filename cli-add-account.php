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

class TapestryAccountAddCLI extends TapestryCommandLine
{
	protected $info = array();
	protected $minArgs = 1;
	protected $maxArgs = 2;
	protected $usage = 'tapestry add-account OPTIONS ACCOUNT-URI [ACCOUNT-ID]';
	protected $options = array(
		'service' => array('description' => 'Service to create an account for', 'has_arg' => true, 'flag' => null),
		'owner' => array('description' => 'Owner the account should be associated with if not the default', 'has_arg' => true, 'flag' => null),
		'feed' => array('description' => 'Override the default feed URI', 'has_arg' => true, 'flag' => null),
		);

	public function main($args)
	{
		if(!isset($this->options['service']['flag']))
		{
			$this->err('A service name or UUID must be specified (--service=NAME)');
			return false;
		}
		if(null == UUID::isUUID($this->options['service']['flag']))
		{
			$service = $this->model->serviceByName($this->options['service']['flag']);
			if(!is_array($service))
			{
				$this->err('Failed to locate a service named "' . $this->options['service']['flag'] . '"');
				return false;
			}
			$this->options['service']['flag'] = $service['uuid'];
		}
		$this->info['service'] = $this->options['service']['flag'];
		$this->info['owner'] = $this->options['owner']['flag'];
		$this->info['feed_uri'] = $this->options['feed']['flag'];
		$this->info['account_uri'] = $args[0];
		$this->info['account_id'] = @$args[1];
		if(($uuid = $this->model->addAccount($this->info)) !== null)
		{
			writeLn('New account added with UUID', $uuid);
			return true;
		}
	}
}
