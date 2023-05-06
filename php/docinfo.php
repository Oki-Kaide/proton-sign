<?php
require_once("config.php");
require_once("utils.php");

// Get details from input and sanitize input
if ($_POST['doc'] && $_POST['sig']) {
  $docrequestid = filter_alphanum($_POST['doc']);
  $sig =  filter_alphanum($_POST['sig']);
}
else {
  return_error("noinfo"); // Missing document/signature info. Please re-check link.";
  exit();
}

// Load relevant json file, check signer id
$json_filename = $json_dir.$docrequestid.".json";
//echo $json_filename;

$docobj = json_decode(file_get_contents($json_dir.$docrequestid.".json"));
//print_r($docobj);

if ($docobj === null) {
  return_error("notfound"); //.$json_dir.$docrequestid.".json"); // document id file not found
  exit();
}

$signer_index = -1;
for ($k = 0; $k < $max_signers; $k++) {
  if ( ($docobj->{signer.$k}->id) == $sig) {
    $signer_index = $k;
    //echo "signer index: ".$k."<br/>\n";
  }
}

if ($signer_index == -1) {
  return_error("nosigner"); //.$docobj->signer0->id); // echo "Can't find signer or document, please re-check the link.";
  exit();
}

exit_if_expired($docobj->creationdate);

$result["docrequestid"] = $docrequestid;
$result["hash"] = $docobj->hash;
$result["filename"] = $docobj->filename;
$result["signer_name"] = $docobj->{signer.$signer_index}->name;
$result["filesize"] = filesize($docobj->filepath);
$return["result"] = $result;
echo json_encode($return);

/*
echo "<a href=\"download.php?doc=".htmlspecialchars($docrequestid)."&sig=".htmlspecialchars($sig)."\">Doc</a><br/><br/>\n";
echo "<font size=+2>".htmlspecialchars($filename)."</font><br/>\n";
echo "hash: ".htmlspecialchars($hash)."<br/>\n";

//echo "node encode.js ".$hash." ".$docrequestid." ".$sig;
//$url = exec("/usr/local/bin/node /data/prot1/code/app/encode.js ".$hash." ".$docrequestid." ".$sig);
echo "<br/><br/><a href=\"".$sign_link."?hash=".$docobj->hash."&doc=".$docobj->filename."&docid=".$docrequestid."&sig=".$sig."\">Continue</a>";
*/
?>
