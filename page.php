<?php

/*
1. Check if domain is posted
2. Process input to prevent XSS
3. Redirect to the index.php with the domain as a URL-encoded query parameter
*/

if(isset($_POST['domain'])) {
    $domain = trim(strtolower(htmlspecialchars($_POST['domain'])));
    header("Location: http://localhost:8888/dns/index.php?domain=" . urlencode($domain));
};

?>