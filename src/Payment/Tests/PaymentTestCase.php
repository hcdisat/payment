<?php namespace HcDisat\Payment\Tests;

use Tests\TestCase;

abstract class PaymentTestCase extends TestCase
{
    use PaymentTestActions;
    
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */

    public function setUp()
    {
        parent::setUp();
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @return string
     */
    private function getBootstrapPath()
    {
        $bootstrapName = 'bootstrap';
        $apName = 'app.php';
        $baseDir = __DIR__;
        $allDirs = explode('/', $baseDir);

        do
        {
            $dirs = scandir($baseDir);
            if( in_array($bootstrapName, $dirs)) {
                $path = $baseDir . DIRECTORY_SEPARATOR . $bootstrapName . DIRECTORY_SEPARATOR. $apName;
                if( file_exists($path)) {
                    return $path;
                }
            }

            $baseDir = str_replace(DIRECTORY_SEPARATOR.array_pop($allDirs), '', $baseDir);

        }
        while(true);
    }
}
