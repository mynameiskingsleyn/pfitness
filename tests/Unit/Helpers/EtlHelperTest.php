<?php

namespace Tests\Unit\Helpers;

use App\Models\Zipcode;

use App\Helpers\ETLHelper;

use Illuminate\Support\Facades\Log;

use PHPUnit\Framework\TestCase;

class EtlHelperTest extends TestCase
{

    protected $zipcode, $helper;
    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $zipcode = new Zipcode();
        $filename = 'us-zip-code-test.csv';
        $this->zipcodeEtl = new ETLHelper($filename,$zipcode);
    }
    /** @test */
    public function etl_helper_can_set_file_path()
    {
        $this->assertNull($this->zipcodeEtl->filePath);
        $this->zipcodeEtl->setFilePath();
        //Log::debug($this->zipcodeEtl->filePath);
        $this->assertNotNull($this->zipcodeEtl->filePath);
    }

}
