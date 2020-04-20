<?php

namespace App\Helpers;

use App\Traits\StorageTrait;

class ETLHelper
{
    protected $headerRead;
    public function __construct($fileName,$model,$seperator =';')
    {
        $this->headerRead = false;
        $this->fileName = $fileName;
        $this->model = $model;
        $this->seperator = $seperator;
        $this->modelHeaders = $model->getEtlFields();
        $this->disk = 'etl';

    }

    public function getHeaders()
    {
        $this->csvFile = $this->getFilePath($this->fileName);
        $file_handle = fopen($this->csvFile, 'r');
        $counter = 0;
        while(!feof($file_handle) && $counter < 1){
            $line = fgetcsv($file_handle,1024,$this->seperator);
            $counter ++;
        }
        fclose($file_handle);
        $this->mapHeaders($line);
    }

    public function mapHeaders($line)
    {
        foreach($line as $key=>$value){
            if(array_key_exists($value,$this->modelHeaders)){
                $this->modelHeaders[$value] = $key;
            }
        }
    }

    public function setFilePath()
    {
        $this->filePath =  $this->getFileFullPath();
    }

    public function readCSV()
    {
        $file_handle = fopen($this->filePath, 'r');
        $line_of_text = [];
        $insertValues = [];
        $counter = 0;
        $unSetHeader = array_search(100,$this->modelHeaders);
        if(empty($this->modelHeaders) || !empty($unSetHeader)){
            return false;
        }
        if($this->modelHeaders)
        if($file_handle !== FALSE ){
            while(!feof($file_handle)){
                if($this->headerRead){
                    $line_of_text[$counter] = fgetcsv($file_handle,1024,$this->seperator);
                    $insertValues[$counter] = $this->setInserter($line_of_text[$counter]);
                    $counter++;
                }else{
                    $header = fgetcsv($file_handle,1024,$this->seperator);
                    $this->headerRead = true;
                }

            }
            fclose($file_handle);
        }

        return $insertValues;
    }

    public function setInserter($line)
    {
        $insert = [];
        foreach($this->modelHeaders as $key=>$val){
            $insert[$key] = $line[$val];
        }
        return $insert;
    }

}