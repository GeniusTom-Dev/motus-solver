<?php
class GridGest {

    private $round = 0;

    public function getRound() {
        return $this->round;
    }

    public function formatGrid($html) {
        $doc = new DOMDocument();
        $doc->loadHTML($html);

        $ligneElements = $doc->getElementsByTagName('div');

        $motusArray = [];

        foreach ($ligneElements as $ligne) {
            $row = [];
            $cellElements = $ligne->getElementsByTagName('div');

            foreach ($cellElements as $cell) {
                $letterElement = $cell->getElementsByTagName('div')->item(0);

                if(!is_null($letterElement)){
                    $letter = strtolower($letterElement->nodeValue);
                    $letterClass = $letterElement->getAttribute('class');
                    if(str_contains($letterClass, "orange") || str_contains($letterClass, "green")){
                        $row[] = ["letter" => $letter, "color" => $letterClass];
                    }else{
                        $row[] = ["letter" => $letter, "color" => ""];
                    }
                }
            }


            if(!empty($row)){
                $motusArray[] = $row;
            }
        }

        $this->round = $this->determineRound($motusArray);

        return $motusArray;
    }

    private function determineRound($motusArray) {
        $localRound = 0;
        foreach ($motusArray as $row) {
            if(empty($row[0]["letter"])){
                return $localRound;
            }
            $localRound++;
        }
        return 7;

    }

}