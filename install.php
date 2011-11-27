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

class TapestryModuleInstall extends ModuleInstaller
{
	public function writeAppConfig($file)
	{
		fwrite($file, "define('MODULE_NAME', 'tapestry');\n");
		fwrite($file, "define('MODULE_CLASS_PATH', 'app.php');\n");
		fwrite($file, "define('MODULE_CLASS', 'TapestryApp');\n");
		fwrite($file, "define('CLI_MODULE_CLASS', 'DefaultApp');\n");
		fwrite($file, "\$SETUP_MODULES[] = 'tapestry';\n");
		fwrite($file, "\$CLI_ROUTES['tapestry'] = array('name' => 'tapestry', 'file' => 'cli.php', 'class' => 'TapestryCLI', 'description' => 'Tapestry commands', 'adjustBase' => true);\n");
	}
}
