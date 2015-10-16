<html><body>
  <head>
    <title>Tutorial: FamilySearch PHP SDK</title>
  </head>
    <h1>Welcome to the Gedcom X PHP SDK tutorial.</h1>
    <p>To get started, open the tutorial.php file in an editor, then read and enable the "SETUP" and "AUTHENTICATE" sections.<br>
    (To enable a section, remove the block comment tags /* and */ for the section.)<br>
    Refresh this page to test the newly enabled code.<br>
    </p>
    
<?php
// *** SETUP  ***
  
// *** BEGIN: AUTHENTICATE  ***************************************************

if(!isset($_SESSION['fs_access_token'])){ // AUTHENTICATE

  if(!isset($_GET['code'])) { // GET AN AUTHORIZATION CODE     
  header('Location: ' . $client->getOAuth2AuthorizationURI());
  }
		// LOAD $code FROM THE URI PARAMS
	$code = $_GET['code'];
		// EXCHANGE THE AUTHORIZATION CODE FOR AN ACCESS TOKEN, AND STORE IT IN A SESSION VARIABLE
  $_SESSION['fs_access_token'] = $client->authenticateViaOAuth2AuthCode($code)->getAccessToken();
	}

?>
<h1>Step 1: Authenticate with FamilySearch</h1>
<h3>CONGRATULATIONS! Your user is authenticated.</h3>
  <p>The access token (Client ID) is:<br> 
  <?=$_SESSION['fs_access_token']?> <br>
  It has been stored in a session so that future interactions in this tutorial are authenticated.</p>
<h3>(Now, read and enable the CURRENT USER section.)</h3>
<?php


// *** END: AUTHENTICATE

// *** BEGIN: CURRENT USER ***************************************************

echo "<h1>Step 2: Get Current User</h1>";       

  // READ THE CURRENT USER PERSON AND SAVE THE RESPONSE
$response = $client->familytree()->readPersonForCurrentUser();
  // GET THE PERSON FROM THE RESPONSE
$person = $response->getPerson();
  // DISPLAY THE CURRENT USER INFO
printPerson($person);

echo "<h3>(Now, read and enable the SEARCH section.)</h3>";


// *** END: CURRENT USER

// *** BEGIN: SEARCH ***************************************************

?>		
<h1>Step 3: Search for a Person by Name</h1>
	<form action="" method="post">
		<p>Enter Last Name (surname): <input type="text" name="lastname" /></p>
	</form>
  
<?php
if (isset($_POST['lastname'])) {
    // CONSTRUCT THE SEARCH QUERY
  $query = new Gedcomx\Rs\Client\Util\GedcomxPersonSearchQueryBuilder();
		// LOAD THE SEARCH PARAMETER(S)INTO THE QUERY STRUCTURE 
  $query->surname($_POST['lastname']);
    // PERFORM THE SEARCH
      // Search for matches and save the response
  $searchResponse = $client->familytree()->searchForPersons($query);
  if ($searchResponse->getResults()) { // MATCHES FOUND
      // Get the matching results
    $entries = $searchResponse->getResults()->getEntries();
  
    {   // DISPLAY THE RESULTS
?>
      <h3>Search Results</h3>
      <table class="table">
        <tr>
        <th>Id</th>
        <th>Name</th>
        <th>Birth</th>
        </tr>
<?php				
      foreach($entries as $entry){
          // Each $entry can contain spouses and parents as well as the
          // matched person. The $entry ID is the ID of the matched person.
        $personId = $entry->getId();
        $gedcomx = $entry->getContent()->getGedcomx();
        foreach($gedcomx->getPersons() as $person){
            // When examining the gedcomx content, we only display the matched person.
          if($person->getId() === $personId){
            $display = $person->getDisplayExtension();
?>
            <tr>
            <td><?= $person->getId(); ?></td>
            <td><?= $display->getName(); ?></td>
            <td><?= $display->getBirthDate(); ?></td>
            </tr>
<?php			  
          }
        }
      }
      echo '</table>';
    } // END DISPLAY THE RESULTS
    echo "<h3>(Now, read and enable the READ PID section.)</h3>";
  } // END MATCHES FOUND
} 

// *** END: SEARCH

// *** BEGIN: READ PID ***************************************************

?>
<h1>Step 4: Read a Person by Person ID</h1>
  <form action="" method="post">
    <p>Enter a Person ID: <input type="text" name="pid" /></p>
  </form>
  
<?php
if (isset($_POST['pid'])) {	
    // READ THE PERSON AND SAVE THE RESPONSE
  $response = $client->familytree()->readPersonById($_POST['pid']);
    // GET THE PERSON FROM THE RESPONSE
  $person = $response->getPerson(); if (!$person) exit;
    // DISPLAY THE PERSON INFO
  echo "<h3>The person is:</h3>";
  printPerson($person);
  echo "<h1>CONGRATULATIONS!!! Now use the PHP SDK documentation as explained in the tutorial.</h1>";
} 

// *** END: READ PID

// =================== FUNCTIONS =======================
function printPerson($person){
  
  if(!$person) return;

  $personId = $person->getId();
  $displayInfo = $person->getDisplayExtension();
?>
  <h3><?= $displayInfo->getName(); ?></h3>
    <div class="panel panel-default">
      <table class="table">
        <tr>
          <th>ID</th>
          <th>  Gender</th>
          <th>  Birth Date</th>
          <th>  Status</th>
        </tr>
        <tr>
          <td><?= $personId; ?></td>
          <td><?= $displayInfo->getGender(); ?></td>
          <td><?= $displayInfo->getBirthDate(); ?></td>
          <td><?= $person->isLiving() ? 'Living' : 'Deceased'; ?></td>
        </tr>
      </table>
    </div>
<?php
	} // **END OF printPerson FUNCTION**	

?>
</body></html>
