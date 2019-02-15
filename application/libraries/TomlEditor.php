<?php
	/*
	 *
	 * Created by 'Wright.' for tsforum.pl.
	 *
	 * Contact:
	 *   > Teamspeak: ts3.black
	 *   > Mail: wright@ogarnij.se
	 *   > Github: WrightProjects
	 *
	 * Copyright (C) 2019 WrightProjects
	 *
	 */

	use Yosymfony\Toml\Toml;
	use Yosymfony\Toml\TomlBuilder;

	class TomlEditor
	{
		protected $CI;
		protected $path;

		public function __construct()
		{
			$this->CI   =& get_instance();
			$this->path = $this->CI->config->item('ts3ab')['path'].'rights.toml';
		}

		public function removeTabele($bot)
		{
			$array = Toml::ParseFile($this->path);
			foreach ($array as $rule => $value) {
				foreach ($value as $index => $item) {
					if (isset($item['bot']) && $item['bot'] == $bot) {
						unset($array['rule'][$index]);
						$this->saveFile($array);
						break;
					}
				}
			}
		}

		public function createTabele($bot, $group, $permissions)
		{
			$array = Toml::ParseFile($this->path);
			foreach ($array as $rule => $value) {
				foreach ($value as $item) {
					if (isset($item['bot'])) {
						array_push($array['rule'], array('bot' => $bot, 'rule' => array(array('groupid' => (int)$group, '+' => $permissions))));
						$this->saveFile($array);
						break;
					}
				}
			}
		}

		public function editTabele($bot, $group)
		{
			$array = Toml::ParseFile($this->path);
			foreach ($array as $rule => $value) {
				foreach ($value as $index => $item) {
					if (isset($item['bot']) && $item['bot'] == $bot) {
						$array['rule'][$index]['rule'][0]['groupid'] = $group;
						$this->saveFile($array);
						break;
					}
				}
			}
		}

		public function editApiTabele($useruid, $apitoken)
		{
			$array = Toml::ParseFile($this->path);
			foreach ($array as $rule => $value) {
				foreach ($value as $index => $item) {
					if (isset($item['apitoken'])) {
						$array['rule'][$index]['useruid']  = $useruid;
						$array['rule'][$index]['apitoken'] = $apitoken;
						$this->saveFile($array);
						break;
					}
				}
			}
		}

		private function saveFile($array)
		{
			$tb = new TomlBuilder();
			$tb->addComment('DO NOT EDIT THIS FILE!');
			$tb->addComment('DO NOT EDIT THIS FILE!');
			$tb->addComment('DO NOT EDIT THIS FILE!');
			foreach ($array as $xd => $values) {
				foreach ($values as $index => $items) {
					$tb->addComment("Rule ".($index + 1), true);
					$tb->addArrayOfTable('rule');
					foreach ($items as $item => $value) {
						if (is_array($value)) {
							foreach ($value as $val) {
								if (is_array($val)) {
									$tb->addArrayOfTable('rule.rule');
									foreach ($val as $i => $v) {
										$tb->addValue($i, $v);
									}
								}
								else {
									$tb->addValue($item, $value);
								}
							}
						}
						else {
							$tb->addValue($item, $value);
						}
					}
				}
			}
			file_put_contents($this->path, $tb->getTomlString());
		}
	}
