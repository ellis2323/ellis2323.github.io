<? include "Header.php"; ?>
<?
include_once "markdown.php";
$myFile = "Introduction.txt";
$fh = fopen($myFile, 'r');
$text = fread($fh, filesize($myFile));
$my_html = Markdown($text);
print $my_html;
?>
</body>
</html>

<? include "Footer.php"; ?>
