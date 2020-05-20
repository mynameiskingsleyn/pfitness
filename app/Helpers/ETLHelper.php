<?php

namespace App\Helpers;

use App\Traits\StorageTrait;
use Illuminate\Support\Facades\Log;

class ETLHelper
{
    use StorageTrait;
    public $headerRead;
    public $filePath;


    public function __construct($fileName,$model,$seperator =';')
    {
        $this->batchSize = 5000;
        $this->numberOfBatch = 0;
        $this->headerRead = false;
        $this->fileName = $fileName;
        $this->model = $model;
        $this->seperator = $seperator;
        $this->modelHeaders = $model->getEtlFields();
        $this->disk = 'etl';

    }

    public function setHeaders()
    {
        $this->setFilePath($this->fileName);
        $file_handle = fopen($this->filePath, 'r');
        $counter = 0;
        while(!feof($file_handle) && $counter < 1){
            $line = fgetcsv($file_handle,2024,$this->seperator);
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
        //\Log::info('Set file Path');
        $this->filePath =  $this->getFileFullPath();
    }

    public function readCSV()
    {
        $file_handle = fopen($this->filePath, 'r');
        $line_of_text = [];
        $insertValues = [];
        $counter = 0;
        $batchCounter =0;
        $unSetHeader = array_search(100,$this->modelHeaders);
        if(empty($this->modelHeaders) || !empty($unSetHeader)){
            return false;
        }
        if($this->modelHeaders)
        if($file_handle !== FALSE ){
            while(!feof($file_handle)){
                if($this->headerRead){
                    $line_of_text[$counter] = fgetcsv($file_handle,1024,$this->seperator);
                    $values = $this->setInserter($line_of_text[$counter]);
                    if(!empty($values)){
                        $insertValues[] = $values;
                    }

                    if($batchCounter >= $this->batchSize-1){
                        $this->insert($insertValues);
                        $insertValues = [];
                        $batchCounter = 0;

                    }else{
                        $batchCounter++;
                    }
                    $counter++;
                }else{
                    $header = fgetcsv($file_handle,1024,$this->seperator);
                    $this->headerRead = true;
                }

            }
            fclose($file_handle);
        }
        if(!empty($insertValues) && count($insertValues) > 0){
            $this->insert($insertValues);
        }
        \Log::info('number of batches ='.$this->numberOfBatch.', number of recodrs inserted ='.$counter);
        return true;
    }

    public function setInserter($line)
    {
        $insert = [];
        if(is_array($line) && !empty($line)){
            foreach($this->modelHeaders as $key=>$val){
                if(is_array($line)){
                    $insert[$key] = isset($line[$val]) ? $line[$val] : '';
                }

            }
            $insert['created_at'] = \Carbon\Carbon::now();
            $insert['updated_at'] = \Carbon\Carbon::now();
        }

        return $insert;
    }

    public function insert($array)
    {
        $this->numberOfBatch++;
        $this->model->insert($array);
        \Log::debug("number of record in batch ".count($array));
    }

    public function getRecordCount()
    {
        $linecount = 0;
        if(!$this->setFilePath())
            $this->setFilePath($this->fileName);

        $file_handle = fopen($this->filePath, 'r');
        while(!feof($file_handle)){
            $line = fgetcsv($file_handle,2024,$this->seperator);
            $linecount ++;
        }
        fclose($file_handle);
        if(!$this->headerRead){
            $linecount--;
        }
        return $linecount;
    }

    public function clearTable()
    {
        $this->model->query()->truncate();
        $table = $this->model->getTable();
        return "Truncated $table table";
    }

}