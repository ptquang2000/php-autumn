<?php

namespace Core;

use Core\Model;
use DOMDocument;
use DOMXpath;
use ReflectionClass;
use ErrorException;

trait ControllerTrait
{
	private Model $model;

	public function __construct()
	{
		$reflection = new ReflectionClass(get_parent_class($this));
		
		// instantiate autwired properties;
		foreach($reflection->getProperties() as $prop) {
			if (!($attr=$prop->getAttributes()) 
			|| $attr[0]->getName()!='Core\Autowired') continue;            
			
			$reflection_prop =  $reflection->getProperty($prop->getName());
			$reflection_prop->setAccessible(true);
			$class_name = $prop->getType()->getName();
			
			$reflection_prop->setValue($this, ($o=autowired($class_name)));
		}
	}

	public function init_model()
	{
		$this->model = new Model();
		return $this->model;
	}
	
	private function edit_node($node, $attribute=null, $iter=null)
	{
		# traverse through bk attributes
		foreach(array_values(array_filter(
			iterator_to_array($node->attributes), function($attr) {	
				return preg_match_all('/^bk:.*$/', $attr->name);
		})) as $attr)
		{
			if (isset($iter))
			{
				$this->model->add_attribute(
					array_key_first($iter),
					$iter[array_key_first($iter)]
				);
				$attribute[array_key_first($iter)] = $iter[array_key_first($iter)];
			}
			try
			{
				if ($attr->name == BK_TAG['text'])
				{
					preg_match_all('/\$\{[^$]*\}/', $attr->value, $matches);
					$text = $attr->value;
					foreach($matches[0] as $match)
						$text = str_replace($match, $this->model->get_attribute($match, $attribute), $text);
					$node->nodeValue = $text;
				}
				else if ($attr->name == BK_TAG['action'])
					$node->setAttribute(str_replace('bk:','',$attr->name),
					Model::get_action($attr->value));
				else $node->setAttribute(str_replace('bk:','',$attr->name),
					$this->model->get_attribute($attr->value, $attribute));
				$node->removeAttribute($attr->name);
			} catch (\Exception $e)
			{
				$msg = explode(':', $e->getMessage())[0];
				if ($msg == 'Model Exception')
					throw new \Exception ($e->getMessage().' on line '.$node->getLineNo());
				else if ($msg == 'Path Exception')
					throw new \Exception ($e->getMessage().$node->getLineNo());
				throw $e;
			}
		}
	}

	private function traverse_child($node, $new_xml, $attribute, $iter=null)
	{
		$childs = $new_xml->query('child::*',$node);
		if (!$childs->count() == 0)
		{
			$clone = $node->cloneNode();
			foreach($childs as $child)
				$clone->appendChild($this->traverse_child($child, $new_xml, $attribute, $iter));
			if ($node->nodeName != 'block')
				$this->edit_node($clone, $attribute, $iter);
			return $clone;
		}
		else
		{
			$clone = $node->cloneNode();
			$this->edit_node($clone, $attribute, $iter);
			return $clone;
		}
	}

	public function render($html)
	{
		if (!isset($html)) return;
		if (file_exists(__STATIC__.$html)) 
			include __STATIC__.$html;
		else if (file_exists(__TEMPLATE__.$html))
		{
			// load document
			$internalErrors = libxml_use_internal_errors(true);
			$document = new DOMDocument();
			$document->preserveWhiteSpace = false;
			// $document->formatOutput = true;
			$document->loadHTMLFile(__TEMPLATE__.$html);
			libxml_use_internal_errors($internalErrors);
		
			// select node is bk:block
			$xml = new DOMXpath($document);
			foreach($xml->query(XPATH_BLOCK) as $element)
			{
				$attr = array_values(array_filter(
					iterator_to_array($element->attributes),
					function($attr) {return preg_match_all('/^bk:.*$/', $attr->name);})
				)[0];
				$regex = '/^[a-z|A-Z|_][a-z|A-Z|_|0-9]*(,[a-z|A-Z|_][a-z|A-Z|_|0-9]*)?\:\$\{[a-z|A-Z|_][a-z|A-Z|_|0-9]*\}$/';
				$str = preg_replace('/\s+/', '', $attr->value);
				if ($attr->name == BK_TAG['foreach'] && preg_match_all($regex, $str))
				{
					if (preg_match('/,[a-z|A-Z|_][a-z|A-Z|_|0-9]*/', $str, $iter) == 1)
					{
						$str = str_replace($iter[0], '', $str);
						$iter = str_replace(',','', $iter[0]);
					}

					$values = array_map(function($e){
						return ltrim(rtrim($e));
					},explode(':', $str));
					
					// append child from template
					$new_xml = new DOMXpath($document);
					$models = $this->model->get_attribute($values[1]);
					$parent_node = $element->parentNode;
					foreach($models as $key=>$model)
					{
						$node = $this->traverse_child($element, $new_xml, [$values[0]=>$model], [$iter=>count($models)-$key]);
						while ($node->hasChildNodes())
							$parent_node->insertBefore($node->lastChild, $element->nextSibling);
					}
					$parent_node->removeChild($element);
				}
			}
			// select node has bk: attribute
			foreach($xml->query(XPATH_NODE) as $element)
				$this->edit_node($element);
			
			// print out dynamic html
			echo $xml->document->saveHTML();
		}
		else echo $html;
	}
}

?>