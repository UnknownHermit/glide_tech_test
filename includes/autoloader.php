<?php

// Attempt to load any class from includes/classes. This saves having to include class files specifically every time we need them
spl_autoload_register(function($class){
    require_once(ROOTDIR."/includes/classes/$class.php");
});

?>