<?php
	/**
	 * Forte REST API v3 – Basic Authentication sample (PHP)
	 *
	 * Aligned with ExternalAPI (net8) CustomersController routes and auth:
	 *   GET /v3/organizations/{org_id}/locations/{loc_id}/customers
	 *   Authorization: Basic {base64(api_access_id:api_secure_key)}
	 *   X-Forte-Auth-Organization-Id: org_{id}
	 *
	 * Usage:
	 *   ?sandbox=1   → Sandbox (default public sandbox host)
	 *   ?local=1     → Local ExternalAPI (https://localhost:9898)
	 *   (neither)    → Live
	 */

	$externalContent = @file_get_contents('http://checkip.dyndns.com/');
	if ($externalContent !== false && preg_match('/Current IP Address: \[?([:.0-9a-fA-F]+)\]?/', $externalContent, $m)) {
		print_r('CheckIp Response: ' . $m[1] . '<br>');
	}

	// Replace placeholders with credentials from Dex (Developer > API Credentials).
	$OrganizationID = 'org_**********';
	$LocationID     = 'loc_**********';
	$ApiAccessID    = '**********';
	$ApiSecureKey   = '**********';

	if (isset($_GET['local'])) {
		$url = 'https://sandbox.forte.net/API';
	} elseif (isset($_GET['sandbox'])) {
		$url = 'https://sandbox.forte.net/api';
	} else {
		$url = 'https://api.forte.net/api';
	}

	$auth_token = base64_encode($ApiAccessID . ':' . $ApiSecureKey);
	$service_url = $url . '/v3/organizations/' . $OrganizationID . '/locations/' . $LocationID . '/customers';

	print_r('Request URL: ' . $service_url . '<br>');

	$curl = curl_init($service_url);
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_HTTPHEADER, array(
		'Authorization: Basic ' . $auth_token,
		'X-Forte-Auth-Organization-Id: ' . $OrganizationID,
		'Accept: application/json',
		'Content-Type: application/json'
	));
	curl_setopt($curl, CURLOPT_NOBODY, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_FAILONERROR, false);
	curl_setopt($curl, CURLOPT_TIMEOUT, 30);

	$curl_response = curl_exec($curl);

	$info = curl_getinfo($curl);
	print_r('HttpStatus Code: ' . $info['http_code'] . '<br>');

	if ($curl_response === false) {
		print_r('Curl error: ' . curl_error($curl) . '<br>');
	}

	// curl_close() is a no-op since PHP 8.0 and deprecated in 8.5; handle frees on unset.
	unset($curl);
	print_r($curl_response);

	$decoded = json_decode($curl_response);
	print_r($decoded);

	echo '<br> end here';
?>
