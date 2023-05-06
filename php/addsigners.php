<?php
//
// ProtonSign: Add signers to the docrequest & Notify them
//   Accepts a post form with the signer names and emails
//   Sends signing emails to them
//    by Adrian Scott, www.adrianscott.com, @AdrianScottcom
//
require_once("config.php");
require_once("utils.php");

$sa = filter_alphanum($_POST["sa"]);
//echo "Welcome ".htmlspecialchars($sa)."!<br/><br/>\n";
if (!$sa) {
  return_error("loginrequired"); //echo "Please login with Proton Wallet first";
  exit();
}

$signers = array();
$j = 0;

// check if input fields are all present
for ($i = 0; $i < $max_signers; $i++) {
  //echo $_POST["name".$i]." ". $_POST["email".$i]."<br/>\n";
  if ($_POST["name".$i] && $_POST["email".$i]) {
    if (!filter_var($_POST["email".$i], FILTER_VALIDATE_EMAIL)) {
      return_error("invalid email");// "Invalid email address ".htmlspecialchars($_POST["email".$i]).". Please go back and fix.";
      exit();
    }
    else {
      // valid email address, and name also filled in, so add to signers
      $signers[$j]["name"] = $_POST["name".$i];
      $signers[$j]["email"] = $_POST["email".$i];
      //print_r($signers[$j]);
      $j++;
    }
  }
  else {
    if ($_POST["name".$i] || $_POST["email".$i]) {
      return_error("incomplete form"); //echo "Please go back and fill in both a name and an email address.";
      exit();
    }
  }
}

// if no name + email combo, ask them to fill in form
if ($j == 0) {
  return_error("missing input"); //echo "Missing input, please go back and fill in at least one name and email address to sign the document.";
  exit();
}

// load json file
// filter input to only return alphanums in docrequestid
$docrequestid = filter_alphanum($_POST['docrequestid']);
$json_filename = $json_dir.$docrequestid.".json";
$docobj = json_decode(file_get_contents($json_dir.$docrequestid.".json"));

if ($docobj === null) {
  return_error("notfound"); // document id file not found
  exit();
}

// confirm creator is same as user
if ($docobj->creator !== $sa) {
  return_error("unauthorized"); // Not same user as creator of the request
  exit();
}

// make sure that signers have not already been added?
if (property_exists($docobj, "signer0")) {
  return_error("alreadyhassigners".json_encode($docobj)); // Already has signers, we don't permit adding new ones
  exit();
}

// add signers to the json file with id's
for ($k = 0; $k < $j; $k++) {
  $tempsigner = new stdClass;
  $tempsigner->name = $signers[$k]["name"];
  $tempsigner->email = $signers[$k]["email"];
  $tempsigner->id = randomseq(16);
  //print_r($tempsigner);
  $docobj->{signer.$k} = $tempsigner;

  //print_r($docobj);
  // mail view/sign links to the signers
  //echo($signers[$k]["email"]." Sign file with ProtonSign"." Sign file with ProtonSign\n\n".$docobj->hash."\n ".$docobj->filename."\n\nSign Doc ".$domain_root."prview.php?doc=".$docrequestid."&sig=".$tempsigner->id."\n"." From: ".$email_from);
  mail($signers[$k]["email"], "Sign file with ProtonSign", "Sign file with ProtonSign\n\n".$docobj->hash."\n ".$docobj->filename."\n\nSign Doc ".$sign_link."?doc=".$docrequestid."&sig=".$tempsigner->id."\n", "From: ".$email_from);
}

//echo "\n\n<br/><br/>Docobj";
//print_r($docobj);
// TODO better to do this before emailing perhaps
file_put_contents($json_filename, json_encode($docobj));

//$result["docrequestid"] = $docrequestid;
$return["result"] = 1; // $result;
echo json_encode($return);
?>
