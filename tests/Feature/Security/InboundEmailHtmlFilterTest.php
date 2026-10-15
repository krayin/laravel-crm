<?php

use Webkul\Email\Helpers\HtmlFilter;
use Webkul\Email\InboundEmailProcessor\SendgridEmailProcessor;
use Webkul\Email\InboundEmailProcessor\WebklexImapEmailProcessor;

/**
 * An inbound message's body is filtered however it arrives.
 *
 * The body comes from outside the installation and the mailbox renders what is stored, so it is
 * stripped of scripting on the way in. One branch of the webhook processor did this and the other
 * did not, which left a record's safety depending on whether the message happened to continue an
 * existing conversation; the mailbox poller did not do it at all.
 */
it('strips scripting from a body it is given', function () {
    $filtered = app(HtmlFilter::class)->process(
        '<p>Hello</p><script>alert(document.domain)</script><img src=x onerror=alert(1)>',
        ''
    );

    expect($filtered)->toContain('<p>Hello</p>')
        ->and($filtered)->not->toContain('<script')
        ->and($filtered)->not->toContain('onerror');
});

/**
 * Both branches of the webhook processor store the same thing, so both are read here rather than
 * only the one a given message happens to take.
 */
it('filters the body in every branch of the webhook processor', function () {
    $source = file_get_contents(
        (new ReflectionClass(SendgridEmailProcessor::class))->getFileName()
    );

    expect($source, 'a body must not be stored unfiltered')
        ->not->toContain("'reply' => \$reply,");

    expect(substr_count($source, 'htmlFilter->process'))
        ->toBeGreaterThanOrEqual(2);
});

/**
 * The mailbox poller reads the same kind of message from an external account.
 */
it('filters the body read from a mailbox', function () {
    $source = file_get_contents(
        (new ReflectionClass(WebklexImapEmailProcessor::class))->getFileName()
    );

    expect($source)->toContain('htmlFilter->process');

    expect($source, 'the raw body must not be stored as it arrives')
        ->not->toContain("'reply' => \$message->bodies");
});

/**
 * The filter has to be reachable where it is used, so a message does not fail on a missing
 * dependency at the moment one actually arrives.
 */
it('gives both processors the filter they depend on', function () {
    foreach ([SendgridEmailProcessor::class, WebklexImapEmailProcessor::class] as $processor) {
        $parameters = (new ReflectionClass($processor))->getConstructor()->getParameters();

        $types = array_map(fn ($parameter) => (string) $parameter->getType(), $parameters);

        expect($types)->toContain(HtmlFilter::class);
    }
});
