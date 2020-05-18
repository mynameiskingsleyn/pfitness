<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\ETLHelper;
use App\Models\Zipcode;

class CronEtlLoad extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:Etl
                            {modelname: Name of the model}
                            {filename: name of the file for etl} 
                            {type: what to call the operation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs Etl for application csv files with inputs modelName filenam type';

    protected $etlHelper;
    protected $filename;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $modelName = strtolower($this->argument('modelname: Name of the model'));
        $file = $this->argument('filename: name of the file for etl');
        $type = $this->argument('type: what to call the operation');

        switch ($modelName){
            case 'zipcode':
                $model = new Zipcode();
                break;
            default:
                $model = Null; //new Zipcode();
        }

        //$model = new Zipcode();
        $filename =  $file ?? 'us-zip-code-test.csv';
        $loading = $type ?? 'zipcode';
        $this->loadFileToDB($type, $model, $filename);

        $this->line("filename is $filename, model name is $modelName, option is $type ");
    }

    /**  load CSV file to db db */
    public function loadFileToDB($loading=null, $model = null, $filename=null)
    {
        //$filename = 'us-zip-code-test.csv';
        //$filename = 'us-zip-code-latitude-and-longitude.csv';
        $this->filename = $filename;
        $this->model = $model;
        $this->loading = $loading;
        $codeEtl = new ETLHelper($this->filename,$this->model);
        $this->etlHelper = $codeEtl;
        $this->etlHelper->setHeaders();
        $this->line("preparing to Load $loading");
        \Log::info("preparing to Load $loading");
        $records = $this->records = $this->etlHelper->getRecordCount();
        $this->line("number of records to be loaded is $records");
        \Log::info("number of records to be loaded is $records");

        \Log::info('<-------------Loading process for  '.$loading .'  started ---------------->');
        $this->line('<-------------Loading process for  '.$loading .'  started ---------------->');
        try{
            if($this->filename && $this->model && $this->etlHelper){
                //truncate table..
                $this->clearTable();
                // run reading
                $insertValues = $this->readCSV();
            }
            //$insertValues = true; //$this->readCSV//$zipcodeEtl->readCSV();
        }catch(\Exception $e){
            \Log::Error( "Error loading $loading.... ".$e->getMessage());
            $this->line("Error loading $loading.... ");
            exit(0);
        }

        if(!$insertValues){
            $this->line("Insert was not successuful for $loading");
            \Log::info("Insert was not successuful for $loading");

        }else{
            $this->line("Insert was successuful for $loading");
            \Log::info("Insert was successuful for $loading");
        }

        //dd($insertValues);


    }

    /** Read the CSV file and populate */
    public function readCSV()
    {
        $count = $this->records ?? 0;
        \Log::info("--------------------ETL Process for $this->loading Started--------------------");
        $this->line("--------------------ETL Process for $this->loading Started--------------------");
        $file_handle = fopen($this->etlHelper->filePath, 'r');
        $line_of_text = [];
        $insertValues = [];
        $counter = 0;
        $batchCounter =0;
        $unSetHeader = array_search(100,$this->etlHelper->modelHeaders);
        if(empty($this->etlHelper->modelHeaders) || !empty($unSetHeader)){
            return false;
        }
        $bar = $this->output->createProgressBar($count);
        $bar->start();
        if($this->etlHelper->modelHeaders)
            if($file_handle !== FALSE ){
                while(!feof($file_handle)){
                    if($this->etlHelper->headerRead){
                        $line_of_text[$counter] = fgetcsv($file_handle,1024,$this->etlHelper->seperator);
                        $values = $this->etlHelper->setInserter($line_of_text[$counter]);
                        $bar->advance();
                        if(!empty($values)){
                            $insertValues[] = $values;
                        }

                        if($batchCounter >= $this->etlHelper->batchSize-1){
                            $this->etlHelper->insert($insertValues);
                            $insertValues = [];
                            $batchCounter = 0;

                        }else{
                            $batchCounter++;
                        }
                        $counter++;

                    }else{
                        $header = fgetcsv($file_handle,1024,$this->etlHelper->seperator);
                        $this->etlHelper->headerRead = true;
                    }

                }
                fclose($file_handle);
            }
        if(!empty($insertValues) && count($insertValues) > 0){
            $this->etlHelper->insert($insertValues);
        }
        $bar->finish();
        \Log::info("--------------------ETL Process for $this->loading Completed--------------------");
        $this->line('');
        $this->info("--------------------ETL Process for $this->loading Completed--------------------");
        \Log::info('number of batches ='.$this->etlHelper->numberOfBatch.', number of recodrs inserted ='.$counter);
        return true;
    }

    public function clearTable()
    {

        $result = $this->etlHelper->clearTable();
        $this->line($result);
        \Log::info($result);
    }


}
