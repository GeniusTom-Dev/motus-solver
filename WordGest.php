<?php

class WordGest {
    private $allWords = [];
    private $filteredWords = [];

    public function __construct() {
        $file = fopen("Lexique383.tsv", "r");

        while (!feof($file)) {
            $line = fgets($file);
            $line = explode("\t", $line);

            $word = str_replace("î", "i", $line[0]);
            $word = str_replace("ô", "o", $word);
            $word = str_replace(["é","è", "ê"], "e", $word);
            $word = str_replace(["à","â"], "a", $word);

            if(empty($word)) {
                continue;
            }
            $this->allWords[] = $word;
        }

        fclose($file);

        $this->allWords = array_unique($this->allWords);

        sort($this->allWords);
    }

    public function getAllWords() {
        return $this->allWords;
    }

    public function getFilteredWords() {
        return $this->filteredWords;
    }

    public function createFilteredArray($letterStart, $size) {
        $this->filteredWords = array_filter($this->allWords, function($word) use ($letterStart) {
            if(empty($word[0])) {
                var_dump($word[0]);
                exit;
            }
            return $word[0] === $letterStart;
        });

        $this->filterArrayByWordSize($size);
    }

    public function filterArrayByWordSize($size) {
        $this->filteredWords = array_filter($this->filteredWords    , function($word) use ($size) {
            return strlen($word) === $size;
        });
    }

    public function filterArrayByLetterPos($letter, $pos){
        $this->filteredWords = array_filter($this->filteredWords, function($word) use ($letter, $pos) {
            if(strlen($word) < $pos) {
                return false;
            }

            return $word[$pos-1] === $letter;
        });
    }

    public function filterArrayByLetterInWordBadPosition($letter, $badPos){
        $this->filteredWords = array_filter($this->filteredWords, function($word) use ($letter, $badPos) {
            for ($i = 1; $i < strlen($word); $i++) {
                if($i !== $badPos - 1 && $word[$i] === $letter) {
                    return true;
                }
            }
            return false;
        });
    }

    public function filterArrayByLetterInPosition($letter, $pos) {
        $this->filteredWords = array_filter($this->filteredWords, function($word) use($letter, $pos) {
            for ($i = 1; $i < strlen($word); $i++) {
                if($i == $pos - 1 && $word[$i] === $letter) {
                    return true;
                }
            }
            return false;
        });

    }

    public function filterArrayByLetterNotInWord($letter) {
        $this->filteredWords = array_filter($this->filteredWords, function($word) use($letter) {
            return !str_contains($word, $letter);
        });
    }
}

