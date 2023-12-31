<?php
class Neso {
    public int $Id;
    public string $Name;
    public string $Size;
    public string $ImageFileName;
    public string $IdolName;
    public string $DisplayName;
    public string $Exclusive;
    public string $ActualSize;
    public string $ReleaseYear;
    public int $OwnedBy;
    

    public function __construct($row, $preflang) {
        
        if ($preflang === 'ja') {
            $this->DisplayName = $row["IdolNameJP"];
        } else {
            $this->DisplayName = $row["IdolName"];
        }

        if ($preflang === 'ja') {
            $this->Name = $row["NesoNameJP"];
        } else {
            $this->Name = $row["NesoName"];
        }
        
        $this->IdolName = $row["IdolName"];
        $this->Id = intval($row["NesoID"]);

        if ($preflang === 'ja') {
            $this->Size = $row["SizeJP"];
        } else {
            $this->Size = $row["Size"];
        }

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

        if(isset($row["OwnedBy"])) {
            $this->OwnedBy = $row["OwnedBy"];
        }
        
    }
}
?>