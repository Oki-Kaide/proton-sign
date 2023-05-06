<?php
//
// ProtonSign: Upload file & create docrequest object
//   Accepts a post form with the file to be signed
//   and creates the doc request object
//    by Adrian Scott, www.adrianscott.com, @AdrianScottcom
//
require_once("config.php");
require_once("utils.php");
$target_file = $upload_dir . basename($_FILES["fileToUpload"]["name"]);
$filename = basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

$sa = filter_alphanum($_POST["sa"]);
//echo "Welcome ".htmlspecialchars($sa)."!<br/><br/>\n";
if (!$sa) {
  return_error("loginrequired"); //echo "Please login with Proton Wallet first";
  exit();
}

// Check if file was uploaded
//if (!file_exists($_FILES["fileToUpload"]["name"]) || !is_uploaded_file($_FILES["fileToUpload"]["name"])) {
if (!is_uploaded_file($_FILES["fileToUpload"]["tmp_name"])) {
  return_error("nofile"); //"Sorry, no file was uploaded. Please go back and select a file to upload.";
  exit();
}

// Check file size
if ($_FILES["fileToUpload"]["size"] > $max_upload_size) {
  return_error("filetoolarge"); // echo "Sorry, your file is too large - max ".$max_upload_size." bytes";
  exit();
}

// Allow certain file formats
//if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jp eg"
//&& $imageFileType != "gif" ) {
if ( $imageFileType != "pdf" ) {
  return_error("filetypenotaccepted"); // echo "Sorry, only PDF files are allowed at this time.";
  exit();
}
// echo "<br/><br/>";

// Process uploaded file
// calc an id to use
$docrequestid = randomseq(16);
$json_filename = $json_dir.$docrequestid.".json";
$i = 0;
// test if docrequestid already in use
while (file_exists($json_filename) && $i < 1000) {
  $docrequestid = randomseq(16);
  $json_filename = $json_dir.$docrequestid.".json";
  $i++;
}
if ($i == 999) {
  return_error("cantcreatedocrequest");
  exit();
}

$newfilename = $upload_dir . $docrequestid . ".pdf"; // "-" . basename($_FILES["fileToUpload"]["name"]);
//echo $docrequestid."<br/>".$newfilename."<br/>\n";

if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $newfilename) === false) {
    //if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
    //echo "The file ". htmlspecialchars( basename( $_FILES["fileToUpload"]["name" ])). " has been uploaded.";
  return_error("filemoveerror"." ".$_FILES["fileToUpload"]["tmp_name"]." ".$newfilename); //"Sorry, there was an error uploading your file.";
  exit();
}

// successful file move, so process:
//echo "processing\n";

// calc sha256 hash
//echo "sha256sum ".escapeshellcmd($newfilename); TODO
$output = explode(" ", exec("sha256sum ".escapeshellarg($newfilename)));
$hash = $output[0];

// generate a .json file for the doc etc
// store filename, hash, creation date, path to file, docrequestid is filename
$docobj->filename = $filename;
$docobj->creationdate = time();
$docobj->filepath = $newfilename;
$docobj->hash = $hash;
$docobj->creator = $sa;
$docobjjson = json_encode($docobj);
//echo $docobjjson;

//echo $json_filename;
$write = file_put_contents($json_filename, $docobjjson);
if ($write === false) {
  return_error("cantwrite ".$json_filename); // Can't write doc info file
  exit();
}
//$docobj->docrequestid = $docrequestid;
$result["docrequestid"] = $docrequestid;
$result["hash"] = $hash;
$result["filename"] = $docobj->filename;
$result["filesize"] = filesize($docobj->filepath);
$return["result"] = $result;
//$return["result"] = $docobj;
echo json_encode($return);

/*
echo "<font size=+2>".htmlspecialchars($filename)."</font>";
echo "<br/>\n";
echo "hash: ".htmlspecialchars($hash)."<br/><br/>\n";
?>
<form method="post" action="success.php">
<h3>Add Signers:</h3><br/>
<?php
for ($i = 0; $i < $max_signers; $i++) {
  echo "Name: <input name=\"name$i\"> Email: <input name=\"email$i\"><br/>\n";
}
?>
<br/>
<input type="hidden" name="filename" value="<?php echo encodeValue($filename); ?>">
<input type="hidden" name="hash" value="<?php echo encodeValue($hash); ?>">
<input type="hidden" name="docrequestid" value="<?php echo encodeValue($docrequestid); ?>">
<?php echo "<input type=hidden name=sa value='".htmlspecialchars($sa)."'>\n";?>
<input type="submit" value="Send out for signing">
</form>

<br/><br/>
All parties must sign within 1 week or void
*/
?>
