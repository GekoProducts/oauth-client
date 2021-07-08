# Geko Products OAuth Client

Based on [OAuth 2.0 Client](https://github.com/thephpleague/oauth2-client) by PHP League.

The client does not yet support retrieving resource owner details.

### Example usage

```
$provider = new \GekoProducts\OAuth\Provider\GekoProducts([
    "client_id" => "my_client_id",
    "client_secret" => "TopSecret"
]);

$accessToken = $provider->getAccessToken("client_credentials");

var_dump($accessToken);
die;
```
