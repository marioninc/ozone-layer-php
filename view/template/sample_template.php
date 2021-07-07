<?php
$d = $this->page_data;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?=$this->page_title?></title>
</head>
<body>
    <script src="<?=get_base_url()?>/js/config.js"></script>
    <script src="<?=get_base_url()?>/js/ajax.js"></script>
    <?=$this->page_script?>
    <h1><?=$this->page_title?></h1>
    <?php require get_root_dir().'view/'.$this->page_body; ?>
</body>
</html>