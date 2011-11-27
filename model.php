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

uses('model', 'uuid', 'date', 'rdfstore');

if(!defined('TAPESTRY_DB')) define('TAPESTRY_DB', null);
if(!defined('TAPESTRY_OWNER')) define('TAPESTRY_OWNER', null);

class Tapestry extends Model
{
	protected $feedHandlers = array(
		'704c4f7b-4596-4317-983b-0a5f60c44264' => array('file' => 'tumblr.php', 'class' => 'TapestryTumblrHandler'),
		);
	/* Obtain an instance of the model */
	public static function getInstance($args = null)
	{
		if(!isset($args['db'])) $args['db'] = TAPESTRY_DB;
		if(!isset($args['class'])) $args['class'] = 'Tapestry';
		return parent::getInstance($args);
	}

	public function __construct($args = null)
	{
		parent::__construct($args);
		$this->owner = UUID::formatted(TAPESTRY_OWNER);
		RDF::ns('http://ogp.me/ns#', 'og');
		RDF::ns('http://ogp.me/ns/fb#', 'fb');
		RDF::ns('http://graph.facebook.com/schema/user#', 'user');
	}

	/* Return the list of available services */
	public function services()
	{
		return $this->db->rows('SELECT * FROM {service}');
	}

	/* Return the information about a service */
	public function service($uuid)
	{
		return $this->db->row('SELECT * FROM {service} WHERE "uuid" = ?', $uuid);
	}

	/* Return the information about a service, given its name */
	public function serviceByName($name)
	{
		return $this->db->row('SELECT * FROM {service} WHERE "name" = ?', $name);
	}

	/* Return the information about an account */
	public function account($uuid)
	{
		return $this->db->row('SELECT * FROM {account} WHERE "uuid" = ?', $uuid);
	}

	/* Create a new account entry and return its UUID. Returns NULL on error. */
	public function addAccount($info)
	{
		if(null == ($uuid = UUID::formatted(@$info['owner'])))
		{
			if(strlen(@$info['owner']))
			{
				trigger_error('Cannot add new account because the owner is not a UUID', E_USER_ERROR);
				return null;
			}
			if(!strlen($this->owner))
			{
				trigger_error('Cannot add new account because the owner has not been specified and no default owner has been configured', E_USER_ERROR);
				return null;
			}			
			$info['owner'] = $this->owner;
		}
		else
		{
			$info['owner'] = $uuid;
		}
		if(null == ($uuid = UUID::formatted(@$info['service'])))
		{
			trigger_error('Cannot add new account because the service identifier is not a UUID', E_USER_ERROR);
			return null;
		}
		else
		{
			$info['service'] = $uuid;
		}
		$service = $this->service($info['service']);
		if(!is_array($service))
		{
			trigger_error('Cannot add new account because the service cannot be located', E_USER_ERROR);
			return null;
		}
		if(!strlen(@$info['account_uri']))
		{
			trigger_error('Cannot add new account because the account URI is not set', E_USER_ERROR);
			return null;
		}
		if(!strlen(@$info['account_id']))
		{
			trigger_error('Cannot add new account because the account ID is not set', E_USER_ERROR);
			return null;
		}
		$search = array(
			'%{account_uri}',
			'%{account_id}',
			'%{account_uri:raw}',
			'%{account_id:raw}',
			);
		$replace = array(
			urlencode($info['account_uri']),
			urlencode($info['account_id']),
			$info['account_uri'],
			$info['account_id'],
			);
		if(!strlen(@$info['feed_uri']))
		{
			if(!strlen($service['feed_pattern']))
			{
				trigger_error('Cannot add new account because the feed URI cannot be constructed', E_USER_ERROR);
				return null;
			}
			$info['feed_uri'] = str_replace($search, $replace, $service['feed_pattern']);
		}
		if(!strlen(@$info['default_post_type']))
		{
			$info['default_post_type'] = $service['default_post_type'];
		}
		$info['uuid'] = UUID::generate();
		unset($info['created']);
		unset($info['updated']);
		$info['@created'] = $this->db->now();
		$this->db->insert('account', $info);
		return $info['uuid'];
	}

