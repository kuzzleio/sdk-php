
use \Kuzzle\Security\User;

$security = $kuzzle->security();

$kuid = 'test';
$userDefinition = [
  'content' => [
    'profileIds' => ['admin'],
    'firstname' => 'John',
    'lastname' => 'Doe'
  ],
  'credentials' => [
    'local' => [
      'password' => 'secret password',
      'username' => 'jdoe'
    ]
    ]
  ];

try {
  $user = $security->createUser($kuid, $userDefinition);
  echo 'true';
}
catch (ErrorException $e) {
  echo $e;
}