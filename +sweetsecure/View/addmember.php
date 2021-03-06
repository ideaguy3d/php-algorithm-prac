<?php
// assign defaults
//date_default_timezone_set('Europe/London');
$mailStatus = '';

$data = [
    'email' => 'email',
    'firstname' => 'Julius',
    'lastname' => 'Alvarado',
    'address' => '117 s 9th st',
    'postcode' => '95112',
    'city' => 'San Jose',
    'stateProv' => 'California',
    // *** validation: implement database lookup for "country" field
    // ***             use the "iso_country_codes" table
    'country' => 'country',
    'dob' => '',    // can be built from next 3 fields
    'dobyear' => 0,
    'dobmonth' => 0,
    'dobday' => 0,
    'telephone' => 'telephone',
    'password' => 'password',
    'photo' => 'http://localhost/img/hack.png',
];

// *** this is not being used, but needs to be implemented as part of the validation scheme
$error = [
    'email' => '',
    'firstname' => '',
    'lastname' => '',
    'address' => '',
    'city' => '',
    'stateProv' => '',
    'country' => '',
    'postcode' => '',
    'telephone' => '',
    'dob' => '',
    'password' => '',
    'photo' => '',
];

require './Model/Members.php';
$member = new Members();

// implementing country code validation
$pdo = $member->getPdo();
$statement = $pdo->query('select * from iso_country_codes');
$selectedCountry = $_POST['data']['country'] ?? null;
$countrySelect = '<select name="data[country]">' . PHP_EOL;
$countryValidate = [];
while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
    $countryValidate [] = $row['iso2'];
    $countrySelect .= "<option value='{$row['iso2']}'";
    $countrySelect .= ($selectedCountry == $row['iso2']) ? 'selected' : '';
    $countrySelect .= ">{$row['name']}</option>" . PHP_EOL;
}
$countrySelect .= '</select>' . PHP_EOL;

