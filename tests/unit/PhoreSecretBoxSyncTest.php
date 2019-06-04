<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 31.05.19
 * Time: 11:41
 */

namespace Test;


use Phore\Core\Helper\PhoreSecretBoxSync;
use PHPUnit\Framework\TestCase;

class PhoreSecretBoxSyncTest extends TestCase
{


    public function testEncryptDecrypt()
    {
        $secret = phore_random_str(512);

        $encryptor = new PhoreSecretBoxSync($secret);

        $input = "ABC";

        $this->assertEquals($input, $encryptor->decrypt($encryptor->encrypt($input)));
    }


    public function testCanDecrypt ()
    {

        $mockIn = "E1-gbdCZ/8+OuCCsseS37SghBIHOhgPq15fZ+pYE6DeAAPMOwQFcKGe41fQI1UWeGpuYyJ6uDc4+vil0POQd3Fi7Nyr+mh2B7Ze6ycIcr/EKsTf5ueVumzlelxOki73WOr/3lOZYEoGdrAfKbXI0VpDdsv8vRbNw6N4HisKK79NU39VLJn2aM+gkivC09yGzUWGyeAkIJTnX5cewH6VAgxxlhKduRRN09qNKNs8d86loYNqQIGe3L/bHt1RIcECw1p0PN2oprOi0JwzTFzD8iJ1vuoKdcK+Mevj0F3yJQY9Y5cMqgdvsVHLt5ojmF+iYeGopv8VcLh82dXs7F/7hVmNz+dOUlsrgdMzD80et1JdWvuS4I0K75wKZ33m3y3LE4CzSFytP/9vngVENR814rN21+oL0UQcRvR/DnM8hnJknLEAUURIKxJFvEEzQ0Nn8AHZUpzM0hxAsTvOrElA404uLqoWfxK2Wld5G8U5eTEEJ+aaiEdxr3MnddzD+WPHeAB3hlBsg+qCN9lJL6kSbJPopgp6HSAVs1vodqku5WnlvcLpkcLk5sLblMb+uT2OBNhDmy+m4TxEAj/6W1T2DCDfZiqNtKH5Tbp+fHWpWCBVgdQXLJVlM59YMUCAUrOgt32BMVQTzb9B7p4N7pySUWGZTXXKbpOvHDX5beaOhX2WpfuW92H5eliF1vRjLdY8uY3SSkLhd1+uQPkYwmp/T2dsaJze0uOsWnjElWeCNyCpqRdBev46grStRWnUB0t6S0OUlR53Z+/3MMyap3fmx51Zu3z7SzSBp2LOf050qD2lX7hZcSkzBKUCXBYTEfN2p5H+6RuLYW9lLIsUYCf42ctLmDiGC7MKfT3RzGBMTOMrKgLq9Tva+QebZNqNAISDdOoF9iRzGW/mfyfx6VU5sd1lCkcNpbAV9t2/2d+aRzFdl5iSCVZxVQL0gK4UQpHIt2DkIvFel59zRiMRmbH5HI3L+XtzPI24q2KEoLJDhZUhQl6ClIWp3H5G5/VJGGvOS1DtM9HHuFCrQb5NsVUaRqSQOiRb7kk3QyuBpdcxzl95qpfarg0MGSosf4ewnjMwKcBK1PegVUkHRXNqrF6vWnXJEaJlxzdawqUaHGbF3O+0WnrrEBoQODmpc3mQPIRLbEmzZcDPTSwhuRIaUFbFNVQV9keCUNLMpysSrVyj+aQBTf3C8CzatApybOzSzdnChUliEMgBWjOvBSSLLQk3IOFzDtbda7ea1xg2fGslWIEwTMg6TMsuCz9U9UJcyYMNT2TpHkETXijHBVirF9jXqKBnzOjr9ihw1+QS+Lo2anxvyzQ80kxNVZG1S74+HqaNrY0meoPRY+YtquRmQeLD6hVNIn+J8tcRYpBxNVn6OGV6wsUVweLPXfm4PAagCuvAJvjO+J7YNXaFYKg=";

        $encryptor = new PhoreSecretBoxSync("SECRET_KEY");
        $encryptor->encrypt("SECRET");


        $this->assertEquals("SECRET", $encryptor->decrypt($mockIn));
    }

}
