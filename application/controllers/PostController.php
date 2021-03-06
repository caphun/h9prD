<?php

class PostController extends Zend_Controller_Action
{	
    public function init()
    {
    
	}

    public function indexAction()
    {
    	$table = new Post();
		$this->view->post_list = $table->fetchAll();
    }

    public function viewAction()
    {
    	$table = new Post();
        $this->view->post = $table->fetch($this->_request->id);
    }

    public function addAction()
    {
        $form = new Default_Form_Post();
		$req = $this->_request;
		
		if ($req->isPost() && $form->isValid($req->getPost()))
		{
			$post = new Post();
			$values = $form->getValues();
			$post->fromArray($values);
			$post->save();
			$this->_redirect('/post/view/id/' . $post->id);
		}
		$this->view->form = $form;
    }

    public function editAction()
    {
		$table = new Post();
        $form = new Default_Form_Post();
		$req = $this->_request;
		$post = $table->fetch($req->id);
		$form->populate($post->toArray());
		
		if ($req->isPost() && $form->isValid($req->getPost()))
		{
			$values = $form->getValues();
			$post->fromArray($values);
			$post->save();
			$this->_redirect('/post/view/id/' . $post->id);
		}
		
		$this->view->form = $form;
		$this->view->post = $post;
    }


}









