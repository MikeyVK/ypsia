<?php

declare(strict_types=1);

require __DIR__ . '/../../app/bootstrap.php';
require YPSIA_ROOT . '/app/validation/waitlist_signup.php';
require YPSIA_ROOT . '/app/services/waitlist_signup.php';

function ypsia_waitlist_redirect(string $status): void
{
    header('Location: ../index.php?waitlist=' . rawurlencode($status) . '#waitlist', true, 303);
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    header('Location: ../index.php', true, 303);
    exit;
}

$input = ypsia_collect_waitlist_input($_POST);
$action = $input['action'] === 'unsubscribe' ? 'unsubscribe' : 'subscribe';

if (ypsia_is_waitlist_bot_submission($input)) {
    ypsia_waitlist_redirect($action === 'unsubscribe' ? 'removed' : 'success');
}

try {
    if ($action === 'unsubscribe') {
        $validation = ypsia_validate_waitlist_removal($input);
        if ($validation['is_valid'] !== true || !is_array($validation['data'])) {
            ypsia_waitlist_redirect('error');
        }

        ypsia_remove_waitlist_signup($validation['data']['email']);
        ypsia_waitlist_redirect('removed');
    }

    $validation = ypsia_validate_waitlist_signup($input);
    if ($validation['is_valid'] !== true || !is_array($validation['data'])) {
        ypsia_waitlist_redirect('error');
    }

    if (ypsia_waitlist_contains_email($validation['data']['email'])) {
        ypsia_waitlist_redirect('already');
    }

    ypsia_store_waitlist_signup($validation['data']);
    ypsia_waitlist_redirect('success');
} catch (Throwable $throwable) {
    ypsia_log_waitlist_error($throwable);
    ypsia_waitlist_redirect('error');
}
