<?php
declare(strict_types=1);

namespace App\Tests\Helper;

use EasyRdf\Graph;
use function assert;
use function count;
use function dirname;
use function file_get_contents;
use function file_put_contents;
use function getenv;
use function is_dir;
use function is_string;
use function json_decode;
use function json_encode;
use function mkdir;
use function preg_replace_callback;
use function rtrim;
use function sprintf;
use const JSON_PRETTY_PRINT;
use const JSON_THROW_ON_ERROR;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

/**
 * Golden-file (snapshot) assertions for the RDF rendering layer.
 *
 * Rendered Turtle and JSON-LD output is compared byte-for-byte against fixture
 * files committed in tests/fixtures/rdf/. These fixtures act as the primary
 * guard when upgrading the easyrdf / arc2 / json-ld dependencies: an upgrade
 * that changes the serialised output will fail these tests with a diff.
 *
 * Nondeterministic tokens (blank node labels, UUIDs) are normalised before
 * comparison. Timestamps are NOT normalised on purpose: tests must freeze any
 * rendered timestamps (e.g. via reflection on createdAt/updatedAt) so that the
 * exact serialisation format stays under test.
 *
 * To (re)generate the fixtures after an intentional change, run:
 *
 *   UPDATE_RDF_SNAPSHOTS=1 vendor/bin/phpunit --testsuite unit
 */
trait RdfSnapshotAssertions
{
    /** Asserts both the Turtle and the JSON-LD serialisation of the graph against snapshots. */
    protected static function assertGraphMatchesSnapshots(Graph $graph, string $snapshotName): void
    {
        self::assertTurtleMatchesSnapshot($graph, $snapshotName);
        self::assertJsonLdMatchesSnapshot($graph, $snapshotName);
    }

    protected static function assertTurtleMatchesSnapshot(Graph $graph, string $snapshotName): void
    {
        $turtle = $graph->serialise('turtle');
        assert(is_string($turtle));

        self::assertRdfMatchesSnapshotFile(self::normalizeRdf($turtle), $snapshotName . '.ttl');
    }

    protected static function assertJsonLdMatchesSnapshot(Graph $graph, string $snapshotName): void
    {
        $jsonLd = $graph->serialise('jsonld');
        assert(is_string($jsonLd));

        $pretty = json_encode(
            json_decode($jsonLd, true, 512, JSON_THROW_ON_ERROR),
            JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );

        self::assertRdfMatchesSnapshotFile(self::normalizeRdf($pretty), $snapshotName . '.jsonld');
    }

    private static function assertRdfMatchesSnapshotFile(string $actual, string $fileName): void
    {
        $path = self::rdfSnapshotPath($fileName);

        if (self::shouldUpdateRdfSnapshots()) {
            $directory = dirname($path);

            if (! is_dir($directory)) {
                mkdir($directory, 0775, true);
            }

            file_put_contents($path, $actual);
        }

        self::assertFileExists($path, sprintf(
            'Missing RDF snapshot tests/fixtures/rdf/%s. ' .
            'Run `UPDATE_RDF_SNAPSHOTS=1 vendor/bin/phpunit --testsuite unit` to create it.',
            $fileName
        ));

        $expected = file_get_contents($path);
        assert(is_string($expected));

        self::assertSame($expected, $actual, sprintf(
            'Rendered RDF does not match snapshot tests/fixtures/rdf/%s. ' .
            'If this change is intentional, refresh the snapshots with ' .
            '`UPDATE_RDF_SNAPSHOTS=1 vendor/bin/phpunit --testsuite unit` and commit the diff.',
            $fileName
        ));
    }

    /**
     * Replaces nondeterministic tokens with stable sequential placeholders and
     * enforces a single trailing newline.
     */
    private static function normalizeRdf(string $rdf): string
    {
        // Blank node labels (e.g. _:genid1) are serialiser-generated and not stable.
        $rdf = self::replaceSequentially($rdf, '/_:[A-Za-z][A-Za-z0-9]*/', '_:blank%d');

        // UUIDs (entity ids) are generated and not stable; tests should avoid
        // rendering them, but normalise as a safety net.
        $rdf = self::replaceSequentially(
            $rdf,
            '/[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}/i',
            'uuid-%d'
        );

        return rtrim($rdf, "\n") . "\n";
    }

    private static function replaceSequentially(string $subject, string $pattern, string $replacementFormat): string
    {
        /** @var array<string, string> $replacements */
        $replacements = [];

        return (string) preg_replace_callback(
            $pattern,
            static function (array $matches) use (&$replacements, $replacementFormat): string {
                $replacements[$matches[0]] ??= sprintf($replacementFormat, count($replacements) + 1);

                return $replacements[$matches[0]];
            },
            $subject
        );
    }

    private static function rdfSnapshotPath(string $fileName): string
    {
        return dirname(__DIR__) . '/fixtures/rdf/' . $fileName;
    }

    private static function shouldUpdateRdfSnapshots(): bool
    {
        $value = getenv('UPDATE_RDF_SNAPSHOTS');

        return $value !== false && $value !== '' && $value !== '0';
    }
}
