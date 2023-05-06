<?php
require_once("config.php");
require_once("utils.php");

// doc signed
//if ($_POST['doc'] && $_POST['sig'] && $_POST['tx'] && $_POST['sa'] ) {
if ($_POST['doc'] && $_POST['sig'] && $_POST['tx']) {
  $docrequestid = filter_alphanum($_POST['doc']);
  $sig = filter_alphanum($_POST['sig']);
  $tx = filter_alphanum($_POST['tx']);
  //$sa = preg_replace("/[^a-zA-Z0-9]+/", "", $_POST['sa']);
}
else {
  return_error("missinginput"); //echo "Missing document/signature info. Please re-check link.";
  exit();
}

// load up json
$json_filename = $json_dir.$docrequestid.".json";
//echo $json_filename."<br/>";
//echo $sig;

$docobj = json_decode(file_get_contents($json_dir.$docrequestid.".json"));
if ($docobj === null) {
  return_error("notfound"); //.$json_dir.$docrequestid.".json"); // document id file not found
  exit();
}

exit_if_expired($docobj->creationdate);

// update json with signature
$time = time();

// loop through signers, looking for sig match and if all have now signed
$num_signers = 0;
$num_signed = 0;
$signer_index = -1;
$email_sig_text = "";

for ($k = 0; $k < $max_signers; $k++) {
  if (property_exists($docobj, signer.$k)) {
    $num_signers++;
    if ( ($docobj->{signer.$k}->id) == $sig) {
      $signer_index = $k;
      $docobj->{signer.$k}->signblock = $tx;
      $docobj->{signer.$k}->signtime = $time;
      //echo $docobj->{signer.$k}->name." has now signed.";
      //echo "signer index: ".$k."<br/>\n";
    }
    if (property_exists($docobj->{signer.$k}, 'signblock')) {
      $num_signed++;
      $email_sig_text .= $docobj->{signer.$k}->name." signed ".date("M-j-Y g:i a", $docobj->{signer.$k}->signtime)." UTC\n\n";
      $email_sig_text .= $docobj->{signer.$k}->name." Signature: https://proton.bloks.io/transaction/".$docobj->{signer.$k}->signblock."\n\n";
    }
  }
}
//echo "num_signers: $num_signers\n<br/>";
if ($signer_index == -1) {
  return_error("nosigner".$docobj->signer0->id); // echo "Can't find signer or document, please re-check the link.";
  exit();
}

// save updated json to file
//echo "<br/><br/>\n"; //signed by both:".$signedbyboth." ".$json_filename;
file_put_contents($json_filename, json_encode($docobj));

// TODO: actually check w/ blockchain possibly
// check if signed by both
if ($num_signers == $num_signed) {
  // if signed by all,
  // email all including transaction id's, doc
  $body = "Document: ".$docobj->filename." (attached)\n\nSHA256 Checksum: ".$docobj->hash."\n\n";
  $body .= $email_sig_text;
  $body .= "Checksum verification: https://emn178.github.io/online-tools/sha256_checksum.html\n\n";
  $body .= "ProtonSign.org\nFree Document Signing.\n";

  $headers = "From: ".$email_from;
  //$headers = ""; // "From: ProtonSign"." <adrian@adrianscott.com>";
  $semi_rand = md5(time());
  $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";
  //echo $mime_boundary;
  $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\"";
  $message = "--{$mime_boundary}\n" . "Content-Type: text/plain; charset=\"UTF-8\"\n" . "Content-Transfer-Encoding: 7bit\n\n" . $body . "\n\n";

  $file = $docobj->filepath;
  //echo $docobj->filepath;
        $message .= "--{$mime_boundary}\n";
        $fp =    @fopen($file,"rb");
        $data =  @fread($fp,filesize($file));

        @fclose($fp);
        $data = chunk_split(base64_encode($data));
        $message .= "Content-Type: application/octet-stream; name=\"".basename($file)."\"\n" .
        "Content-Description: ".$docobj->filename."\n" .
        "Content-Disposition: attachment;\n" . " filename=\"".$docobj->filename."\"; size=".filesize($file).";\n" .
        "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
  $message .= "--{$mime_boundary}--";
  $returnpath = "-f" . $from;

  // Send email
  //$mail = @mail($to, $subject, $message, $headers, $returnpath);
  for ($j = 0; $j < $num_signers; $j++) {
    $mail = @mail($docobj->{signer.$j}->email, "File signed with ProtonSign", $message, $headers);
  }
  //echo "mail response: ".$mail."DONE";
  //echo "<pre>".$message;
  //mail($docobj->signer1->email, "File signed with ProtonSign", $body);
  //mail($docobj->signer2->email, "File signed with ProtonSign", $body);
  //echo "All parties have signed. Please check your email for a copy of the document and links to the signatures.<br/><br/>\n";
  //echo "<!-- $body -->";
}
else {
  //echo "<br/><br/>We'll email you once all parties have signed.<br/><br/>Thanks for using ProtonSign";
}

$return["result"] = 1; // $result;
echo json_encode($return);

?>
