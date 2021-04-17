<?php 
declare(strict_types=1);
require_once('./Configuration.php');
require_once('./models/RegisterConferenceMember.php');
use PHPUnit\Framework\TestCase;

final class RegisterConferenceMemberTest extends TestCase
{

    public function testSetAttendingDate(): void
    {
        $data = array(
            "sessionId" => "test-data-123"
            , "emailAddress" => "kkelm@outlook.com"
            , "attendingDate" => "Tuesday"
            , "attendingChecked" => "false");
        $RegisterConferenceMember = new RegisterConferenceMember();
        $this->assertTrue($RegisterConferenceMember->setAttendingDate($data));

        $data = array(
            "sessionId" => "test-data-123"
            , "emailAddress" => "kkelm@outlook.com"
            , "attendingDate" => "Thursday"
            , "attendingChecked" => "true");
        $RegisterConferenceMember = new RegisterConferenceMember();
        $this->assertTrue($RegisterConferenceMember->setAttendingDate($data));
    }

    public function testSetCEU(): void
    {
        $data = array(
            "sessionId" => "test-data-123"
            , "emailAddress" => "kkelm@outlook.com"
            , "ceu" => "true");
        $RegisterConferenceMember = new RegisterConferenceMember();
        $this->assertTrue($RegisterConferenceMember->setCEU($data));
    }

    public function testSetLicenseType(): void
    {
        $data = array(
            "sessionId" => "test-data-123"
            , "emailAddress" => "kkelm@outlook.com"
            , "licenseType" => "Type123");
        $RegisterConferenceMember = new RegisterConferenceMember();
        $this->assertTrue($RegisterConferenceMember->setLicenseType($data));
    }

    public function testSetLicenseNumber(): void
    {
        $data = array(
            "sessionId" => "test-data-123"
            , "emailAddress" => "kkelm@outlook.com"
            , "licenseNumber" => "License123");
        $RegisterConferenceMember = new RegisterConferenceMember();
        $this->assertTrue($RegisterConferenceMember->setLicenseNumber($data));
    }
    
}
?>