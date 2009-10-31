<?php

class CommentController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    public function addAction()
    {
        $form = new Default_Form_Comment();
		$req = $this->_request;
		
		if ($req->isPost() && $form->isValid($req->getPost()))
		{
			$comment = new Comment();
			$values = $form->getValues();
			$comment->fromArray($values);
			$comment->save();
		}
		
		$this->view->form = $form;
    }


}





