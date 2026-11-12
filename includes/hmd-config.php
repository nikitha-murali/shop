<?php
$collegeDomains = getenv('HMD_COLLEGE_EMAIL_DOMAINS');
if ($collegeDomains === false || trim($collegeDomains) === '') {
    $collegeDomains = 'presidencyuniversity.in';
}
$configuredDomains = [];
if ($collegeDomains !== false && trim($collegeDomains) !== '') {
    foreach (explode(',', strtolower($collegeDomains)) as $domain) {
        $domain = trim($domain);
        $atPosition = strrpos($domain, '@');
        if ($atPosition !== false) {
            $domain = substr($domain, $atPosition + 1);
        }
        if ($domain !== '') {
            $configuredDomains[] = $domain;
        }
    }
}
define(
    'HMD_COLLEGE_EMAIL_DOMAINS',
    array_values(array_unique($configuredDomains))
);
