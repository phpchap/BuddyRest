<?php
$ds = DIRECTORY_SEPARATOR; 
require dirname(__FILE__) . $ds . "..".$ds."src".$ds."api".$ds."UserAPI.php";


class UserApiTest extends PHPUnit_Framework_TestCase 
{
    private $u;
    
    public function setUp()
    {
        $this->u = new UserAPI();
    }
    
    // make sure we can run tests
    public function testAssertion() 
    {
        $this->assertEquals(true, true);
    }
}
