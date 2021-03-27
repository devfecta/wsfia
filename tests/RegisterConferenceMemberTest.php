<?php 
declare(strict_types=1);
require_once('./Configuration.php');
require_once('./models/RegisterConferenceMember.php');
use PHPUnit\Framework\TestCase;

final class RegisterConferenceMemberTest extends TestCase
{
    private $user;

    public function testSetLicenseNumber(): void
    {
        $RegisterConferenceMember = new RegisterConferenceMember();
        $this->assertTrue($RegisterConferenceMember->setLicenseNumber("test"));
    }
    
}
?>