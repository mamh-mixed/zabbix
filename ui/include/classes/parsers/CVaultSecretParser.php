<?php
/*
** Zabbix
** Copyright (C) 2001-2020 Zabbix SIA
**
** This program is free software; you can redistribute it and/or modify
** it under the terms of the GNU General Public License as published by
** the Free Software Foundation; either version 2 of the License, or
** (at your option) any later version.
**
** This program is distributed in the hope that it will be useful,
** but WITHOUT ANY WARRANTY; without even the implied warranty of
** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
** GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License
** along with this program; if not, write to the Free Software
** Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
**/


/**
 * Class is used to validate if given string is valid HashiCorp Vault secret.
 *
 * Valid token must match format:
 * - <namespace>/<path/to/secret>:<key>
 * - <namespace>/<secret>:<key>
 * - <path/to/secret>:<key>
 */
class CVaultSecretParser extends CParser {

	private $options = [
		'with_key' => true
	];

	/**
	 * @param array  $options
	 * @param bool   $options['with_key']  (optional) Validated string must contain key.
	 */
	public function __construct(array $options = []) {
		if (array_key_exists('with_key', $options)) {
			$this->options['with_key'] = $options['with_key'];
		}
	}

	/**
	 * @inheritDoc
	 */
	public function parse($source, $pos = 0) {
		$this->start = $pos;
		$this->errorClear();

		$namespace_sep = strpos($source, '/');
		if ($namespace_sep === false || $namespace_sep == 0) {
			$this->errorPos($source, 0);

			return self::PARSE_FAIL;
		}

		if ($this->options['with_key']) {
			$path_pos = $namespace_sep + 1;
			$key_sep = strpos($source, ':', $path_pos);

			if ($key_sep === false || $key_sep == $path_pos || $key_sep == strlen($source) - 1) {
				$this->errorPos($source, $path_pos);

				return self::PARSE_FAIL;
			}
		}

		return self::PARSE_SUCCESS;
	}
}
