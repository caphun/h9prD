<?php

class Default_Form_Post extends Zend_Form
{
	public function init()
	{
		$this->addElement('text', 'title', array('label' => 'Title'));
		$this->addElement('text', 'excerpt', array('label' => 'Excerpt'));
		$this->addElement('textarea', 'body', array('label' => 'Body'));
		$this->addElement('checkbox', 'is_published', array('label', 'Published?'));
		$this->addElement('text', 'published_at', array('label' => 'Publish Date'));
		$this->addElement('captcha', 'captcha', array(
			'label' => 'Please enter the 5 letters below:',
			'required' => true,
			'captcha' => array(
				'captcha' => 'Figlet',
				'wordlen' => 5,
				'timeout' => 300
			)
		));
		$this->addElement('submit', 'submit', array(
			'label' => 'Save Post',
			'ignore' => true
		));
		$this->addElement('hash','csrf', array(
			'ignore' => true
		));
	}
}
