<?php

require 'vendor/autoload.php';


// I fancy mimicing Laravel's style of prepending API calls with a /api/* path
// So I splitted my router file into two. Dedicating one to API requests.
require_once sprintf('%s/router/%s.php', __DIR__, strpos($_SERVER['REQUEST_URI'], '/api/') > 0 ? 'api' : 'web');
