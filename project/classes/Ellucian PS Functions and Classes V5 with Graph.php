<?php
//Trigger Name: Ellucian PS Functions and Classes V5 with graph

class erpUser{
public $credentialType;
public $credentialValue;
public $erpId;
public $erpUserName;
public $guid;
public $firstName;
public $lastName;
public $fullName;
public $email;
public $wfRole;


/**
 * Creates @@ variables for firstName, lastName, FullName, Guid, ID, Username and email
 *
 * name="wfRole">supported wfRoles are Student, Instructor, Employee, Advisor.
 * The @@ variables are named like wfRolePropertyName ie: studentFirstName
 */
    public function __construct($application,$credentialType,$credentialValue,$wfRole,$version =null,$graphLogLevel = "not_in_use"){
     if ($GLOBALS["atat_SYS_SYS"]== "triggerHelper") {
        $caseUID = "fake";
        $aVariables = array();
        $aVariables['debug'] = "";
        $aVariables['error'] = "";
     } else {
         $caseUID = $application;
         G::LoadClass('case');
         $oCase = new Cases();                //Create Cases object
         $aCase = $oCase->loadCase($caseUID); //Load a particular case
         $aVariables = $aCase['APP_DATA'];    //Get the variables for that case
         $infoPanelHtml = $aVariables['infoPanelHtml'];
         $infoPanelArray = $aVariables['infoPanelArray'];
         if (empty($graphLogLevel)) {
             $graphLogLevel = $aVariables['graphLogLevel'];
         }
     }
       //if (!isset($aVariables[$wfRole.'Obj'])) {
            $debug = $aVariables['debug'];
            $erpUserError = $aVariables['error'];
            $erpUserError .= "nope";
            $this->wfRole = strtolower($wfRole);
            $this->credentialType = $credentialType;
            $this->credentialValue = $credentialValue;
            if ($credentialType == "guid") {
                $guid = $credentialValue;
            } else {
                $guid = "00000000-0000-0000-0000-000000000000";
            }
            $graphQuery = "query getPersonRecord{
                persons: persons12(
                  filter:
                  {OR:[
                    {id:{EQ:'$guid'}}
                    {credentials:{value:{EQ:'$credentialValue'}}}
                  ]}
                )
                {totalCount edges { node {
                  id
                  names{type{category} preference firstName lastName fullName title}
                  roles{role startOn endOn}
                  emails{type{emailType} preference address}
                  credentials{type value}
                  phones{type{phoneType} preference number}

                }}}}";
            if ($credentialType == "guid") {
                $primaryId = $guid;
            } else {
                $primaryId = $credentialValue;
            }
            if ($graphLogLevel != "not_in_use" || $graphLogLevel == ""  ) {
                        $graphResponse = getDataFromGraph($graphQuery,"persons",$primaryId,$graphLogLevel);

                    if (empty($graphResponse->responseObject->errors)) {
                        $r_person = new stdClass;
                        $r_person->dataObj = $graphResponse->responseObject->data->persons->edges[0]->node??"";
                    }
             }
            if (empty($r_person)) {
                if ($credentialType == "guid") {
                    $r_person = eeGetEthosDataModel("persons",$credentialValue,$version);
                } else {
                    $r_person = eeGetUserFromEthos($credentialType,$credentialValue,$version);
                }
            } else {
                $r_person->count = 1;
            }
            if ($r_person->count == 1) {
                $erpUserError = "";
                $person = $r_person->dataObj;

                $this->guid = $person->id;
				$person->names[0]->fullName = $person->names[0]->firstName . ' ' . $person->names[0]->lastName;
                $this->firstName = $person->names[0]->firstName;
                $this->lastName = $person->names[0]->lastName;
                $this->fullName = $person->names[0]->fullName;
                $this->email = $person->emails[0]->address;
                //$person->emails = $person->emails??array();
                foreach ($person->emails??array() as $key => $email) {
                    if (isset($email->preference)) {
                        $this->email = $email->address;
                    }
                }
                foreach ($person->names??array() as $key => $name) {
                    if (isset($name->preference)) {
						$name->fullName = $name->firstName . ' ' . $name->lastName;
                        $this->firstName = $name->firstName;
                        $this->lastName = $name->lastName;
                        $this->fullName = $name->fullName;
                    }
                }
                $person->credentials = $person->credentials??array();
                foreach ($person->credentials as $key => $credential) {
                    if (strpos($credential->type,"UserName") !== false) {
                        $this->erpUserName = $credential->value;

                    }
                    if ($credential->type == "colleaguePersonId" || $credential->type == "bannerId" ||$credential->type == "colleagueId" ) {
                        $this->erpId = $credential->value;

                    }
                }
                foreach ($person->roles??array() as $key => $role) {
                    if (isset($role->endOn)) {
                        $endOn = strtotime($role->endOn);
                    } else {
                        $endOn = strtotime("+1 week");
                    }
                    if ($endOn > time()) {
                        $erpRoles[] = ucwords($role->role);
                    }
                }
                $this->phoneNumber = "";

                foreach ($person->phones??array() as $key => $phone) {
                    if (isset($phone->preference)) {
                        $this->phoneNumber = $phone->number??"";
                        $this->phoneNumber = preg_replace('~.*(\d{3})[^\d]{0,7}(\d{3})[^\d]{0,7}(\d{4}).*~', '($1) $2-$3', $phone->number);
                    }
                }
                //$infoPanelHtml .= '<label class="pmdynaform-label"><span class="textlabel">'.ucfirst($this->wfRole).": </strong>$this->fullName $this->email  (id: $this->erpId) (un: $this->erpUserName) </span></label>".PHP_EOL;
                $infoPanelArray[$wfRole] = ucfirst($this->wfRole).": $this->fullName $this->email  (id: $this->erpId) (un: $this->erpUserName) $this->phoneNumber ";
                if (!empty($erpRoles)) {
                    $infoPanelArray[$wfRole."ErpRoles"] = ucfirst($this->wfRole)." Roles: ". implode(", ",$erpRoles);
                }
                $aData = array( $this->wfRole.'Id' => $this->erpId ,
                                $this->wfRole.'Guid' => $this->guid ,
                                $this->wfRole.'UserName' => $this->erpUserName ,
                                $this->wfRole.'FirstName' => $this->firstName,
                                $this->wfRole.'LastName' => $this->lastName,
                                $this->wfRole.'FullName' => $this->fullName,
                                $this->wfRole.'Email' => $this->email,
                                $this->wfRole.'PhoneNumber' => $this->phoneNumber??"",
                                $this->wfRole.'Obj' => serialize($this),
                                "infoPanelArray" => $infoPanelArray,
                                "debug"=>$debug,
                                "erpUserError"=>$erpUserError
                            );
                PMFSendVariables($application, $aData);

            } else {
                $erpUserError = "Person Not Found";
                $aData = array( "debug"=>$debug,
                                "erpUserError"=>$erpUserError
                            );
                PMFSendVariables($application, $aData);
            }