	/* Return the activity feed for an account */
	public function activity($owner = null, $limit = 100, $after = null)
	{
		if(!strlen($owner))
		{
			if(!strlen($this->owner))
			{
				return array('error' => 'No owner specified');
			}
			$owner = $this->owner;
		}
		$owner = UUID::formatted($owner);
		$results = array(
			'owner' => $owner,
			'limit' => $limit,
			);
		$results['activity'] = $this->db->rows('SELECT "ac".*, "s".*, "a".*, "s"."title" AS "service_title" FROM {activity} "a", {account} "ac", {service} "s" WHERE "a"."account" = "ac"."uuid" AND "s"."uuid" = "ac"."service" AND "a"."owner" = ? ORDER BY "a"."postdate" DESC', $owner);
		return $results;
	}
	
	/* Return a specific activity item for an account given its GUID */
	public function activityItemFromAccountGuid($account, $guid)
	{
		return $this->db->row('SELECT "ac".*, "s".*, "a".*, "ac"."uuid", "s"."title" AS "service_title" FROM {activity} "a", {account} "ac", {service} "s" WHERE "a"."account" = "ac"."uuid" AND "s"."uuid" = "ac"."service" AND "a"."account" = ? AND "a"."guid" = ?', $account, $guid);
	}

	/* Add a new activity item */
	public function addActivityItem($item)
	{
		if(!strlen(@$item['account']))
		{
			trigger_error('Cannot add activity item because it has no account', E_USER_ERROR);
			return null;
		}
		if(!strlen(@$item['owner']))
		{
			trigger_error('Cannot add activity item because it has no owner', E_USER_ERROR);
			return null;
		}
		$item['@seen'] = $this->db->now();		
		$item['@updated'] = $this->db->now();
		unset($item['seen']);
		unset($item['updated']);
		$item['uuid'] = UUID::generate();
		$this->db->insert('activity', $item);
		return $item['uuid'];
	}
	
	/* Update an activity item (both $storedItem and $item must be arrays
	 * containing the item data).
	 */
	public function updateActivityItem($storedItem, $item)
	{
		$item['@updated'] = $this->db->now();
		unset($item['account']);
		unset($item['owner']);
		unset($item['seen']);
		unset($item['updated']);
		$this->db->update('activity', $item, array('guid' => $storedItem['guid']));
		return $storedItem['uuid'];
	}

	/* Locate the feed-handling class, if any, for a particular account */
	public function feedHandler($account)
	{
		if(!isset($this->feedHandlers[$account['service']]))
		{
			$i = $this->feedHandlers[$account['service']] = new TapestryFeedHandler();
			$i->model = $this;
			$i->account = $account;
			return $i;
		}
		$info = $this->feedHandlers[$account['service']];
		if(!isset($info['instance']))
		{
			$class = $info['class'];
			Loader::load($info);
			$info['instance'] = new $class();
			$this->feedHandlers[$account['service']] = $info;
			$info['instance']->model = $this;
			$info['instance']->account = $account;
		}
		return $info['instance'];
	}

	/* Update the activity feed for a given account, optionally overriding the
	 * feed URI.
	 */
	public function updateAccountFeed($account, $feedUri = null)
	{		
		$cacheFile = INSTANCE_ROOT . 'incoming/' . $account['owner'] . '/' . $account['uuid'];
		if(!file_exists($cacheFile))
		{
			mkdir($cacheFile, 0777, true);
		}
		if(!strlen($feedUri))
		{
			$feedUri = $account['feed_uri'];
		}
		$cacheFile .= '/' . md5($feedUri);
		$items = $this->parseFeedFromURL($feedUri, $cacheFile);
		if(!is_array($items))
		{
			return false;
		}
		$handler = $this->feedHandler($account);
		foreach($items as $item)
		{
			if(!isset($item['guid']))
			{
				if(isset($item['permalink']))
				{
					$item['guid'] = $item['permalink'];
				}
				else if(isset($item['link']))
				{
					$item['guid'] = $item['link'];
				}
			}
			$item['account'] = $account['uuid'];
			$item['owner'] = $account['owner'];
			$storedItem = $this->activityItemFromAccountGuid($account['uuid'], $item['guid']);
			if($handler !== null)
			{
				$handler->processActivityItem($item);
			}
			if(is_array($storedItem) && isset($storedItem['uuid']))
			{				
				$this->updateActivityItem($storedItem, $item);
			}
			else
			{
				$this->addActivityItem($item);
			}
		}
		return $items;
	}

