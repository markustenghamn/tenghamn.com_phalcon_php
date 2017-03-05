<?php
/**
 * Created by PhpStorm.
 * User: markustenghamn
 * Date: 2016-10-15
 * Time: 14:26
 */
$verify = false;
if (isset($_POST['password'])) {
    //$hash = password_hash($_POST['password'], 'bcrypt');
    $verify = password_verify($_POST['password'], $_POST['hash']);
}

?>
    <form method="post" action="">
    Receipt data: <input type="text" name="password">
    <br/>
    Token: <input type="text" name="hash">
    <br/>
        <input type="submit" value="Verifiera token">
    </form>
<?php

if (isset($_POST['password'])) {
    if ($verify) {
        echo "<b>Token matches receipt data:</b> " . $_POST['hash'];
    } else {
        echo "<b>Token does not match receipt data:</b> " . $_POST['hash'];
    }
}