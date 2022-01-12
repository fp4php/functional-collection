<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection;

use Whsv26\Functional\Stream\Stream;

/**
 * @internal
 */
final class HashComparator
{
    public static function hashEquals(mixed $lhs, mixed $rhs): bool
    {
        if ($lhs instanceof HashContract) {
            return $lhs->equals($rhs);
        } elseif ($rhs instanceof HashContract) {
            return $rhs->equals($lhs);
        } else {
            return self::tryToHash($lhs) === self::tryToHash($rhs);
        }
    }

    /**
     * @template T
     * @param T $subject
     * @return T|string
     */
    public static function tryToHash(mixed $subject): mixed
    {
        if (is_object($subject)) {
            return $subject instanceof HashContract
                ? $subject->hashCode()
                : spl_object_hash($subject);
        } elseif (is_array($subject)) {
            return Stream::emits($subject)
                ->map(fn($elem): mixed => self::tryToHash($elem))
                ->compile()
                ->mkString('[', ',', ']');
        } else {
            return $subject;
        }
    }
}
