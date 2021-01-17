<?php

// CSRFGuard, changed to manual for route specific callbacks
if (config('enable_csrfguard', true)) {
    if (session_id() === '') {
        die("Sessions must be started to use CSRFGuard!");
    }

    $key = config('csrf_key', 'csrf_token');
    if (!session_get($key)) {
        session_set($key, str_random_secure(20));
    }

    $token = session_get($key);

    // Append data to view
    view_data([
        'csrf_key'   => $key,
        'csrf_token' => $token,
        'csrf_html'  => "\n<input type=\"hidden\" name=\"{$key}\" value=\"{$token}\">\n"
    ]);

    registry_store('csrf_key', $key, true);
    registry_store('csrf_token', $token, true);
}

// Honeypot middleware, changed to manual for route specific callbacks
if (config('enable_honeypot', true)) {
    $key = config('honeypot_key', '__required_for_safety__');

    // Append data to view
    view_data([
        'honeypot_key'      => $key,
        'honeypot_html'     => "\n<textarea class=\"d-none\" aria-hidden=\"true\" autocomplete=\"off\" name=\"{$key}\" value=\"\"></textarea>\n"
    ]);

    registry_store('honeypot_key', $key, true);
}
