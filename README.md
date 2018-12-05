# voice-collector-new
Backend for voice collector app

_ STAGE=dev /usr/local/bin/php index.php
_ ./vendor/bin/phinx migrate

* Install apcu_bc

* Fractal

--- Single include
include=loggeduser

--- Nested include
include=loggeduser,loggeduser.jobstatus

--- Query relation with conditions
$myLoggedUserResult = $job->getUsers([
    'conditions' => '[\User\Model\User].[id] = :loggedUserId:',
    'bind' => [
        'loggedUserId' => (int) $authService->getUser()['id']
    ]
]);