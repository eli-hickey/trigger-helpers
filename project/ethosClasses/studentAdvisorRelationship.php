<?php
class studentAdvisorRelationship
{
    public $advisor;
public $advisorType;
public $assignedPriority;
public $id;
public $startAcademicPeriod;
public $startOn;
public $student;

   function __construct($guid)
    {
        $this->guid = $guid;
        $this->response = eeGetEthosDataModel("student-advisor-relationships",$this->guid);
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