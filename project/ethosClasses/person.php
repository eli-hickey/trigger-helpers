<?php
class person
{
    public $credentials;
public $dateOfBirth;
public $gender;
public $id;
public $names;
public $privacyStatus;

   function __construct($guid)
    {
        $this->guid = $guid;
        $this->response = eeGetEthosDataModel("persons",$this->guid);
        $this->populateProperties();
    }

    protected function populateProperties(){
        if (is_array($this->response->dataObj)) {
            foreach ($this->response->dataObj as $objKey => $item) {
                foreach ($item as $itemKey => $value) {
                    $this->$itemKey = $value;
                    }
            }
        }
        if (is_object($this->response->dataObj)) {
            foreach ($this->response->dataObj as $key => $item) {
                            $this->$key = $item;

            }
        }

    }
}