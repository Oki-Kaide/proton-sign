<?php
require_once("config.php");
require_once("utils.php");

//output/include the doc, checking for value
// Get details from input and sanitize input
if ($_GET['doc'] && $_GET['sig']) {
  $docrequestid = preg_replace("/[^a-zA-Z0-9]+/", "", $_GET['doc']);
  $signerid = preg_replace("/[^a-zA-Z0-9]+/", "", $_GET['sig']);
}
else {
  echo "Missing document/signature info. Please re-check link.";
  exit();
}

// Load relevant json file, check signer id
$json_filename = $json_dir.$docrequestid.".json";
$docobj = json_decode(file_get_contents($json_dir.$docrequestid.".json"));

  $file = $docobj->filepath;
$filename = $docobj->filename;
exit_if_expired($docobj->creationdate);


  header('Content-type: application/pdf');
  header('Content-Disposition: inline; filename="' . $filename . '"');
  header('Content-Transfer-Encoding: binary');
  header('Accept-Ranges: bytes');
  @readfile($file);
?>