	/* Given a URL, parse a feed and return the items it contains */
	public function parseFeedFromURL($url, $cacheFile = null)
	{
		if(!strncmp($url, 'http:', 5) || !strncmp($url, 'https:', 5))
		{
			$cc = new CurlCache($url);
			$cc->cacheFile = $cacheFile;
			$cc->followLocation = true;
			$cc->returnTransfer = true;
			$buf = $cc->exec();
		}
		else
		{
			$buf = file_get_contents($url);
		}
		if($buf === null || $buf === false)
		{
			return null;
		}
		$root = simplexml_load_string($buf);
		return $this->parseFeed($root);
	}

	/* Given a SimpleXML document node, parse the feed entries it contains and
	 * return an array of items.
	 */
	protected function parseFeed($root)
	{
		$elem = $root->getName();
		$attrs = $root->attributes();
		if(!strcmp($elem, 'rss') && (!strcmp($attrs->version, '2.0') || !strlen($attrs->version)))
		{
			return $this->parseRSS2($root);
		}
		if(!strcmp($elem, 'feed'))
		{
			return $this->parseAtom($root);
		}
		trigger_error('Unknown feed format', E_USER_NOTICE);
	}

	/* Given a SimpleXML document node, parse it as an Atom feed and return
	 * an array containing the entries.
	 */
	protected function parseAtom($root)
	{
		$items = array();
		if(!isset($root->entry))
		{
			return array();
		}
		foreach($root->entry as $item)		   
		{
			$entry = array();
			$entry['title'] = strval($item->title);
			$entry['guid'] = strval($item->id);
			if(isset($item->published))
			{
				$entry['postdate'] = new EregansuDateTime($item->published);
			}
			else if(isset($item->updated))
			{
				$entry['postdate'] = new EregansuDateTime($item->updated);
			}
			if(isset($item->content))
			{
				$entry['description'] = strval($item->content);
				$cattrs = $item->content->attributes();
				$entry['description_format'] = $cattrs->type;
				if(!strlen($entry['description_format']))
				{
					$entry['description_format'] = 'text';
				}
			}
			$items[] = $entry;
		}
		return $items;
	}

	/* Given a SimpleXML document node, parse it as an RSS2 feed and return
	 * an array containing the entries.
	 */
	protected function parseRSS2($root)
	{
		$items = array();
		if(!isset($root->channel))
		{
			return array();
		}
		$channel = $root->channel;
		if(!isset($root->channel->item))
		{
			return array();
		}
		foreach($channel->item as $item)		   
		{
			$entry = array();
			$entry['title'] = strval($item->title);
			$entry['description'] = strval($item->description);
			$entry['description_format'] = 'html';
			if(isset($item->guid))
			{
				$entry['guid'] = strval($item->guid);
				$gattrs = $item->guid->attributes;
				if(isset($gattrs['isPermaLink']))
				{
					$entry['permalink'] = strval($item->guid);
				}
			}
			if(isset($item->link))
			{
				$entry['link'] = strval($item->link);
			}
			$entry['postdate'] = new EregansuDateTime($item->pubDate);			
			$items[] = $entry;
		}
		return $items;
	}

