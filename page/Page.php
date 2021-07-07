<?php
/**
 * Page
 */
class Page{
    public $page_title;
    private $page_script = "";
    private $page_body;
    public $page_template;
    public $page_data;
    public function SetPageView($page_view){
        $this->page_body = $page_view.'.php';
    }
    public function SetPageScript($script_name){
        $this->page_script .= '<script type="text/javascript" src="'.get_base_url().'js/'.$script_name.'.js"></script>';
    }
    public function LoadView(){
        require get_root_dir().'view/template/'.$this->page_template.'.php';
    }
}