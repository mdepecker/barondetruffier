<?php

/**
 * Created by PhpStorm.
 * User: mdepe
 * Date: 08/01/2016
 * Time: 09:36
 */
class AdminOtherMailController extends ModuleAdminController
{

    public function __construct()
    {
        $this->table = 'othermailproduct';
        $this->lang = false;
        $this->bootstrap = true;
        $this->meta_title = $this->l('Mail alert');
        $this->module = 'othermailalert';
        $this->context = Context::getContext();
        /*todo do custom query*/

        $this->fields_list = array(
            'id_othermailproduct' => array(
                'title' => $this->l('ID'),
                'align' => 'text-center',
            ),
            'id_product' => array(
                'title' => $this->l('product'),
                'align' => 'text-center',
            ),
            'threshold' => array(
                'title' => $this->l('Seuil Alert'),
                'align' => 'text-center',
            ),
            'email' => array(
                'title' => $this->l('destinataire'),
                'align' => 'text-center',
            ),
        );
        parent::__construct();
    }

    public function renderList()
    {
        // add action on list
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->addRowAction('details');
        $this->bulk_actions = array('delete' => array(
            'text' => $this->l('Delete selected'),
            'confirm' => $this->l('Delete selected items?')));
        return parent::renderList();

    }
}