       // }
    }
    public function setCredentialType($credentialType){
        $this->credentialType = $credentialType;
        return $this;

    }
    public function setRole($role){
        $this->wfRole = $role;
        return $this;

    }
    public function setCredentialValue($credentialValue){
        $this->credentialValue = $credentialValue;
        return $this;

    }

    public function setGuid($guid){
        $this->guid = $guid;
        return $this;

    }
    public function getGuid(){
        return $this->guid;

    }
    public function getFirstName(){
        return $this->firstName;
    }
    public function getlastName(){
        return $this->lastName;
    }
    public function getFullName(){
        return $this->fullName;
    }
    public function getEmail(){
        return $this->email;
    }
    public function getPhone(){
        return $this->phone;
    }
    public function getAddress(){
        return $this->address;
    }
    public function getRole(){
        return $this->wfRole;
    }


}

/**
 * @param mixed $query
 * @param string $resource
 * @param string $primaryId
 * @param string $logLevel
 * @return void|object
 * @throws Exception
 */
function getDataFromGraph($query,$resource = "", $primaryId="", $logLevel = "not_in_use") {
    $query = str_replace("'",'"',$query);
    $response = eeGetEthosDataByGraphQLQuery($query);

    if (empty($logLevel)) {
      $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
      if ($conn->connect_error) {
        $debug = "MySQL FAIL: " . $conn->connect_error;
        return;
      }
      $conn->autocommit(true);
      $responseSql = $conn->query("SELECT * FROM PMT_PROCESS_CONTROL WHERE DATA_NAME = 'Graph QL Log Level'",);
      $logLevel = $responseSql->fetch_assoc()["DATA_VALUE"];
    }

    if (($logLevel == "verbose" || $logLevel == "error") && empty($conn)) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($conn->connect_error) {
          $debug = "MySQL FAIL: " . $conn->connect_error;
          return;
        }
        $conn->autocommit(true);
    }

    $errors = $response->responseObject->errors;
    $elapsedTime = $response->elapsed;
    $type = "info";
    if ($elapsedTime > 4 || !empty($errors)) {
      $type = "error";
    }
    if ($conn && (($logLevel == "error" && $type == "error")|| $logLevel == "verbose")) {
      $msg = "Query: ".$query.PHP_EOL;
      $msg .= "Headers: ".json_encode($response->headers,JSON_PRETTY_PRINT).PHP_EOL;
      $msg .= "Errors: ".json_encode($response->responseObject->errors,JSON_PRETTY_PRINT).PHP_EOL;
      $msg .= "Response Data (truncated): ".  substr($response->responseString,0,300);
      $msg = mysqli_real_escape_string($conn,$msg);
      $logDate = date("Y-m-d H:i:s");
      $process = "graphQl";
      $sqlInsert = "INSERT INTO PMT_EWF_LOG (LOG_DATE, TYPE, PROCESS, MSG, ELAPSED_TIME, ETHOS_RESOURCE, PRIMARY_GUID) VALUES ";
      $sqlInsert .=  "('$logDate','$type','$process','$msg','$elapsedTime','$resource','$primaryId')";
      $conn->query($sqlInsert);
      $conn->close();
    }

    return $response;
  }


  function readEthosDataFromCache($resource){
        if ($GLOBALS["atat_SYS_SYS"]== "triggerHelper") {
            $token = eeGetEthosSessionToken();
            $customer = $_ENV['ethosTenant'];
            $path = '/project/ethosCache/' . $customer."/";
            $filename = $path.$resource.".dat";
            $resource = unserialize(eePHP_file_get_contents($filename));
        return $resource;
      } else {
          $filename = PATH_DOCUMENT.'cache'.PATH_SEP.$resource.".dat";
          $resource = unserialize(eePHP_file_get_contents($filename));
          //unset($resource["cacheInfo"]);
          return $resource;

      }
}