if(isset($_POST['data'])) {
    $data = $_POST['data'];
    $valid = true;
    
    //-- first and last:
    $validateName = function($key) use (&$error, &$valid, $data) {
        $name = $data[$key];
        if(!preg_match('/^[a-z,. ]+$/i', $name)) {
            $error[$key] = 'Only letters, commas, and periods are allowed';
            $valid = false;
        }
        else if(strlen($name) > 128) {
            $error[$key] = 'Only allowed to have 128 characters';
            $valid = false;
        }
        return null;
    };
    
    $validateAddressComponents = function($key, $limit) use (&$error, &$valid, $data) {
        if(!ctype_alnum(str_replace([' ', ',', '.'], '', $data[$key]))) {
            $error[$key] = "$key can only have letters, numbers, commas, and periods";
            $valid = false;
        }
        else if(strlen($data[$key]) > $limit) {
            $error[$key] = "$key can only be $limit characters long";
            $valid = false;
        }
    };
    
    // *** filtering: need to remove unwanted tags from incoming data
    if(isset($data['dobyear']) && isset($data['dobmonth']) && isset($data['dobday'])) {
        try {
            // sprintf() filters user input
            $bdateString = sprintf('%4d-%02d-%02d', $data['dobyear'], $data['dobmonth'], $data['dobday']);
            $bdate = new DateTime($bdateString);
            $today = new DateTime();
            $interval21 = new DateInterval('P21Y');
            $bdate21 = $today->sub($interval21);
            if($bdate > $bdate21) {
                $error['dob'] = 'Must be over 21 years old';
                $valid = false;
            }
            
            // final dob doesn't directly use user input
            $data['dob'] = $bdate->format('Y-m-d H:i:s');
        }
        catch(Exception $e) {
            // *** validation: need to add info to $error['dob']
            // *** security: consider logging the error message rather than displaying it
            //echo $e->getMessage();
            error_log($e->getMessage(), 0);
            header('Location: /');
            exit;
        }
    }
    else {
        // *** validation: need to add info to $error['dob']
        $error['dob'] = 'Unable to add dob info';
        $valid = false;
    }
    
    // filter all input with strip_tags()
    foreach($data as $key => $value) {
        $date[$key] = trim(strip_tags($value));
    }
    
    // *** security: all incoming data should be filtered and/or validated!
    // *** validation: place error information into $error[] array
    /*
        // Example:
        if (!preg_match('/^[a-z][a-z0-9._-]+@(\w+\.)+[a-z]{2,6}$/i', $data['email'])) {
            $error['email'] = '<b class="error">Invalid email address</b>';
        }
    */
    
    //-- email:
    if(filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $error['email'] = 'Invalid email entered';
        $valid = false;
    }
    
    //-- first & last:
    $validateName('firstname');
    $validateName('lastname');
    
    //-- photo:
    if(!preg_match('/^.+\.(jpg|png|gif)$/', $data['photo'])) {
        $error['photo'] = 'Photo must be jpg, png, or gif';
        $valid = false;
    }
    else if(strlen($data['photo']) > 128) {
        $error['photo'] = 'Photo URL can only be 128 characters long';
        $valid = false;
    }
    
    // address, city, state:
    $validateAddressComponents('address', 255);
    $validateAddressComponents('city', 64);
    $validateAddressComponents('stateProv', 32);
    
    //-- country: is using the result set from the db
    if(isset($data['count'])) {
        $countryKey = array_search($data['country'], $countryValidate, true);
        if(false === $countryKey) {
            $error['country'] = 'Country not found';
            $valid = false;
        }
        else {
            // setting country to a value from the db
            $data['country'] = $countryValidate[$countryKey];
        }
    }
    
    //-- postcode:
    $postCode = strtoupper($data['postcode']);
    if(!ctype_alnum(str_replace([' ', '-'], '', $postCode))) {
        $error['postcode'] = 'Only letters, numbers, and dashes are allowed';
        $valid = false;
    }
    else if(strlen($postCode) > 10) {
        $error['postcode'] = 'Postcode can only be 10 digits long';
        $valid = false;
    }
    
    //-- phone number:
    if(!ctype_digit(str_replace([' ', '-'], '', $data['telephone']))) {
        $error['telephone'] = 'Phone number can only have spaces, numbers, and dashes';
        $valid = false;
    }
    
    //-- password:
    if(!(isset($data['password']) && !empty($data['password']))) {
        $error['password'] = 'A password is required';
        $valid = false;
    }
    
    // *** validation: check to see if form data is valid before sending email and adding data
    if($valid) {
        // add data and retrieve last insert ID
        $newId = $member->add($data);
        
        try {
            // send email confirmation
            $address = "phpninja@mail.com";
            $newName = $data['firstname'] . ' ' . $data['lastname'];
            require_once('./PHPMailer/class.phpmailer.php');
            $mail = new PHPMailer();
            $body = "Welcome $newName ! <br> Enjoy your new membership";
            $mail->AddReplyTo($address, 'HackMatch.io');
            $mail->SetFrom($address, 'HackMatch.io');
            $mail->AddAddress($data['email'], $newName);
            $mail->AddBCC($address, 'HackMatch.io');
            $mail->Subject = 'HackMatch.io membership';
            $member->confirm($newId, $data);
            $mail->AltBody = "Visit HackMatch.io to learn more";
            $mail->MsgHTML($body);
            if(!$mail->Send()) {
                $mailStatus = 'Mailer Error: ' . $mail->ErrorInfo;
            }
            else {
                $mailStatus = 'Confirmation Email sent';
            }
        }
        catch(\Throwable $e) {
            error_log($e->getMessage(), 3, './email_error.log');
        }
        
        // *** redirect to the confirmation page if valid or if 1st time
        // Header('Location: ./confirmation');
    }
    
}
?>

