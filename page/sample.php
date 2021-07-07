<?php

class Sample extends Page{
    public $method; 
    public $param;
    public $database;
    function __construct($method,$param,$database){
        // if_login();
        $this->method = $method;
        $this->param = $param;
        $this->database = $database;

        // default view template
        $this->page_template = "sample_template";
    }

    function test(){
        $this->page_title = "サンプルテスト"; // ページタイトル
        $this->SetPageView('sample_test'); // Viewを読み込む
        $this->SetPageScript('sample_test'); // スクリプトを読み込む
        $this->LoadView(); // ページを出力する
    }
}