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

if(!defined('TAPESTRY_DB')) define('TAPESTRY_DB', null);

class TapestryModule extends Module
{
	public $moduleId = 'net.nevali.tapestry';
	public $latestVersion = 5;

	public static function getInstance($args = null)
	{
		if(!isset($args['db'])) $args['db'] = TAPESTRY_DB;
		if(!isset($args['class'])) $args['class'] = 'TapestryModule';
		return parent::getInstance($args);
	}

	public function updateSchema($targetVersion)
	{
		if($targetVersion == 1)
		{
			$t = $this->tableWithOptions('service', DBTable::CREATE_ALWAYS);
			$t->columnWithSpec('uuid', DBType::UUID, null, DBCol::NOT_NULL, null, 'Unique service identifier');
			$t->columnWithSpec('name', DBType::VARCHAR, 32, DBCol::NOT_NULL, null, 'The name of the service');
			$t->columnWithSpec('title', DBType::VARCHAR, 128, DBCol::NOT_NULL, null, 'The title of the service');
			$t->columnWithSpec('homepage', DBType::VARCHAR, 255, DBCol::NOT_NULL, null, 'The service homepage');
			$t->columnWithSpec('icon', DBType::VARCHAR, 64, DBCol::NOT_NULL, null, 'The name of the service 16x16 icon file');
			$t->columnWithSpec('icon_type', DBType::VARCHAR, 64, DBCol::NOT_NULL, null, 'The MIME type of the icon file');
			$t->columnWithSpec('feed_pattern', DBType::TEXT, null, DBCol::NULLS, null, 'The feed URI pattern');
			$t->columnWithSpec('feed_type', DBType::VARCHAR, 64, DBCol::NULLS, null, 'The MIME type of the feed');
			$t->columnWithSpec('default_post_type', DBType::VARCHAR, 32, DBCol::NOT_NULL, null, 'The default post type for this service');
			$t->indexWithSpec(null, DBIndex::PRIMARY, 'uuid');
			$t->indexWithSpec('name', DBIndex::INDEX, 'name');
			return $t->apply();
		}
		if($targetVersion == 2)
		{
			$this->db->insert('service', array('uuid' => '704c4f7b-4596-4317-983b-0a5f60c44264', 'name' => 'tumblr', 'title' => 'Tumblr', 'homepage' => 'http://tumblr.com/', 'icon' => 'tumblr.gif', 'icon_type' => 'image/gif', 'feed_pattern' => '%{account_uri:raw}/rss', 'feed_type' => 'application/rss+xml', 'default_post_type' => 'blog'));
			$this->db->insert('service', array('uuid' => '514ed40c-27bf-48ca-b7fd-ce95eee82c57', 'name' => 'identica', 'title' => 'identi.ca', 'homepage' => 'http://identi.ca/', 'icon' => 'identica.ico', 'icon_type' => 'image/x-icon', 'feed_pattern' => 'http://identi.ca/api/statuses/user_timeline/%{account_id}.atom', 'feed_type' => 'application/atom+xml', 'default_post_type' => 'status'));
			$this->db->insert('service', array('uuid' => '03272efa-162b-4064-8135-ba06c8a5ba74', 'name' => 'flickr', 'title' => 'Flickr', 'homepage' => 'http://www.flickr.com/', 'icon' => 'flickr.ico', 'icon_type' => 'image/x-icon', 'feed_pattern' => 'http://api.flickr.com/services/feeds/photos_public.gne?id=%{account_id}&lang=en-us&format=atom', 'feed_type' => 'application/atom+xml', 'default_post_type' => 'photo'));
			$this->db->insert('service', array('uuid' => 'd59bd745-fae3-4932-8796-9b405b81cc02', 'name' => 'lastfm', 'title' => 'Last.fm', 'homepage' => 'http://www.last.fm/', 'icon' => 'lastfm.ico', 'icon_type' => 'image/x-icon', 'feed_pattern' => 'http://ws.audioscrobbler.com/1.0/user/%{account_id}/recenttracks.rss', 'feed_type' => 'application/rss+xml', 'default_post_type' => 'music'));
			$this->db->insert('service', array('uuid' => 'a987dc59-9140-453b-b4df-a475e503d659', 'name' => 'twitter', 'title' => 'Twitter', 'homepage' => 'http://twitter.com/', 'icon' => 'twitter.ico', 'icon_type' => 'image/x-icon', 'feed_pattern' => 'http://api.twitter.com/1/statuses/user_timeline.rss?screen_name=%{account_id}', 'feed_type' => 'application/rss+xml', 'default_post_type' => 'status'));
			$this->db->insert('service', array('uuid' => '7622023e-9d67-426a-bcce-8f6841ba5f77', 'name' => 'instapaper', 'title' => 'Instapaper', 'homepage' => 'http://instapaperr.com/', 'icon' => 'instapaper.png', 'icon_type' => 'image/png', 'feed_pattern' => 'http://www.instapaper.com/starred/rss/%{account_id:raw}', 'feed_type' => 'application/rss+xml', 'default_post_type' => 'link'));
			return true;
		}
		if($targetVersion == 3)
		{
			$t = $this->tableWithOptions('account', DBTable::CREATE_ALWAYS);
			$t->columnWithSpec('uuid', DBType::UUID, null, DBCol::NOT_NULL, null, 'Unique account identifier');
			$t->columnWithSpec('service', DBType::UUID, null, DBCol::NOT_NULL, null, 'Service identifier');
			$t->columnWithSpec('owner', DBType::UUID, null, DBCol::NOT_NULL, null, 'Owner identifier');
			$t->columnWithSpec('account_uri', DBType::VARCHAR, 255, DBCol::NOT_NULL, null, 'Account URI');
			$t->columnWithSpec('account_id', DBType::VARCHAR, 255, DBCol::NOT_NULL, null, 'Service-specific account identifier');
			$t->columnWithSpec('created', DBType::DATETIME, null, DBCol::NOT_NULL, null, 'When this account entry was created');
			$t->columnWithSpec('updated', DBType::DATETIME, null, DBCol::NULLS, null, 'When activity from this account was last refreshed');
			$t->columnWithSpec('feed_uri', DBType::VARCHAR, 255, DBCol::NOT_NULL, null, 'Feed URI');
			$t->columnWithSpec('default_post_type', DBType::VARCHAR, 32, DBCol::NOT_NULL, null, 'Default activity post type');
			$t->indexWithSpec(null, DBIndex::PRIMARY, 'uuid');
			$t->indexWithSpec('service', DBIndex::INDEX, 'service');
			$t->indexWithSpec('owner', DBIndex::INDEX, 'owner');
			return $t->apply();
		}
		if($targetVersion == 4)
		{
			$t = $this->tableWithOptions('activity', DBTable::CREATE_ALWAYS);
			$t->columnWithSpec('uuid', DBType::UUID, null, DBCol::NOT_NULL, null, 'Activity entry identifier');
			$t->columnWithSpec('owner', DBType::UUID, null, DBCol::NOT_NULL, null, 'Owner identifier');
			$t->columnWithSpec('account', DBType::UUID, null, DBCol::NOT_NULL, null, 'Account identifier');
			$t->columnWithSpec('guid', DBType::VARCHAR, 255, DBCol::NOT_NULL, null, 'Activity GUID URI');
			$t->columnWithSpec('permalink', DBType::VARCHAR, 255, DBCol::NOT_NULL, null, 'Activity permalink URI');
			$t->columnWithSpec('link', DBType::VARCHAR, 255, DBCol::NULLS, null, 'Activity link URI');
			$t->columnWithSpec('title', DBType::TEXT, null, DBCol::NULLS, null, 'Activity title');
			$t->columnWithSpec('summary', DBType::TEXT, null, DBCol::NULLS, null, 'Activity summary (if known)');
			$t->columnWithSpec('description', DBType::TEXT, null, DBCol::BIG|DBCol::NULLS, null, 'Activity description');
			$t->columnWithSpec('description_format', DBType::VARCHAR, 64, DBCol::NULLS, null, 'Type of activity description (text, html, xhtml, MIME type)');
			$t->columnWithSpec('seen', DBType::DATETIME, null, DBCol::NOT_NULL, null, 'When this entry was first seen');
			$t->columnWithSpec('updated', DBType::DATETIME, null, DBCol::NOT_NULL, null, 'When this entry was last updated');
			$t->columnWithSpec('postdate', DBType::DATETIME, null, DBCol::NULLS, null, 'Timestamp this entry carries');
			$t->columnWithSpec('image', DBType::VARCHAR, 255, DBCol::NULLS, null, 'Photo/image URL');
			$t->columnWithSpec('thumbnail', DBType::VARCHAR, 255, DBCol::NULLS, null, 'Photo/image thumbnail URL');
			$t->indexWithSpec(null, DBIndex::PRIMARY, 'uuid');
			$t->indexWithSpec('account', DBIndex::INDEX, 'account');
			$t->indexWithSpec('guid', DBIndex::INDEX, 'guid');
			$t->indexWithSpec('owner', DBIndex::INDEX, 'owner');
			return $t->apply();
		}
		if($targetVersion == 5)
		{
			$t = $this->tableWithOptions('owner', DBTable::CREATE_ALWAYS);
			$t->columnWithSpec('uuid', DBType::UUID, null, DBCol::NOT_NULL, null, 'UUID of the person');
			$t->columnWithSpec('property', DBType::VARCHAR, 64, DBCol::NOT_NULL, null, 'Property URI');
			$t->columnWithSpec('sequence', DBType::INT, null, DBCol::NOT_NULL, null, 'Property sequence number');
			$t->columnWithSpec('datatype', DBType::VARCHAR, 64, DBCol::NULLS, null, 'Property datatype URI');
			$t->columnWithSpec('language', DBType::VARCHAR, 16, DBCol::NULLS, null, 'Property language');
			$t->columnWithSpec('value', DBType::TEXT, null, DBCol::NULLS, null, 'Property content');
			$t->indexWithSpec('uuid', DBIndex::INDEX, 'uuid');
			$t->indexWithSpec('property', DBIndex::INDEX, 'property');
			$t->indexWithSpec('sequence', DBIndex::INDEX, 'sequence');
			return $t->apply();
		}
	}	
}