function ewfLogMsg($msg,$type = "info"){
    $wsName = eeWorkspaceName();
    $logDate = date("Y-m-d H:i:s");

    if ($wsName =="workspace display name" ) {
        print $type."-".$msg . PHP_EOL;
    } else {
        $process = "Cache Resources";
        $msg = addslashes($msg);
        $process = addslashes($process);
        $sqlInsert = "INSERT INTO PMT_EWF_LOG (LOG_DATE, TYPE, PROCESS, MSG) VALUES ('$logDate','$type','$process','$msg')";
        $response = executeQuery($sqlInsert);
    }
}

function getUidFromEmail($email = ""){
    $info = new stdClass;
    $sql = "SELECT * FROM USERS WHERE USR_EMAIL = '$email'";
    $response = executeQuery($sql);
    $info->uid =  $response[1]["USR_UID"]??"";
    $info->userName = $response[1]["USR_USERNAME"]??"";
    if (empty($info->uid) ) {
        $uid = "00000000000000000000000000000001";
    }
    return $info;
}
function getUidFromUserName($userName = ""){
  $info = new stdClass;
  $sql = "SELECT * FROM USERS WHERE USR_USERNAME = '$userName'";
  $response = executeQuery($sql);
  $info->uid =  $response[1]["USR_UID"]??"";
  $info->userName = $response[1]["USR_USERNAME"]??"";
  if (empty($info->uid) ) {
      $info->uid = "00000000000000000000000000000001";
  }
  return $info->uid;
}
function translateDateString($strDate){
	if (empty($strDate)) {
		return "";
	} else {
		return date_create(str_replace('-', '/', substr($strDate,0,10)));
	}
}