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

uses('date');

require_once(dirname(__FILE__) . '/page.php');

class TapestryActivity extends TapestryPage
{
	protected $supportedTypes = array('text/html' => array('q' => 1.0), 'application/atom+xml');
	protected $templateName = 'activity.phtml';

	protected function getObject()
	{
		if(true !== ($r = parent::getObject()))
		{
			return $r;
		}
		$this->objects = $this->model->activity();
		return true;
	}

	protected function perform_GET_Atom($type = 'application/atom+xml')
	{
		global $MODULE_NAME;

		$now = new EregansuDateTime();
		writeLn('<?xml version="1.0" encoding="UTF-8"?>');
		writeLn('<feed xmlns="http://www.w3.org/2005/Atom" xmlns:t="http://tapestry.nevali.net/ns/">');
		writeLn('<title>Activity</title>');
		writeLn('<link href="' . _e($this->request->httpBase) . '" />');
		writeLn('<id>urn:uuid:' . $this->objects['owner'] . '</id>');
		foreach($this->objects['activity'] as $post)
		{
			if(!strlen($post['type']))
			{
				$post['type'] = $post['default_post_type'];
			}			
			if(!strlen($post['type']))
			{
				$post['type'] = 'blog';
			}
			$skin = defined('DEFAULT_SKIN') ? DEFAULT_SKIN : $this->defaultSkin;
			$templatesPath = $this->request->httpBase . TEMPLATES_PATH . '/' . $skin . '/';
			$updated = new EregansuDateTime($post['updated']);
			$postdate = new EregansuDateTime($post['postdate']);
			writeLn('<entry>');
			writeLn('<id>urn:uuid:' . $post['uuid'] . '</id>');
			writeLn('<title>' . _e($post['title']) . '</title>');
			writeLn('<published>' . $postdate . '</published>');
			writeLn('<updated>' . $updated . '</updated>');
			if(strlen($post['link']))
			{
				writeLn('<link href="' . _e($post['link']) . '" />');
			}
			writeLn('<link rel="shortcut icon" type="' . _e($post['icon_type']) . '" href="' . _e($templatesPath . 'services/' . $post['icon']) . '" />');
			if(strlen($post['description']))
			{
				writeLn('<content type="' . _e($post['description_format']) . '">');
				writeLn(_e($post['description']));
				writeLn('</content>');
			}
			writeLn('<t:kind>' . _e($post['type']) . '</t:kind>');
			writeLn('<t:source>');
			writeLn('<t:title>' . _e($post['service_title']) . '</t:title>');
			writeLn('<t:homepage>' . _e($post['homepage']) . '</t:homepage>');
			writeLn('<t:account>' . _e($post['account_uri']) . '</t:account>');
			writeLn('<t:feed>' . _e($post['feed_uri']) . '</t:feed>');
			if(strlen($post['permalink']))
			{
				writeLn('<t:permalink>' . _e($post['permalink']) . '</t:permalink>');
			}
			writeLn('<t:guid>' . _e($post['guid']) . '</t:guid>');
			writeLn('</t:source>');
			writeLn('</entry>');
		}
		writeLn('</feed>');
	}
}