<?php
require_once("config.php");
require_once("function.php");

$language = fetch_all_row("SELECT * FROM ui_language");

?>
<ul>
<?php
foreach ($language as $row) {
    echo "<li><a onclick=\"document.cookie='lang=" . $row["lang_code"] . "; path=/'; history.back();\">" . T_($row["lang_code"]) . "</a></li>";
}
?>
</ul>