<div class="content">
    <br/>
    <div class="product-list">
        <h2>Sign Up</h2>
        <br/>

        <b>Please enter your information.</b>
        <br/>
        <br/>
        <?php if($mailStatus) echo '<br /><b class="confirm">', $mailStatus, '</b><br />'; ?>
        <br/>

        <form action="?page=addmember" method="POST">
            <!-- Birthday DOB -->
            <p class="app-input-birthday">
                <!-- // *** validation: birthdate validation is already done -->
                <label>Birthdate: </label>

                <!-- Year -->
                <select name="data[dobyear]">
                    <?php if($data['dobyear']) {
                        echo '<option>', $data['dobyear'], '</option>';
                    } ?>
                    <?php $year = date('Y'); ?>
                    <?php for($x = $year; $x > ($year - 120); $x--) { ?>
                        <option><?php echo $x; ?></option>
                    <?php } ?>
                </select>

                <!-- Month -->
                <select name="data[dobmonth]">
                    <?php
                    $month = [
                        // start at 1 so PHP increments from 1 onward
                        1 => 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                        'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec',
                    ];
                    if($data['dobmonth']) {
                        printf(
                            '<option value="%02d">%s</option>',
                            $data['dobmonth'],
                            $month[(int)$data['dobmonth']]
                        );
                    }
                    for($x = 1; $x <= 12; $x++) {
                        printf('<option value="%02d">%s</option>', $x, $month[$x]);
                        echo PHP_EOL;
                    }
                    ?>
                </select>

                <!-- Day -->
                <select name="data[dobday]">
                    <?php if($data['dobday']) {
                        echo '<option>', $data['dobday'], '</option>';
                    } ?>
                    <?php for($x = 1; $x < 32; $x++) { ?>
                        <option><?php echo $x; ?></option>
                    <?php } ?>
                </select>
                <?php if($error['dob']) echo '<p>', htmlentities($error['dob']), '</p>'; ?>
            </p>

            <!-- Email -->
            <p class="app-input-email">
                <label>Email: </label>
                <!-- // *** security: all values should use output escaping -->
                <!-- // Example:  echo htmlspecialchars($data['email']); -->
                <input type="text" name="data[email]" value="<?php echo htmlentities($data['email']); ?>"/>
                <!-- // *** validation: make sure your validation checks above add info to $error[] for this field -->
                <?php if($error['email']) echo '<p>', htmlentities($error['email']), '</p>' ?>
            </p>

            <!-- First Name -->
            <p class="app-input-first-name">
                <label>First Name: </label>
                <input type="text" name="data[firstname]" value="<?php echo htmlentities($data['firstname']); ?>"/>
                <!-- // *** validation: make sure your validation checks above add info to $error[] for this field -->
                <?php if($error['firstname']) echo '<p>', htmlentities($error['firstname']); ?>
            </p>

            <!-- Last Name -->
            <p class="app-input-last-name">
                <label>Last Name: </label>
                <input type="text" name="data[lastname]" value="<?php echo $data['lastname']; ?>"/>
                <!-- // *** validation: make sure your validation checks above add info to $error[] for this field -->
                <?php if($error['lastname']) echo '<p>', htmlentities($error['lastname']); ?>
            </p>

            <!-- Photo -->
            <p class="app-input-photo">
                <label>Photo: </label>
                <input type="text" name="data[photo]" value="<?php echo htmlentities($data['photo']); ?>"/>
                <!-- // *** validation: make sure your validation checks above add info to $error[] for this field -->
                <?php if($error['photo']) echo '<p>', htmlentities($error['photo']); ?>
            </p>

            <!-- Address -->
            <p class="app-input-address">
                <label>Address: </label>
                <input type="text" name="data[address]" value="<?php echo $data['address']; ?>"/>
                <!-- // *** validation: make sure your validation checks above add info to $error[] for this field -->
                <?php if($error['address']) echo '<p>', htmlentities($error['address']), '</p>'; ?>
            </p>

            <!-- City -->
            <p class="app-input-city">
                <label>City: </label>
                <input type="text" name="data[city]" value="<?php echo $data['city']; ?>"/>
                <!-- // *** validation: make sure your validation checks above add info to $error[] for this field -->
                <?php if($error['city']) echo '<p>', $error['city']; ?>
            </p>

            <!-- State -->
            <p class="app-input-state">
                <label>State/Province: </label>
                <input type="text" name="data[stateProv]" value="<?php echo $data['stateProv']; ?>"/>
                <!-- // *** validation: make sure your validation checks above add info to $error[] for this field -->
                <?php if($error['stateProv']) echo '<p>', $error['stateProv']; ?>
            </p>

            <!-- Country -->
            <p class="app-input-country">
                <!-- // *** validation: implement a database lookup -->
                <label>Country: </label>
                <input type="text" name="data[country]" value="<?php echo $data['country']; ?>"/>
                <!-- // *** make sure your validation checks above add info to $error[] for this field -->
                <?php if($error['country']) echo '<p>', $error['country']; ?>
            </p>

            <!-- Postcode -->
            <p class="app-input-postcode">
                <label>Postcode: </label>
                <input type="text" name="data[postcode]" value="<?php echo $data['postcode']; ?>"/>
                <!-- // *** validation: make sure your validation checks above add info to $error[] for this field -->
                <?php if($error['postcode']) echo '<p>', $error['postcode']; ?>
            </p>

            <!-- Telephone -->
            <p class="app-input-telephone">
                <label>Telephone: </label>
                <input type="text" name="data[telephone]" value="<?php echo $data['telephone']; ?>"/>
                <!-- // *** validation: make sure your validation checks above add info to $error[] for this field -->
                <?php if($error['telephone']) echo '<p>', $error['telephone']; ?>
            </p>

            <!-- Password -->
            <p class="app-input-password">
                <label>Password: </label>
                <input type="text" name="data[password]" value="<?php echo $data['password']; ?>"/>
                <!-- // *** validation: make sure your validation checks above add info to $error[] for this field -->
                <?php if($error['password']) echo '<p>', $error['password']; ?>
            </p>

            <!-- Submit Buttons -->
            <p class="app-input-submit-btn">
                <!-- // *** proper access controls: add a security question + answer -->
                <input type="reset" name="data[clear]" value="Clear" class="button"/>
                <input type="submit" name="data[submit]" value="Submit" class="button marL10"/>
            </p>
        </form>

        <br>
    </div>

</div>
