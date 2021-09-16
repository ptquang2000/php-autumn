<?php

namespace Core;

use Core\Model;
use DOMDocument;
use DOMXpath;

define('XPATH_NODE', '//*[@*[contains(name(), "bk:")]]');
define('XPATH_BLOCK', '//*[local-name()="block"]');
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
	
	private function edit_node($node, $attribute=null)
	{
		# traverse through bk attributes
		foreach(array_values(array_filter(
			iterator_to_array($node->attributes), function($attr) {	
				return preg_match_all('/^bk:.*$/', $attr->name);
		})) as $attr)
		{
			if ($attr->name == BK_TAG['text'])
				$node->nodeValue = $this->model->get_attribute(
					$attr->value, $attribute);
			else
				$node->setAttribute(str_replace('bk:','',$attr->name),								$this->model->get_attribute(
						$attr->value, $attribute));
			$node->removeAttribute($attr->name);
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
				$this->edit_node($element);

			echo $xml->document->saveHTML();
		}
		else echo $html;
	}
}
		
?>