	/* Set a property on a user */
	public function set($owner, $property, $value, $datatype = null, $lang = null, $add = true)
	{
		if(null == ($uuid = UUID::formatted($owner)))
		{
			if(strlen($owner))
			{
				trigger_error('Cannot set property because the owner is not a UUID', E_USER_ERROR);
				return null;
			}
			if(!strlen($this->owner))
			{
				trigger_error('Cannot set property because the owner has not been specified and no default owner has been configured', E_USER_ERROR);
				return null;
			}			
			$owner = $this->owner;
		}
		else
		{
			$owner = $uuid;
		}
		if(!$add)
		{
			$this->db->exec('DELETE FROM {owner} WHERE "uuid" = ? AND "property" = ?', $owner, $property);
			$index = 0;
		}
		else
		{
			$index = $this->db->value('SELECT MAX("sequence") FROM {owner} WHERE "uuid" = ? AND "property" = ?', $owner, $property);
			if($index === false || $index === null)
			{
				$index = 0;
			}
			else
			{
				$index = intval($index) + 1;
			}
		}
		if(!strlen($datatype))
		{
			$datatype = null;
		}
		if(!strlen($lang))
		{
			$lang = null;
		}
		$this->db->insert('owner', array(
							  'uuid' => $owner,
							  'property' => $property,
							  'value' => $value,
							  'sequence' => $index,
							  'datatype' => $datatype,
							  'language' => $lang));
		return $index;
	}

	/* Return the flat list of properties associated with a user */
	public function propertyList($owner = null)
	{
		if(null == ($uuid = UUID::formatted($owner)))
		{
			if(strlen($owner))
			{
				return null;
			}
			if(!strlen($this->owner))
			{
				return null;
			}			
			$owner = $this->owner;
		}
		else
		{
			$owner = $uuid;
		}
		return $this->db->rows('SELECT * FROM {owner} WHERE "uuid" = ? ORDER BY "property", "sequence"', $owner);
	}

	/* Return the list of properties associated with a user, arranged as
	 * an RDF/JSON array.
	 */
	public function propertyArray($owner = null)
	{
		$array = array();
		if(null === ($list = $this->propertyList($owner)))
		{
			return null;
		}
		foreach($list as $prop)
		{
			if($prop['datatype'] == RDF::rdf.'resource')
			{
				$value = array('type' => 'uri', 'value' => $prop['value']);
			}
			else if(isset($prop['datatype']))
			{
				$value = array('type' => 'literal', 'datatype' => $prop['datatype']);
			}
			else if(isset($prop['language']))
			{
				$value = array('type' => 'literal', 'datatype' => $prop['language']);
			}
			else
			{
				$value = $prop['value'];
			}
			$array[$prop['property']][] = $value;
		}
		return $array;
	}

	/* Return the properties associated with a user as an RDFInstance */
	public function properties($owner = null)
	{
		if(null === ($array = $this->propertyArray($owner)))
		{
			return null;
		}
		return RDFStoredObject::objectForData($array);
	}
	
	/* Return all of the accounts belonging to a user */
	public function accounts($owner = null)
	{
		if(null == ($uuid = UUID::formatted($owner)))
		{
			if(strlen($owner))
			{
				return array();
			}
			if(!strlen($this->owner))
			{
				return array();
			}			
			$owner = $this->owner;
		}
		else
		{
			$owner = $uuid;
		}
		return $this->db->rows('SELECT "s".*, "a".* FROM {account} "a", {service} "s" WHERE "a"."owner" = ? AND "s"."uuid" = "a"."service"', $owner);
	}	
}

/* Base class for feed handlers */
abstract class TapestryFeedHandler
{
	public $account;
	public $model;

	public function processActivityItem(&$item)
	{
		if(!strlen(@$item['permalink']) &&
		   strlen(@$item['guid']) &&
		   (!strncmp($item['guid'], 'http:', 5) ||
			!strncmp($item['guid'], 'https:', 6)))
		{
			$item['permalink'] = $item['guid'];
		}
		if(!strlen(@$item['link']) && strlen(@$item['permalink']))
		{
			$item['link'] = $item['permalink'];
		}
	}
}

