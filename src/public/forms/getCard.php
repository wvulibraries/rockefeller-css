<?php
// The page would display the corressponding card

//Get the localvars
$localvars  = localvars::getInstance();

//Some other variables
$thisRow = '';

//Create the instance for the db connection name
$db = db::get($localvars->get('dbConnectionName'));

// Test to see for if the identifier is set
if(isset($_REQUEST['cid'])){
  $curCard = intval($_REQUEST['cid']);
}
else{
  $output .= "Identifier not set";
}

//Simple check for the negetive values
if($curCard<0){
  $localvars->set('errInfo', "Invalid Identifier");
  return;
}

//Execute the query
$curCard=htmlSanitize($curCard);
$sql = "SELECT * FROM `correspondence` WHERE `id`=$curCard AND `publicAccess`=1";
$sqlResult = $db->query($sql);

//Check for any errors
if ($sqlResult->error()) {
    throw new Exception("ERROR SQL" . $sqlResult->errorMsg());
}

//Check if there are any rows
if ($sqlResult->rowCount() < 1) {
   $output .=  "<p>No Rows</p>";
}
else{
  while($row = $sqlResult->fetch()){
    $thisRow.='<table>';
    foreach ($row as $key => $value) {
      //Check for the flag
      $key=htmlSanitize($key);
      $prmsnQuery = "SELECT `publicAccess` FROM `publicAccess` WHERE `fName`='$key'";
      $prmsnQueryRslt = $db->query($prmsnQuery);
      //check for errors
      if ($prmsnQueryRslt->error()) {
          throw new Exception("ERROR SQL" . $prmsnQueryRslt->errorMsg());
      }
      //Fetch the results
      while($pQueryRow = $prmsnQueryRslt->fetch()){
        foreach ($pQueryRow as $pQkey => $pQValue) {
          // Show the results only when you have permissions
          if(strcmp($pQValue[0],"1")==0){
            if((strcmp($key,"out_document_name")==0)||(strcmp($key,"in_document_name")==0)){

              //Method 1 to build path
              //$pTkns=explode('\\',strval($value));
              //$fPath=$pTkns[1].'/'.$pTkns[1];

              //Method 2 to build path
              $pTkns=explode('\\',strval($value));
              $fChar=array_shift($pTkns);
              $pTkns = array_diff($pTkns,array('BlobExport'));
              $fPath = implode("/",$pTkns);
              #print $fPath;
              #$fPath='documents/indivletters/9996092.txt';

              $thisRow .= sprintf(
              '<tr>
              <td>%s</td>
              <td><a href="%s">%s</a></td>
              </tr>',
              htmlSanitize("$key"),
              htmlSanitize("$fPath"),
              htmlSanitize("$value")
            );

            }
            else{
              $thisRow .= sprintf(
              '<tr>
              <td>%s</td>
              <td>%s</td>
              </tr>',
              htmlSanitize("$key"),
              htmlSanitize("$value")
            );
            }
          }
        }
      }
    }
    $thisRow.='</table>';
}
$output .= $thisRow;

}

// Set the variables finally
$localvars->set('cardInfo', $output);
