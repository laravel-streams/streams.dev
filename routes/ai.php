<?php

use App\Mcp\Servers\StreamsDocsServer;
use Laravel\Mcp\Facades\Mcp;

/*
|--------------------------------------------------------------------------
| MCP servers
|--------------------------------------------------------------------------
|
| Loaded by laravel/mcp outside the "web" middleware group, so there is no
| session or CSRF on these routes. The docs server is public and read-only;
| the "mcp" rate limiter (RouteServiceProvider) caps it per client IP.
|
*/

Mcp::web('/mcp', StreamsDocsServer::class)
    ->middleware('throttle:mcp')
    ->name('mcp.docs');

Mcp::local('streams-docs', StreamsDocsServer::class);
