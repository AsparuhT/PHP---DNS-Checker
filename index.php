<?php
/*

    1. ^(?![0-9]+$) - Ensures the domain name doesn't consist only of digits.
    2. (?!-) - Ensures the domain name doesn't start with a hyphen.
    3. [a-zA-Z0-9-]{1,63} - Allows letters, digits, and hyphens in the domain name, with a length of 1 to 63 characters.
    4. (?<!-) - Ensures the domain name doesn't end with a hyphen.
    5. \. - Expects a literal dot.
    6. [a-zA-Z]{2,6}$ - Allows a 2 to 6 characters long top-level domain (TLD), consisting of letters only.

*/
function isValidDomainName($domain)
{
    // Regular expression for validating a domain name
    $regex = '/^(?!\-)([a-zA-Z0-9-]{1,63}\.)+[a-zA-Z]{2,}$/';

    if (preg_match($regex, $domain)) {
        return true;
    } else {
        return false;
    }
}


$executionTime = 0; 
$error_msg = [
    'empty' => '',
    'domain_format' => ''
];


// Using the GET method which enables link sharing
if (isset($_GET['domain'])) {
    // get the domain
    $domain = trim(strtolower(htmlspecialchars($_GET['domain'])));

    // Check for empty domain or incorrect format
    if (empty($domain)) {
        $error_msg['empty'] = "Add a domain name";
    } else {
        if (!isValidDomainName($domain)) {
            $error_msg['domain_format'] = "The domain has to be in the right format";
        } else {



            // measure the execution time
            $start_time = microtime(true);

            if (strpos($domain, 'www.') !== 0) {
                $ns_records = dns_get_record($domain, DNS_NS);
                $a_records = dns_get_record($domain, DNS_A);
                $mx_records = dns_get_record($domain, DNS_MX);
                $txt_records = dns_get_record($domain, DNS_TXT);

                // www
                $wwwCNAMErecords = dns_get_record('www.' . $domain, DNS_CNAME);
                $wwwArecords = dns_get_record('www.' . $domain, DNS_A);

                // DMARC
                $dmarc_records = dns_get_record('_dmarc.' . $domain, DNS_TXT);

                // DNSSEC
                // 1. Prepare the URL
                $url = "https://dns.google/resolve?name={$domain}&type=DS";

                //2. Make the API call
                $response = file_get_contents($url);

                // 3. Check if there is DNSSEC record
                if ($response !== false) {
                    $data = json_decode($response, true);

                    if (isset($data['Answer'])) {
                        $dnssec_record =  'Active';
                    } else {
                        $dnssec_record =  'unsigned';
                    };
                } else {
                    $dnssec_record =  'DNSSEC check failed';
                };
            } else {
                $cname_records = dns_get_record($domain, DNS_CNAME);
                $a_records = dns_get_record($domain, DNS_A);
            }

            // end the execution time measuring
            $end_time = microtime(true);


            // calculate the time it took to execute the code between the microtime() functions
            $executionTime = number_format(($end_time - $start_time), 1);
        } // end of checks
    };
};

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DNS manager</title>
    <link rel="icon" href="images/dns.png" type="image/x-icon">
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/style.css">
</head>




