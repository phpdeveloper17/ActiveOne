<?php

namespace Unilab\Prescription\Block;

class Presavescannedrx extends \Magento\Framework\View\Element\Template
{
	protected $image_name;
	protected $image_source;

	public function __construct(\Magento\Framework\View\Element\Template\Context $context)
	{
		parent::__construct($context);
	}

	public function setImageName($image_name){
		$this->image_name = $image_name;
	}

	public function setImageSource($image_source){
		$this->image_source = $image_source;
	}

	public function getImageName(){
		return $this->image_name;
	}

	public function getImageSource(){
		return $this->image_source;
	}

	
}