<?php
/**
 * @namespace   MemoryUsageTest.php
 * @copyright   (c) 2012 Gjero Krsteski http://www.krsteski.de/
 */
class MemoryUsageTest extends PHPUnit_Framework_TestCase
{
  public function testCreatingNewInstance()
  {
    new Pimf_Util_MemoryUsage(true);
  }

  public function testAllocatingAndPrintingTheInformation()
  {
    $this->expectOutputRegex('/Peak of memory usage: /i');

      // Create new MemoryUsageInformation class
      $memoryUsage = new Pimf_Util_MemoryUsage(true);
      // Set start
      $memoryUsage->setStart();
      // Set memory usage before loop
      $memoryUsage->setMark('Before Loop');


         // Create example array
         $a = array();

         // Fill array with
         for($i = 0; $i < 1000; $i++) {
            $a[$i] = uniqid();
         }


      // Set memory usage after loop
      $memoryUsage->setMark('After Loop');


         // Unset array
         unset($a);


      // Set memory usage after unset
      $memoryUsage->setMark('After Unset');
      // Set end
      $memoryUsage->setEnd();
      // Print memory usage statistics
      $memoryUsage->printInformation();
  }
}
