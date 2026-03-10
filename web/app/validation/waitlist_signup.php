<?php

declare(strict_types=1);

function ypsia_collect_waitlist_input(array $post): array
{
    $email = isset($post['email']) ? trim((string) $post['email']) : '';
    $interest = isset($post['interest']) ? trim((string) $post['interest']) : '';
    $website = isset($post['website']) ? trim((string) $post['website']) : '';
    $action = isset($post['action']) ? trim((string) $post['action']) : 'subscribe';

    return [
        'email' => $email,
        'interest' => $interest,
        'website' => $website,
        'action' => $action,
    ];
}

function ypsia_is_waitlist_bot_submission(array $input): bool
{
    return $input['website'] !== '';
}

function ypsia_normalize_waitlist_email(string $email): string
{
    return strtolower(filter_var($email, FILTER_SANITIZE_EMAIL) ?: '');
}

function ypsia_validate_waitlist_email(string $email): bool
{
    return $email !== ''
        && strlen($email) <= 254
        && filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function ypsia_validate_waitlist_signup(array $input): array
{
    $email = ypsia_normalize_waitlist_email($input['email']);
    $interest = strip_tags($input['interest']);
    $interest = preg_replace('/\s+/', ' ', $interest) ?? '';
    $interest = trim($interest);

    if (!ypsia_validate_waitlist_email($email)) {
        return [
            'is_valid' => false,
            'data' => null,
        ];
    }

    if (strlen($interest) > 240) {
        $interest = substr($interest, 0, 240);
    }

    return [
        'is_valid' => true,
        'data' => [
            'submitted_at' => gmdate('c'),
            'email' => $email,
            'interest' => $interest,
        ],
    ];
}

function ypsia_validate_waitlist_removal(array $input): array
{
    $email = ypsia_normalize_waitlist_email($input['email']);

    return [
        'is_valid' => ypsia_validate_waitlist_email($email),
        'data' => [
            'email' => $email,
        ],
    ];
}
