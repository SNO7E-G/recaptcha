# Architecture

The general pattern of usage is to instantiate the `ReCaptcha` class with your
secret key, specify any additional validation rules, and then call `verify()`
with the reCAPTCHA response and user's IP address. For example:

```php
<?php
$recaptcha = new \ReCaptcha\ReCaptcha($secret);
$resp = $recaptcha->setExpectedHostname('recaptcha-demo.appspot.com')
                  ->verify($gRecaptchaResponse, $remoteIp);
if ($resp->isSuccess()) {
    // Verified!
} else {
    $errors = $resp->getErrorCodes();
}
```

The `ReCaptcha` class automatically chooses a method to communicate with the
reCAPTCHA service based on your server's capabilities. See the
[Alternate request methods](README.md#alternate-request-methods) section in the
README for more details.

## Adding new request methods

Create a class that implements the
[`RequestMethod`](./src/ReCaptcha/RequestMethod.php) interface. The convention
is to name this class `RequestMethod\`_MethodType_`Post`. Take a look at
[`RequestMethod\CurlPost`](./src/ReCaptcha/RequestMethod/CurlPost.php)
with the matching
[`RequestMethod/CurlPostTest`](./tests/ReCaptcha/RequestMethod/CurlPostTest.php)
to see this pattern in action.

### Error conventions

The client returns the response as provided by the reCAPTCHA services augmented
with additional error codes based on the client's checks. When adding a new
[`RequestMethod`](./src/ReCaptcha/RequestMethod.php) ensure that it returns the
`ReCaptcha::E_CONNECTION_FAILED` and `ReCaptcha::E_BAD_RESPONSE` where
appropriate.

## Public API compatibility

The 1.x line treats the following classes and interfaces as public API:
`ReCaptcha`, `RequestMethod`, `Response`, `RequestParameters`,
`RequestMethod\Post`, `RequestMethod\CurlPost`, and
`RequestMethod\SocketPost`.

Changes that narrow those APIs, such as adding native scalar parameter types,
adding native return types to existing public methods, making public non-final
classes `readonly` or `final`, removing public classes, or removing existing
constructor argument forms, should be reserved for a major release.

The `RequestMethod::submit()` interface intentionally keeps its 1.x-compatible
native signature. Implementations are still expected to return the body of the
reCAPTCHA response as a string.
