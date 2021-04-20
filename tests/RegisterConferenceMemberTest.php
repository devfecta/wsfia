<?php 
declare(strict_types=1);
require_once('./Configuration.php');
require_once('./models/RegisterConferenceMember.php');
use PHPUnit\Framework\TestCase;

final class RegisterConferenceMemberTest extends TestCase
{
/* Session Data
    {
        "id": "WSFIA-10919", 
        "areas": ["2"], 
        "userId": "1", 
        "jobTitle": "Fire Inspector", 
        "lastName": "Kelm", 
        "firstName": "Kevin", 
        "studentId": "", 
        "businesses": ["270"], 
        "conference": {
            "ceu": "true", 
            "attending": {
                "Friday": "true", 
                "Monday": "true", 
                "Tuesday": "true", 
                "Thursday": "true", 
                "Wednesday": "true"
            }, 
            "licenseType": "test1", 
            "licenseNumber": "111"
        }, 
        "emailAddress": "kkelm@outlook.com"
    }
*/

    public $currentSession = array("sessionId" => "test-data-123");

    public function testAddConferenceCurrentMembers(): void {

        $this->currentSession["memberIds"] = "WSFIA-10919,WSFIA-431121329";

        $RegisterConferenceMember = new RegisterConferenceMember();
        $this->assertTrue($RegisterConferenceMember->addConferenceCurrentMembers($this->currentSession));
    }

    public function testSetAttendingDate(): void
    {
        $this->currentSession["emailAddress"] = "kkelm@outlook.com";
        $this->currentSession["attendingDate"] = "Tuesday";
        $this->currentSession["attendingChecked"] = "false";
        
        $RegisterConferenceMember = new RegisterConferenceMember();
        $this->assertTrue($RegisterConferenceMember->setAttendingDate($this->currentSession));

        $this->currentSession["attendingDate"] = "Thursday";
        $this->currentSession["attendingChecked"] = "true";

        $RegisterConferenceMember = new RegisterConferenceMember();
        $this->assertTrue($RegisterConferenceMember->setAttendingDate($this->currentSession));
    }

    public function testSetCEU(): void
    {
        $this->currentSession["emailAddress"] = "kkelm@outlook.com";
        $this->currentSession["ceu"] = "true";

        $RegisterConferenceMember = new RegisterConferenceMember();
        $this->assertTrue($RegisterConferenceMember->setCEU($this->currentSession));
    }

    public function testSetLicenseType(): void
    {
        $this->currentSession["emailAddress"] = "kkelm@outlook.com";
        $this->currentSession["licenseType"] = "Type123";

        $RegisterConferenceMember = new RegisterConferenceMember();
        $this->assertTrue($RegisterConferenceMember->setLicenseType($this->currentSession));
    }

    public function testSetLicenseNumber(): void
    {
        $this->currentSession["emailAddress"] = "kkelm@outlook.com";
        $this->currentSession["licenseNumber"] = "License123";

        $RegisterConferenceMember = new RegisterConferenceMember();
        $this->assertTrue($RegisterConferenceMember->setLicenseNumber($this->currentSession));
    }
    
}
?>