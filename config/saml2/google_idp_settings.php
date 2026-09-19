<?php

$this_idp_env_id = 'GOOGLE';

return [
    'strict' => true,
    'debug' => env('APP_DEBUG', false),

    'sp' => [
        'NameIDFormat' => 'urn:oasis:names:tc:SAML:2.0:nameid-format:persistent',
        'x509cert' => env('SAML2_'.$this_idp_env_id.'_SP_x509', ''),
        'privateKey' => env('SAML2_'.$this_idp_env_id.'_SP_PRIVATEKEY', ''),
        'entityId' => env('SAML2_'.$this_idp_env_id.'_SP_ENTITYID', ''),
        'assertionConsumerService' => [
            'url' => env('SAML2_'.$this_idp_env_id.'_SP_ACS_URL', ''),
        ],
        'singleLogoutService' => [
            'url' => env('SAML2_'.$this_idp_env_id.'_SP_SLS_URL', ''),
        ],
    ],

    'idp' => [
        'entityId' => env('SAML2_'.$this_idp_env_id.'_IDP_ENTITYID', 'https://accounts.google.com/o/saml2?idpid=C01fcw1ee'),
        'singleSignOnService' => [
            'url' => env('SAML2_'.$this_idp_env_id.'_IDP_SSO_URL', 'https://accounts.google.com/o/saml2/idp?idpid=C01fcw1ee'),
        ],
        'singleLogoutService' => [
            'url' => env('SAML2_'.$this_idp_env_id.'_IDP_SLO_URL', ''),
        ],
        'x509cert' => env('SAML2_'.$this_idp_env_id.'_IDP_x509', <<<'CERT'
-----BEGIN CERTIFICATE-----
MIIDdDCCAlygAwIBAgIGAZ+QSP1VMA0GCSqGSIb3DQEBCwUAMHsxFDASBgNVBAoTC0dvb2dsZSBJ
bmMuMRYwFAYDVQQHEw1Nb3VudGFpbiBWaWV3MQ8wDQYDVQQDEwZHb29nbGUxGDAWBgNVBAsTD0dv
b2dsZSBGb3IgV29yazELMAkGA1UEBhMCVVMxEzARBgNVBAgTCkNhbGlmb3JuaWEwHhcNMjYwNzIz
MTg0MjEwWhcNMzEwNzIyMTg0MjEwWjB7MRQwEgYDVQQKEwtHb29nbGUgSW5jLjEWMBQGA1UEBxMN
TW91bnRhaW4gVmlldzEPMA0GA1UEAxMGR29vZ2xlMRgwFgYDVQQLEw9Hb29nbGUgRm9yIFdvcmsx
CzAJBgNVBAYTAlVTMRMwEQYDVQQIEwpDYWxpZm9ybmlhMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8A
MIIBCgKCAQEAscrDW2/nqWPcKFhsaIEoyVhhRZW9tCC1mMufBD9FB7P5ulbMHJg4AxAtKoE7sYXr
YvyRYPuLDDWym2aZdJxXO1b6uRCZB/iwLCZFjXiP8kU+BZa/VK1QEv5c8dbFtpIz90hBeEkS/Il1
nieWVHHEjY4U9DhV9xSO/Enf7D7iP/XGI2ZQQo7LFqDaSrQdj4lpN6fywXXgvy40iMxyzXwAJFAd
BzZnKtNgqTk60gByUG8+xipSWE2rjPa8nC31+cwE6YUAu5aBucD+ltE1fL8Xir8O5pgwFNHWW/kZ
/ygoTjlzWiqK6rzAKoqxsEt0Eodf/cpe7VWj+EH+6+p0zLVvLQIDAQABMA0GCSqGSIb3DQEBCwUA
A4IBAQBPKjQLo28FtEch+MuQWJgMovagAj40MJT2c5KOYKvo+OA2NexW+nRo+YiZ6PKDK6etq9Ur
il0eqlmV84Q5QW0BrF/p4Hwi2amlQM0BrNxeDBrhxCWIB7VChM6h9aCRVWhgkJj+ndkYd/tCHDyx
p+aPs6WpPFA8nVkHMUjJwJP615jIcx2MMd7FMdu1P1rm/MVHeo55X1s1EA8pSgu8Ygw2v8Wo0e7f
G2aTpNS012DKBJJWbFh8BEV2omvXgOt6GYg7DX3rD3YoH4UASR4REim02MN6tXJdT8HZPIMtnbWR
Jj7NdCI35vQOAF5cgU/IgzFkSUmkA9abn2j87KNSYuLF
-----END CERTIFICATE-----
CERT),
    ],

    'security' => [
        'nameIdEncrypted' => false,
        'authnRequestsSigned' => false,
        'logoutRequestSigned' => false,
        'logoutResponseSigned' => false,
        'signMetadata' => false,
        'wantMessagesSigned' => false,
        'wantAssertionsSigned' => false,
        'wantNameIdEncrypted' => false,
        'requestedAuthnContext' => true,
    ],

    'contactPerson' => [
        'technical' => [
            'givenName' => 'Lulu',
            'emailAddress' => env('ADMIN_EMAIL', 'no-reply@lotteria.local'),
        ],
        'support' => [
            'givenName' => 'Lulu Support',
            'emailAddress' => env('ADMIN_EMAIL', 'no-reply@lotteria.local'),
        ],
    ],

    'organization' => [
        'en-US' => [
            'name' => 'Lulu',
            'displayname' => 'Lulu',
            'url' => env('APP_URL', 'http://localhost:8000'),
        ],
    ],
];
