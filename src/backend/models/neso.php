<?php
class Neso {
    public int $Id;
    public string $Name;
    public string $Size;
    public string $ImageFileName;
    public string $IdolName;
    public string $Exclusive;
    public string $ActualSize;
    public string $ReleaseYear;

    public function __construct($row, $IdolName) {
        $this->Id = intval($row["NesoID"]);
        $this->Name = $row["NesoName"];
        $this->Size = $row["Size"];
        if(isset($row["ActualSize"])) {
            $this->ActualSize = $row["ActualSize"];
        } 
        if(isset($row["ReleaseYear"])) {
            $this->ReleaseYear = $row["ReleaseYear"];
        } 
        if(isset($row["Exclusive"])) {
            $this->Exclusive = $row["Exclusive"];
        } 

        $this->ImageFileName = $row["ImageFileName"];
        $this->IdolName = $IdolName;
    }
}
?>