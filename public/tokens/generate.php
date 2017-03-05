<?php
/**
 * Created by PhpStorm.
 * User: markustenghamn
 * Date: 2016-10-15
 * Time: 14:24
 */
$hash = "";
if (isset($_POST['password'])) {
    $hash = password_hash($_POST['password'], PASSWORD_BCRYPT);
}

?>
<form method="post" action="">
Receipt data: <input type="text" name="password" value="<?php if (isset($_POST['password'])) { echo $_POST['password']; } ?>">
<br/>
    <input type="submit" value="Skapa token">
</form>
<?php
if (strlen($hash) > 0) {
    echo "<b>Token:</b> ".$hash;
}

