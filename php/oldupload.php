<html>
<body>

<?php
require_once("config.php");

$sa = $_GET["sa"];
if (!$sa) {
  echo "Please login with Proton Wallet first";
  exit();
}
echo "Welcome ".htmlspecialchars($sa)."!<br/><br/>\n";
?>
Upload Document to Sign
<br/><br/>
<form action="sign.php" method="post" enctype="multipart/form-data">
  <input type="file"  name="fileToUpload" id="fileToUpload">
<?php 
echo "<input type=hidden name=sa value='".htmlspecialchars($sa)."'>\n";
?>
<br/><br/>
  <input type="submit" value="Upload" name="submit">
</form>

</body>
</html>
