<?php

namespace Core;

use Core\Model;
use DOMDocument;
use DOMXpath;

define('XPATH_NODE', '//*[@*[contains(name(), "bk:")]]');
define('XPATH_ATTR', '//@*[starts-with(name(), "bk:")]');
define('XPATH_BLOCK', '//*[local-name()="block"]');
define('XPATH_CHILD', '[@*[contains(name(), "bk:")]]');
define('BK_TAG', [
	'text'=>'bk:text',
	'foreach'=>'bk:foreach'
]);

trait ControllerTrait
{
	public Model $model;

	public function __construct()
	{
		$this->model = new Model();
	}
	
	private function render_block($elements, $model, $name)
	{
		foreach($elements as $element)
		{
			# traverse through bk attributes
			foreach(array_values(array_filter(
				iterator_to_array($element->attributes), function($attr) {	
					return preg_match_all('/^bk:.*$/', $attr->name);
			})) as $attr)
			{
				if ($attr->name == BK_TAG['text'])
					$element->nodeValue = $this->model->get_attribute(
						$attr->value, [$name=>$model]);
				else
					$element->setAttribute(str_replace('bk:','',$attr->name),								$this->model->get_attribute(
							$attr->value, [$name=>$model]));
				$element->removeAttribute($attr->name);
			}
		}
	}

	private function edit_node($element, $attribute)
	{
		# traverse through bk attributes
		foreach(array_values(array_filter(
			iterator_to_array($element->attributes), function($attr) {	
				return preg_match_all('/^bk:.*$/', $attr->name);
		})) as $attr)
		{
			if ($attr->name == BK_TAG['text'])
				$element->nodeValue = $this->model->get_attribute(
					$attr->value, $attribute);
			else
				$element->setAttribute(str_replace('bk:','',$attr->name),								$this->model->get_attribute(
						$attr->value, $attribute));
			$element->removeAttribute($attr->name);
		}
	}

	private function traverse_child($node, $new_xml, $attribute)
	{
		$childs = $new_xml->query('child::*',$node);
		if (!$childs->count() == 0)
		{
			$clone = $node->cloneNode();
			foreach($childs as $child)
				$clone->appendChild($this->traverse_child($child, $new_xml, $attribute));
			if ($node->nodeName != 'block')
				$this->edit_node($clone,$attribute);
			return $clone;
		}
		else
		{
			$clone = $node->cloneNode();
			$this->edit_node($clone, $attribute);
			return $clone;
		}
	}

	public function render($html)
	{
		if (file_exists(__STATIC__.$html)) 
		{
			include __STATIC__.$html;
		}
		else if (file_exists(__TEMPLATE__.$html))
		{
			// load document
			$internalErrors = libxml_use_internal_errors(true);
			$document = new DOMDocument();
			$document->preserveWhiteSpace = false;
			$document->formatOutput = true;
			$document->loadHTMLFile(__TEMPLATE__.$html);
			libxml_use_internal_errors($internalErrors);
			
			// select node is bk:block
			$xml = new DOMXpath($document);
			foreach($xml->query(XPATH_BLOCK) as $element)
			{
				$attr = array_values(array_filter(
					iterator_to_array($element->attributes), function($attr) {	
						return preg_match_all('/^bk:.*$/', $attr->name);
				}))[0];
				$regex = '/^\s*[a-z|A-Z|_][a-z|A-Z|_|0-9]*\s*\:\s*\$\{[a-z|A-Z|_][a-z|A-Z|_|0-9]*\}\s*$/';
				if ($attr->name == BK_TAG['foreach'] && preg_match_all($regex, $attr->value))
				{
					$values = array_map(function($e){
						return ltrim(rtrim($e));
						},explode(':', $attr->value));
					
					// append child from template
					$new_xml = new DOMXpath($document);
					$models = $this->model->get_attribute($values[1]);
					$parent_node = $element->parentNode;
					foreach($models as $model)
					{
						$node = $this->traverse_child($element, $new_xml, [
							$values[0]=>$model]);
						while ($node->hasChildNodes())
							$parent_node->insertBefore($node->lastChild, $element->nextSibling);
					}
					$parent_node->removeChild($element);
				}
			}
			
			// select node has bk: attribute
			foreach($xml->query(XPATH_NODE) as $element)
			{
				# traverse through bk attributes
				foreach(array_values(array_filter(
					iterator_to_array($element->attributes), function($attr) {	
						return preg_match_all('/^bk:.*$/', $attr->name);
				})) as $attr)
				{
					if ($attr->name == BK_TAG['text'])
						$element->nodeValue = $this->model->get_attribute($attr->value);
					$element->removeAttribute($attr->name);
				}
			}
			echo $xml->document->saveHTML();
		}
		else echo $html;
	}
}
		
?>