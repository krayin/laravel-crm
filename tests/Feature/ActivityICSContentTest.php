<?php

use Carbon\Carbon;
use Webkul\Activity\Contracts\Activity as ContractsActivity;
use Webkul\Automation\Helpers\Entity\Activity as ActivityHelper;

/**
 * Builds a stand-in activity for the .ics generator.
 *
 * `Webkul\Activity\Contracts\Activity` is an empty marker interface, so the
 * generator only ever reads properties off whatever it is handed — no database
 * round-trip is needed to exercise it.
 */
function icsActivity(array $overrides = []): ContractsActivity
{
    $participants = collect($overrides['participants'] ?? [])
        ->map(fn ($name) => (object) [
            'user' => (object) ['name' => $name, 'email' => 'participant@example.com'],
        ]);

    $activity = new class implements ContractsActivity
    {
        public $id = 1;

        public $title = 'Call';

        public $location = '';

        public $comment = '';

        public $created_at;

        public $schedule_from;

        public $schedule_to;

        public $user;

        public $participants;
    };

    $activity->created_at = Carbon::parse('2026-07-23 13:45:11', 'UTC');
    $activity->schedule_from = Carbon::parse('2026-07-31 09:30:00', 'America/Argentina/Buenos_Aires');
    $activity->schedule_to = Carbon::parse('2026-07-31 10:00:00', 'America/Argentina/Buenos_Aires');
    $activity->user = (object) ['name' => 'Alejandro Sanchez', 'email' => 'organizer@example.com'];
    $activity->participants = $participants;

    foreach (array_diff_key($overrides, ['participants' => null]) as $key => $value) {
        $activity->{$key} = $value;
    }

    return $activity;
}

/**
 * Splits an .ics into physical lines.
 *
 * Deliberately breaks on a lone CR or LF as well as on CRLF. RFC 5545 delimits
 * content lines with CRLF, but a bare line break that slipped into a value is
 * exactly the defect worth catching, and every line-based parser — Google's
 * included — will break on it too. Splitting on CRLF alone would swallow one
 * inside the surrounding value and hide the bug.
 */
function icsPhysicalLines(string $ics): array
{
    return preg_split("/\r\n|\r|\n/", $ics);
}

/**
 * Unfolds an .ics back into logical content lines the way RFC 5545 section 3.1
 * says a parser must: a physical line starting with a space is a continuation
 * of the previous one.
 */
function icsLines(string $ics): array
{
    $lines = [];

    foreach (icsPhysicalLines($ics) as $physical) {
        if (str_starts_with($physical, ' ') && $lines !== []) {
            $lines[array_key_last($lines)] .= substr($physical, 1);

            continue;
        }

        $lines[] = $physical;
    }

    return $lines;
}

it('keeps every content line a valid property when the comment spans lines', function () {
    $ics = app(ActivityHelper::class)->getICSContent(icsActivity([
        'comment' => "Mandarlo en horario comercial.\n\nTiene que dejar por escrito: USD 3.600 pago único.",
    ]));

    // A raw line break would end the content line, leaving "Tiene que dejar por
    // escrito" to be read as a property name — which is what made Google refuse
    // the whole file with "Cannot load event".
    foreach (icsLines($ics) as $line) {
        expect(explode(':', $line)[0])
            ->toMatch('/^[A-Za-z0-9-]+(;.*)?$/', "Illegal content line: [$line]");
    }
});

it('escapes line breaks, commas, semicolons and backslashes in text values', function () {
    $ics = app(ActivityHelper::class)->getICSContent(icsActivity([
        'title' => 'Chequear el mail (MAINTRAVEL); si no, insistir',
        'comment' => "Primera linea\nSegunda, con coma\\barra",
    ]));

    $lines = icsLines($ics);

    expect($lines)->toContain('SUMMARY:Chequear el mail (MAINTRAVEL)\; si no\, insistir');
    expect($lines)->toContain('DESCRIPTION:Primera linea\nSegunda\, con coma\\\\barra');
});

it('folds long lines to 75 octets with a space-prefixed continuation', function () {
    $ics = app(ActivityHelper::class)->getICSContent(icsActivity([
        'comment' => str_repeat('a', 300),
    ]));

    foreach (icsPhysicalLines($ics) as $physical) {
        expect(strlen($physical))->toBeLessThanOrEqual(75);
    }

    // Folding must be transparent: unfolding gives the value back intact.
    expect(icsLines($ics))->toContain('DESCRIPTION:'.str_repeat('a', 300));
});

it('never splits a multi-byte character when folding', function () {
    $ics = app(ActivityHelper::class)->getICSContent(icsActivity([
        'comment' => str_repeat('á', 200),
    ]));

    foreach (icsPhysicalLines($ics) as $physical) {
        expect(mb_check_encoding($physical, 'UTF-8'))->toBeTrue();
    }

    expect(icsLines($ics))->toContain('DESCRIPTION:'.str_repeat('á', 200));
});

it('quotes a participant name that carries a parameter delimiter', function () {
    $ics = app(ActivityHelper::class)->getICSContent(icsActivity([
        'participants' => ['Sanchez, Alejandro'],
    ]));

    expect(icsLines($ics))->toContain(
        'ATTENDEE;ROLE=REQ-PARTICIPANT;CN="Sanchez, Alejandro";PARTSTAT=NEEDS-ACTION:MAILTO:participant@example.com'
    );
});

it('emits schedule date-times as UTC', function () {
    $ics = app(ActivityHelper::class)->getICSContent(icsActivity());

    // 09:30 in -03 is 12:30 UTC.
    expect(icsLines($ics))
        ->toContain('DTSTART:20260731T123000Z')
        ->toContain('DTEND:20260731T130000Z');
});
