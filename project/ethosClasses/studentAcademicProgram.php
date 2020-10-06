<?php
class studentAcademicProgram
{
    public $academicLevel;
public $academicPeriods;
public $associatedCurriculum;
public $catalog;
public $credentials;
public $curriculumObjective;
public $disciplines;
public $endOn;
public $enrollmentStatus;
public $id;
public $program;
public $programOwner;
public $startOn;
public $student;

   function __construct($guid)
    {
        $this->guid = $guid;
        $this->response = eeGetEthosDataModel("student-academic-programs",$this->guid);
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