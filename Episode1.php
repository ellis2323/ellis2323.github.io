<? include "Header.php"; ?>

<?
include_once "markdown.php";
$myFile = "Episode1.txt";
$fh = fopen($myFile, 'r');
$text = fread($fh, filesize($myFile));
$my_html = Markdown($text);
print $my_html;
?>

<? include "Footer.php"; ?>
