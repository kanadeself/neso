<?php
class Idol {
    public int $Id;
    public string $Name;
    public string $NameJP;
    public string $FullName;
    public string $Color;
    public string $Franchise;
    public array $Nesos;

    public function __construct($row) {
        $this->Id = intval($row["IdolID"]);
        $name = explode(" ", $row["IdolName"]);
        $this->Name = implode("", $name);
        $this->FullName = $row["IdolName"];
        $this->NameJP = $row["IdolNameJP"];
        $this->Color = $row["Color"];
        $this->Nesos = [];
        $this->Franchise = $row["franchise"];
    }
}
?>