<?php
require 'bootstrap.php';
$img =
Dao::beginTransaction();
Dao::updateByCriteria(new DaoQuery('Content'), 'content=:img', 'id=:id', array('img' => $img, 'id' => 1));
var_dump(Content::get(1)->getContent());
Dao::rollbackTransaction();
?>