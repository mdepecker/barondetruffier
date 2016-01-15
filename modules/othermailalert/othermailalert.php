<?php
if(!defined('_PS_VERSION_'))
    exit;

class OtherMailAlert extends Module{

    public function __construct()
    {
        $this->name = 'othermailalert';
        $this->tab = 'administration';
        $this->author = 'Moi (Moche et méchant)';
        $this->version = '1.0.0';
        $this->ps_version_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->bootstrap = true;
        $this->context = Context::getContext();

        parent::__construct();

        $this->need_instance = 0;

        $this->displayName = $this->l('Le super mail alert des M2');
        $this->description = $this->l('La super description qui va avec');

        $this->confirmUninstall = $this->l("Non c'est pas vraiment ce que tu veux faire");
    }
    public function install(){
        if(!parent::install()
        || !$this->installDb()
        || !$this->installTab()
        ) return false;
        return true;
    }
    public function uninstall(){
        if(!parent::uninstall()
            || !$this->removeConfigValue()
            || ! $this->uninstallDb()
            || !$this->uninstallTab()
        )
            return false;
        return true;
    }

    public function removeConfigValue(){
            Configuration::deleteByName('DEFAULT_THRESHOLD');
            Configuration::deleteByName('DEFAULT_EMAIL_ADDRESS');
            return true;


    }
    public function getContent(){

        $output  = null;
        $has_error = false;
        if(Tools::isSubmit('submit'.$this->name)){
            $default_threshold = strval(Tools::getValue('DEFAULT_THRESHOLD'));
            $default_email_address = strval(Tools::getValue('DEFAULT_EMAIL_ADDRESS'));
            if (!$default_threshold
                ||empty($default_threshold)
                ||!Validate::isInt($default_threshold)){
                $output.= $this->displayError($this->l("le champ n'est pas un nombre"));
                $has_error = true;
            }
            if (!$default_email_address
                ||empty($default_email_address)
                ||!Validate::isEmail($default_email_address)){
                $output.= $this->displayError($this->l("le champ n'est pas une adresse email"));
                $has_error = true;
            }
            if(!$has_error){
                Configuration::updateValue('DEFAULT_THRESHOLD',$default_threshold);
                Configuration::updateValue('DEFAULT_EMAIL_ADDRESS',$default_email_address);
                $output.=$this->displayConfirmation($this->l("Okay, c'est cool"));
            }
        }
        return $output.$this->displayForm();
    }
    public function displayForm(){
        $lang = (int)Configuration::get('PS_LANG_DEFAULT');
        $helper = new HelperForm();
        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => 'Ceci est la légence dans un tableau'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => 'label de mon champ threshold',
                    'name' => 'DEFAULT_THRESHOLD',
                    'require' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => 'label de mon champ email',
                    'name' => 'DEFAULT_EMAIL_ADDRESS',
                    'require' => true,
                )
            ),
            'submit' => array(
                'title' => 'Sauvegarder',
                'class' => 'button'
            )
        );
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        //$helper->currentIndex = AdminController::$curretnIndex.'&configure='.$this->name;

        $helper->default_form_language = $lang;
        $helper->allow_employee_form_lang = $lang; /*optional (really)*/

        $helper->title = $this->displayName;
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->submit_action = 'submit'.$this->name;
        $helper->toolbar_btn = array(
            'save' =>
            array(
                'desc' => $this->l('sauvegarder'),
                'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.'token='.Tools::getAdminTokenLite('AdminModules')
            ));

        $helper->fields_value['DEFAULT_THRESHOLD'] = (int)Configuration::get('DEFAULT_THRESHOLD');
        $helper->fields_value['DEFAULT_EMAIL_ADDRESS'] = Configuration::get('DEFAULT_EMAIL_ADDRESS');
        return $helper->generateForm($fields_form);
    }

    public function installDb(){
        $sql = "CREATE TABLE IF NOt EXISTS " ._DB_PREFIX_."othermailproduct(
        id_otherMailProduct int(11) NOT NULL AUTO_INCREMENT,
        id_product int(11) NOT NULL,
        threshold int(5) NOT NULL,
        email VARCHAR(255) NOT NULL,
        PRIMARY KEY (id_otherMailProduct)
        ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8";

        $result = Db::getInstance()->execute($sql);
        return $result;
    }
    public function uninstallDb(){
        $sql = "DROP TABLE IF EXISTS "._DB_PREFIX_."othermailalert";
        $result = Db::getInstance()->execute($sql);
        return $result;
    }

    public function installTab(){
        if(!$idTab = Tab::getIdFromClassName('AdminOtherMail')) {
            $tab = new Tab();
            $tab->class_name= 'AdminOtherMail';
            $tab->module = $this->name;
            $tab->name[$this->context->language->id] = $this->l($this->name);
            if(version_compare(_PS_VERSION_,'1.6.0.1','>=')){
                $tab->id_parent = 0;
            }else {
                $tab->id_parent = 13; // eg: 13 = transport
            }
            /* if multilingue*/
            $languages = Language::getLanguages();
            foreach($languages as $lang)
            {
                $tab->name[$lang['id_lang']]= $this->l($this->name);
            }
            return $tab->add();
        }
    }
    public function uninstallTab(){
        $idTab = Tab::getIdFromClassName("AdminOtherMail");
        if (!is_array($idTab)){
            $idTabs[] = $idTab;
        }
        foreach($idTabs as $id){
            if($id != 0){
                $tab = new Tab($id);
                $tab->delete();
            }
        }

    }

}