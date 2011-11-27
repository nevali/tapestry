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

class TapestrySetCLI extends TapestryCommandLine
{
	protected $minArgs = 2;
	protected $maxArgs = 2;
	protected $usage = 'tapestry set [OPTIONS] PROP-URI VALUE';
	protected $options = array(
		'owner' => array('has_arg' => true, 'description' => 'Specify the UUID of the property owner', 'flag' => null),
		'lang' => array('has_arg' => true, 'description' => 'Specify property language', 'flag' => null),
		'datatype' => array('has_arg' => true, 'description' => 'Specify property datatype', 'flag' => null),
		'add' => array('has_arg' => false, 'description' => 'Add a new value instead of replacing', 'flag' => false),
		'uri' => array('has_arg' => false, 'description' => 'The value is a URI'),
		);

	public function main($args)
	{
		RDF::ns();
		$x = explode(':', $args[0], 2);
		if(($k = array_search($x[0], RDF::$namespaces)) !== false)
		{
			$args[0] = RDF::fqname($k, $x[1]);
		}
		if(!empty($this->options['uri']['flag']))
		{
			$this->options['datatype']['flag'] = RDF::rdf.'resource';
		}
		$this->model->set($this->options['owner']['flag'], $args[0], $args[1],
						  $this->options['datatype']['flag'],
						  $this->options['lang']['flag'],
						  $this->options['add']['flag']);
	}
}
