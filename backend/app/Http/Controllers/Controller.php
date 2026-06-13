<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

/**
 * Base Controller
 *
 * Central controller for all application controllers.
 *
 * Features:
 * - Authorization helpers
 * - Validation helpers
 * - Policy integration
 * - Future middleware extensions
 *
 * @package App\Http\Controllers
 */
abstract class Controller
{
    use AuthorizesRequests;
    use ValidatesRequests;
}