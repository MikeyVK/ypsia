<?php

declare(strict_types=1);

function ypsia_waitlist_csv_path(): string
{
    return YPSIA_ROOT . '/storage/leads/waitlist.csv';
}

function ypsia_waitlist_log_path(): string
{
    return YPSIA_ROOT . '/storage/logs/waitlist.log';
}

function ypsia_ensure_waitlist_directory_exists(string $path): void
{
    $directory = dirname($path);

    if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
        throw new RuntimeException('Unable to create waitlist storage directory.');
    }
}

function ypsia_read_waitlist_entries(): array
{
    $csvPath = ypsia_waitlist_csv_path();
    if (!is_file($csvPath)) {
        return [];
    }

    $handle = fopen($csvPath, 'rb');
    if ($handle === false) {
        throw new RuntimeException('Unable to open waitlist CSV file for reading.');
    }

    $entries = [];

    try {
        $isFirstRow = true;
        while (($row = fgetcsv($handle)) !== false) {
            if ($isFirstRow) {
                $isFirstRow = false;
                continue;
            }

            if ($row === [null] || count($row) < 3) {
                continue;
            }

            $entries[] = [
                'submitted_at' => (string) $row[0],
                'email' => (string) $row[1],
                'interest' => (string) $row[2],
            ];
        }
    } finally {
        fclose($handle);
    }

    return $entries;
}

function ypsia_waitlist_contains_email(string $email): bool
{
    foreach (ypsia_read_waitlist_entries() as $entry) {
        if (($entry['email'] ?? '') === $email) {
            return true;
        }
    }

    return false;
}

function ypsia_store_waitlist_signup(array $signup): void
{
    $csvPath = ypsia_waitlist_csv_path();
    ypsia_ensure_waitlist_directory_exists($csvPath);

    $alreadyExists = ypsia_waitlist_contains_email($signup['email']);
    if ($alreadyExists) {
        throw new RuntimeException('Duplicate waitlist signup.');
    }

    $fileExists = is_file($csvPath);
    $isEmpty = !$fileExists || filesize($csvPath) === 0;

    $handle = fopen($csvPath, 'ab');
    if ($handle === false) {
        throw new RuntimeException('Unable to open waitlist CSV file.');
    }

    try {
        if ($isEmpty) {
            fputcsv($handle, ['submitted_at', 'email', 'interest']);
        }

        if (fputcsv($handle, [$signup['submitted_at'], $signup['email'], $signup['interest']]) === false) {
            throw new RuntimeException('Unable to write waitlist signup to CSV.');
        }
    } finally {
        fclose($handle);
    }
}

function ypsia_remove_waitlist_signup(string $email): bool
{
    $csvPath = ypsia_waitlist_csv_path();
    if (!is_file($csvPath)) {
        return false;
    }

    $entries = ypsia_read_waitlist_entries();
    $remainingEntries = [];
    $removed = false;

    foreach ($entries as $entry) {
        if (($entry['email'] ?? '') === $email) {
            $removed = true;
            continue;
        }

        $remainingEntries[] = $entry;
    }

    $handle = fopen($csvPath, 'wb');
    if ($handle === false) {
        throw new RuntimeException('Unable to rewrite waitlist CSV file.');
    }

    try {
        fputcsv($handle, ['submitted_at', 'email', 'interest']);
        foreach ($remainingEntries as $entry) {
            fputcsv($handle, [$entry['submitted_at'], $entry['email'], $entry['interest']]);
        }
    } finally {
        fclose($handle);
    }

    return $removed;
}

function ypsia_log_waitlist_error(Throwable $throwable): void
{
    $logPath = ypsia_waitlist_log_path();

    try {
        ypsia_ensure_waitlist_directory_exists($logPath);
    } catch (Throwable $ignored) {
        return;
    }

    $line = sprintf("[%s] %s\n", gmdate('c'), $throwable->getMessage());
    file_put_contents($logPath, $line, FILE_APPEND | LOCK_EX);
}
