<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
    	$table = new Post();
		$this->view->post_list = $table->fetchAll();
    }


}

