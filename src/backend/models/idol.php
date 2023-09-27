<?php
class Idol {
    public int $Id;
    public string $Name;
    public string $NameJP;
    public string $FullName;
    public string $Color;
    public array $Nesos;

    public function __construct($row) {
        $this->Id = intval($row["IdolID"]);
        $this->Name = explode(" ", $row["IdolName"])[0];
        $this->FullName = $row["IdolName"];
        $this->NameJP = $row["IdolNameJP"];
        $this->Color = $row["Color"];
        $this->Nesos = [];
    }
}
?>