<?php 
declare(strict_types=1);
require_once('./Configuration.php');
require_once('./models/Membership.php');
use PHPUnit\Framework\TestCase;

require './vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer;

final class MembershipTest extends TestCase
{

    public function testExportMembersInfo(): void
    {
        $Membership = new Membership();
        $client = new Google_Client();
        $client->setAuthConfigFile('../drive.json');

        

        //$client->setRedirectUri('http://localhost:3000/reports/members');
        $client->setRedirectUri('http://localhost/api.php?class=Membership&method=exportMemberInfo');
        $client->addScope("https://www.googleapis.com/auth/drive");


        if (isset($_GET['code'])) {
            error_log(date('Y-m-d H:i:s') . " Get Code: " . $_GET['code'] . "\n", 3, "/var/www/html/php-errors.log");
            $accessToken = $client->authenticate($_GET['code']);
            $client->setAccessToken($accessToken);
            $client->setAccessType("offline");
            $service = new Google_Service_Drive($client);
            $file = new Google_Service_Drive_DriveFile();

            $this->assertTrue($Membership->exportMembersInfo($service, $file));
        }
        else {
            $auth_url = $client->createAuthUrl();
            header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
        }

        
    }
    
}
?>