<?php

namespace Core;

# directory
define("DL", "\\");
define('__STATIC__', 'app'.DL.'static'.DL);
define('__TEMPLATE__', 'app'.DL.'templates'.DL);
define('__APP__', 'app'.DL.'PHP'.DL);
# render html tag
define('XPATH_NODE', '//*[@*[contains(name(), "bk:")]]');
define('XPATH_SWITCH', '//*[@*[contains(name(), "bk:switch")]]');
define('XPATH_BLOCK', '//*[local-name()="block"]');
define('BK_TAG', [
	'text'=>'bk:text',
	'case'=>'bk:case',
	'switch'=>'bk:switch',
	'foreach'=>'bk:foreach',
	'action'=>'bk:action'
]);
# repository keyword
define ('FIND_BY', [
  'and'=> ['and', ' ='],
  'is'=> ['and', ' ='],
  'equal'=> ['and', ' ='],
  'or' => ['or', ' ='],
  'lessthan' => ['or', ' <'],
  'lessthanequal' => ['or', ' <='],
  'greaterthan' => ['or', ' >'],
  'greaterthanequal' => ['or', ' >='],
  'like' => ['or', ' like'],
  'notlike' => ['or', ' not like'],
]);

?>