<?php

declare(strict_types=1);


/*
|--------------------------------------------------------------------------
| NasyidOS Application Bootstrap
|--------------------------------------------------------------------------
*/

date_default_timezone_set(
    'Asia/Kuala_Lumpur'
);


/*
|--------------------------------------------------------------------------
| Production Error Handling
|--------------------------------------------------------------------------
|
| Do not expose PHP errors to visitors.
|
*/

ini_set(
    'display_errors',
    '0'
);

ini_set(
    'display_startup_errors',
    '0'
);

error_reporting(
    E_ALL
);


/*
|--------------------------------------------------------------------------
| Session Security
|--------------------------------------------------------------------------
*/

ini_set(
    'session.use_strict_mode',
    '1'
);

ini_set(
    'session.cookie_httponly',
    '1'
);

ini_set(
    'session.cookie_samesite',
    'Lax'
);


$https =
    !empty($_SERVER['HTTPS']) &&
    $_SERVER['HTTPS'] !== 'off';


session_name(
    'nasyidos_session'
);


session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => $https,
    'httponly' => true,
    'samesite' => 'Lax',
]);


session_start();


/*
|--------------------------------------------------------------------------
| Load Core
|--------------------------------------------------------------------------
*/

require_once
    __DIR__ .
    '/app/Core/Database.php';

require_once
    __DIR__ .
    '/app/Core/Security.php';

require_once
    __DIR__ .
    '/app/Core/Auth.php';


/*
|--------------------------------------------------------------------------
| Load Auth Controller
|--------------------------------------------------------------------------
*/

require_once
    __DIR__ .
    '/app/Controllers/AuthController.php';


/*
|--------------------------------------------------------------------------
| Artist
|--------------------------------------------------------------------------
*/

require_once
    __DIR__ .
    '/app/Models/Artist.php';

require_once
    __DIR__ .
    '/app/Controllers/ArtistController.php';


/*
|--------------------------------------------------------------------------
| Release
|--------------------------------------------------------------------------
*/

require_once
    __DIR__ .
    '/app/Models/Release.php';

require_once
    __DIR__ .
    '/app/Controllers/ReleaseController.php';


/*
|--------------------------------------------------------------------------
| Track
|--------------------------------------------------------------------------
*/

require_once
    __DIR__ .
    '/app/Models/Track.php';

require_once
    __DIR__ .
    '/app/Controllers/TrackController.php';


/*
|--------------------------------------------------------------------------
| Basic Router
|--------------------------------------------------------------------------
*/

$page =
    $_GET['page']
    ?? 'home';


/*
|--------------------------------------------------------------------------
| REGISTER
|--------------------------------------------------------------------------
*/

if (
    $page === 'register' &&
    $_SERVER['REQUEST_METHOD'] === 'POST'
) {

    AuthController::register();

}


if (
    $page === 'register' &&
    $_SERVER['REQUEST_METHOD'] === 'GET'
) {

    require
        __DIR__ .
        '/views/auth/register.php';

    exit;
}


/*
|--------------------------------------------------------------------------
| LOGIN
|--------------------------------------------------------------------------
*/

if (
    $page === 'login' &&
    $_SERVER['REQUEST_METHOD'] === 'POST'
) {

    AuthController::login();

}


if (
    $page === 'login' &&
    $_SERVER['REQUEST_METHOD'] === 'GET'
) {

    require
        __DIR__ .
        '/views/auth/login.php';

    exit;
}


/*
|--------------------------------------------------------------------------
| ARTISTS
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| Artist create - POST
|--------------------------------------------------------------------------
*/

if (
    $page === 'artists-create' &&
    $_SERVER['REQUEST_METHOD'] === 'POST'
) {

    ArtistController::create();

}


/*
|--------------------------------------------------------------------------
| Artist create - GET
|--------------------------------------------------------------------------
*/

if (
    $page === 'artists-create' &&
    $_SERVER['REQUEST_METHOD'] === 'GET'
) {

    Auth::requireLogin();

    require
        __DIR__ .
        '/views/artist-create.php';

    exit;
}


/*
|--------------------------------------------------------------------------
| Artist list
|--------------------------------------------------------------------------
*/

if (
    $page === 'artists' &&
    $_SERVER['REQUEST_METHOD'] === 'GET'
) {

    $user =
        Auth::requireLogin();


    $artists =
        Artist::allForUser(
            (int) $user['id']
        );


    require
        __DIR__ .
        '/views/artists.php';

    exit;
}


/*
|--------------------------------------------------------------------------
| Artist detail
|--------------------------------------------------------------------------
*/

if (
    $page === 'artist' &&
    $_SERVER['REQUEST_METHOD'] === 'GET'
) {

    ArtistController::show();

}


/*
|--------------------------------------------------------------------------
| Artist edit - POST
|--------------------------------------------------------------------------
*/

if (
    $page === 'artist-edit' &&
    $_SERVER['REQUEST_METHOD'] === 'POST'
) {

    ArtistController::update();

}


/*
|--------------------------------------------------------------------------
| Artist edit - GET
|--------------------------------------------------------------------------
*/

if (
    $page === 'artist-edit' &&
    $_SERVER['REQUEST_METHOD'] === 'GET'
) {

    ArtistController::edit();

}


