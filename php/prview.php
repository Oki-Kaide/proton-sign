<h3>Sign file with ProtonSign</h3>
<br/><br/>
<?php
require_once("config.php");
require_once("utils.php");

// Get details from input and sanitize input
if ($_GET['doc'] && $_GET['sig']) {
  $docrequestid = preg_replace("/[^a-zA-Z0-9]+/", "", $_GET['doc']);
  $sig = preg_replace("/[^a-zA-Z0-9]+/", "", $_GET['sig']);
}
else {
  echo "Missing document/signature info. Please re-check link.";
  exit();
}

// Load relevant json file, check signer id
$json_filename = $json_dir.$docrequestid.".json";
//echo $json_filename;
$docobj = json_decode(file_get_contents($json_dir.$docrequestid.".json"));
//print_r($docobj);

$signer_index = -1;
for ($k = 0; $k < $max_signers; $k++) {
  if ( ($docobj->{signer.$k}->id) == $sig) {
    $signer_index = $k;
    //echo "signer index: ".$k."<br/>\n";
  }
}
if ($signer_index == -1) {
  echo "Can't find signer or document, please re-check the link.";
  exit();
}

$filename = $docobj->filename;
$hash = $docobj->hash;
exit_if_expired($docobj->creationdate);

echo "<a href=\"download.php?doc=".htmlspecialchars($docrequestid)."&sig=".htmlspecialchars($sig)."\">Doc</a><br/><br/>\n";
echo "<font size=+2>".htmlspecialchars($filename)."</font><br/>\n";
echo "hash: ".htmlspecialchars($hash)."<br/>\n";

//echo "node encode.js ".$hash." ".$docrequestid." ".$sig;
//$url = exec("/usr/local/bin/node /data/prot1/code/app/encode.js ".$hash." ".$docrequestid." ".$sig);
echo "<br/><br/><a href=\"".$sign_link."?hash=".$docobj->hash."&doc=".$docobj->filename."&docid=".$docrequestid."&sig=".$sig."\">Continue</a>";
?>