<body>

    <header>
        <div class="container flex">

            <div class="logo-div">
                <img src="images/domain.png" alt="dns-logo" id="dns-logo">
                <!-- <p>Manager</p> -->
            </div>


            <form action="page.php" method="POST">


                <label for="domain">Domain:</label>
                <input type="text" name="domain" id="domain-input" value="<?php echo $domain ?? "" ?>">
                <input type="submit" value="Check" id="submit-input">
                <br>
                <p class="error-msg"><?php echo $error_msg['empty'] ?></p>
                <p class="error-msg"><?php echo $error_msg['domain_format'] ?></p>
            </form>

            <!-- <h3>The Domain name checker tool</h3> -->
        </div>
    </header>

    <main>
        <section class="dns-records sections">
            <h4>DNS records</h4>

            <table cellspacing="0" cellpadding="0" id="dns-table">
                <tr>
                    <th class="domain-column">Domain</th>
                    <th class="type-column">Type</th>
                    <th class="ttl-column">TTL</th>
                    <th class="prior-column">Prior</th>
                    <th class="answer-column">Answer</th>
                </tr>
                <?php if (isset($ns_records)) { ?>
                    <?php foreach ($ns_records as $record) {; ?>
                        <tr>
                            <td>
                                <?php echo $record['host'] ?>
                            </td>
                            <td>
                                <?php echo $record['type'] ?>
                            </td>
                            <td>
                                <?php echo $record['ttl'] ?>
                            </td>
                            <td></td>
                            <td>
                                <?php echo $record['target'] ?>
                            </td>
                        </tr>
                <?php }
                }; ?>


                <?php if (isset($a_records)) { ?>
                    <?php foreach ($a_records as $record) {; ?>
                        <tr>
                            <td>
                                <?php echo $record['host'] ?>
                            </td>
                            <td>
                                <?php echo $record['type'] ?>
                            </td>
                            <td>
                                <?php echo $record['ttl'] ?>
                            </td>
                            <td></td>
                            <td>
                                <?php echo $record['ip'] ?>
                            </td>
                        </tr>
                <?php }
                }; ?>


                <?php if (isset($wwwArecords)) { ?>
                    <?php foreach ($wwwArecords as $record) {; ?>
                        <tr>
                            <td>
                                <?php echo $record['host'] ?>
                            </td>
                            <td>
                                <?php echo $record['type'] ?>
                            </td>
                            <td>
                                <?php echo $record['ttl'] ?>
                            </td>
                            <td></td>
                            <td>
                                <?php echo $record['ip'] ?>
                            </td>
                        </tr>
                <?php }
                }; ?>


                <?php if (isset($wwwCNAMErecords)) { ?>
                    <?php foreach ($wwwCNAMErecords as $record) {; ?>
                        <tr>
                            <td>
                                <?php echo $record['host'] ?>
                            </td>
                            <td>
                                <?php echo $record['type'] ?>
                            </td>
                            <td>
                                <?php echo $record['ttl'] ?>
                            </td>
                            <td></td>
                            <td>
                                <?php echo $record['target'] ?>
                            </td>
                        </tr>
                <?php }
                }; ?>


                <?php if (isset($cname_records)) { ?>
                    <?php foreach ($cname_records as $record) {; ?>
                        <tr>
                            <td>
                                <?php echo $record['host'] ?>
                            </td>
                            <td>
                                <?php echo $record['type'] ?>
                            </td>
                            <td>
                                <?php echo $record['ttl'] ?>
                            </td>
                            <td></td>
                            <td>
                                <?php echo $record['target'] ?>
                            </td>
                        </tr>
                <?php }
                }; ?>


                <?php if (isset($mx_records)) { ?>
                    <?php foreach ($mx_records as $record) {; ?>
                        <tr>
                            <td>
                                <?php echo $record['host'] ?>
                            </td>
                            <td>
                                <?php echo $record['type'] ?>
                            </td>
                            <td>
                                <?php echo $record['ttl'] ?>
                            </td>
                            <td>
                                <?php echo $record['pri'] ?>
                            </td>
                            <td>
                                <?php echo $record['target'] ?>
                            </td>
                        </tr>
                <?php }
                }; ?>


                <?php if (isset($txt_records)) { ?>
                    <?php foreach ($txt_records as $record) {; ?>
                        <tr>
                            <td>
                                <?php echo $record['host'] ?>
                            </td>
                            <td>
                                <?php echo $record['type'] ?>
                            </td>
                            <td>
                                <?php echo $record['ttl'] ?>
                            </td>
                            <td></td>
                            <td>
                                <?php echo $record['txt'] ?>
                            </td>
                        </tr>
                <?php }
                }; ?>


                <?php if (isset($dmarc_records)) { ?>
                    <?php foreach ($dmarc_records as $record) {; ?>
                        <tr>
                            <td>
                                <?php echo $record['host'] ?>
                            </td>
                            <td>
                                <?php echo $record['type'] ?>
                            </td>
                            <td>
                                <?php echo $record['ttl'] ?>
                            </td>
                            <td></td>
                            <td>
                                <?php echo $record['txt'] ?>
                            </td>
                        </tr>
                <?php }
                }; ?>
            </table>

            <p class="exe-time"><?php echo "Total elapsed query time: <b>$executionTime s</b>" ?></p>
        </section>


        <section class="info sections">

            <div class="info-left">
                <p><b>Whois</b> info</p>
                <a href='<?php echo "https://www.whois.com/whois/" . ($domain ?? '') ?>' target="_blank">check for - <?php echo $domain ?? '' ?></a>

                <br><br>

                <p><b>SPF</b> - MX Toolbox check</p>
                <a href='<?php echo "https://mxtoolbox.com/SuperTool.aspx?action=spf%3a" . ($domain ?? '') . "&run=toolpage" ?>' target="_blank">check for - <?php echo $domain ?? '' ?></a>

                <br><br>

                <p><b>DMARC</b> - MX Toolbox check</p>
                <a href='<?php echo "https://mxtoolbox.com/SuperTool.aspx?action=dmarc%3a" . ($domain ?? '') . "&run=toolpage" ?>' target="_blank">check for - <?php echo $domain ?? '' ?></a>
            </div>


            <div class="info-right">
                <h4>DNSSEC</h4>
                <?php if (isset($dnssec_record)) { ?>
                    <?php if ($dnssec_record === 'Active') { ?>
                        <p class="danger"><b>Active</b></p>
                    <?php } else {
                        echo $dnssec_record;
                    } ?>
                <?php } ?>
            </div>


        </section>

    </main>


</body>

</html>