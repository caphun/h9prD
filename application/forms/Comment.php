<?php

class Default_Form_Comment extends Zend_Form
{
	protected function init()
	{
		$this->addElement('text', 'title', array('label' => 'Title'));
		$this->addElement('text', 'excerpt', array('label' => 'Excerpt'));
		$this->addElement('textarea', 'body', array('label' => 'Body'));
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
			'label' => 'Add Comment',
			'ignore' => true
		));
		$this->addElement('hash','csrf', array(
			'ignore' => true
		));		
	}
}