/*
|--------------------------------------------------------------------------
| RELEASES
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| Release create - POST
|--------------------------------------------------------------------------
*/

if (
    $page === 'release-create' &&
    $_SERVER['REQUEST_METHOD'] === 'POST'
) {

    ReleaseController::create();

}


/*
|--------------------------------------------------------------------------
| Release create - GET
|--------------------------------------------------------------------------
*/

if (
    $page === 'release-create' &&
    $_SERVER['REQUEST_METHOD'] === 'GET'
) {

    $user =
        Auth::requireLogin();


    $artists =
        Artist::allForUser(
            (int) $user['id']
        );


    $errors =
        $_SESSION['release_errors']
        ?? [];


    $old =
        $_SESSION['old_release']
        ?? [];


    if (
        !is_array($errors)
    ) {

        $errors = [];

    }


    if (
        !is_array($old)
    ) {

        $old = [];

    }


    unset(
        $_SESSION['release_errors'],
        $_SESSION['old_release']
    );


    require
        __DIR__ .
        '/views/release-create.php';

    exit;
}


/*
|--------------------------------------------------------------------------
| Release edit - POST
|--------------------------------------------------------------------------
*/

if (
    $page === 'release-edit' &&
    $_SERVER['REQUEST_METHOD'] === 'POST'
) {

    ReleaseController::update();

}


/*
|--------------------------------------------------------------------------
| Release edit - GET
|--------------------------------------------------------------------------
*/

if (
    $page === 'release-edit' &&
    $_SERVER['REQUEST_METHOD'] === 'GET'
) {

    ReleaseController::edit();

}


/*
|--------------------------------------------------------------------------
| Release list
|--------------------------------------------------------------------------
*/

if (
    $page === 'releases' &&
    $_SERVER['REQUEST_METHOD'] === 'GET'
) {

    ReleaseController::index();

}


/*
|--------------------------------------------------------------------------
| Release detail
|--------------------------------------------------------------------------
*/

if (
    $page === 'release' &&
    $_SERVER['REQUEST_METHOD'] === 'GET'
) {

    ReleaseController::show();

}


/*
|--------------------------------------------------------------------------
| TRACKS
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| Track create - POST
|--------------------------------------------------------------------------
|
| The TrackController method is create().
|
*/

if (
    $page === 'track-create' &&
    $_SERVER['REQUEST_METHOD'] === 'POST'
) {

    TrackController::create();

}


/*
|--------------------------------------------------------------------------
| Track create - GET
|--------------------------------------------------------------------------
|
| GET only renders the create form.
|
*/

if (
    $page === 'track-create' &&
    $_SERVER['REQUEST_METHOD'] === 'GET'
) {

    $user =
        Auth::requireLogin();


    $releaseId = filter_input(
        INPUT_GET,
        'release_id',
        FILTER_VALIDATE_INT
    );


    if (!$releaseId) {

        http_response_code(400);

        exit(
            'Invalid release.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Verify release belongs to current user
    |--------------------------------------------------------------------------
    */

    $release =
        Release::findForUser(
            (int) $releaseId,
            (int) $user['id']
        );


    if (!$release) {

        http_response_code(404);

        exit(
            'Release not found.'
        );
    }


    require
        __DIR__ .
        '/views/track-create.php';

    exit;
}


/*
|--------------------------------------------------------------------------
| Track detail - GET
|--------------------------------------------------------------------------
|
| 32I-5
|
*/

if (
    $page === 'track' &&
    $_SERVER['REQUEST_METHOD'] === 'GET'
) {

    TrackController::show();

}


/*
|--------------------------------------------------------------------------
| Track edit - POST
|--------------------------------------------------------------------------
*/

if (
    $page === 'track-edit' &&
    $_SERVER['REQUEST_METHOD'] === 'POST'
) {

    TrackController::update();

}


/*
|--------------------------------------------------------------------------
| Track edit - GET
|--------------------------------------------------------------------------
*/

if (
    $page === 'track-edit' &&
    $_SERVER['REQUEST_METHOD'] === 'GET'
) {

    TrackController::edit();

}


/*
|--------------------------------------------------------------------------
| Track delete - POST
|--------------------------------------------------------------------------
*/

if (
    $page === 'track-delete' &&
    $_SERVER['REQUEST_METHOD'] === 'POST'
) {

    TrackController::delete();

}


/*
|--------------------------------------------------------------------------
| DASHBOARD
|--------------------------------------------------------------------------
*/

if (
    $page === 'dashboard'
) {

    $user =
        Auth::requireLogin();


    require
        __DIR__ .
        '/views/dashboard.php';

    exit;
}


/*
|--------------------------------------------------------------------------
| LOGOUT
|--------------------------------------------------------------------------
*/

if (
    $page === 'logout'
) {

    Auth::logout();


    header(
        'Location: ?page=login'
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| HOME
|--------------------------------------------------------------------------
*/

if (
    $page === 'home'
) {

    require
        __DIR__ .
        '/views/home.php';

    exit;
}


/*
|--------------------------------------------------------------------------
| 404
|--------------------------------------------------------------------------
*/

http_response_code(404);

echo 'Page not found.';