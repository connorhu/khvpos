<?php declare(strict_types=1);

namespace KHTools\VPos\Payum;

final class Constants
{
    /** Payment created, waiting for customer action */
    public const STATUS_PENDING = 1;
    /** Customer interaction in progress */
    public const STATUS_PROCESSING = 2;
    /** Cancelled (by customer or timeout) */
    public const STATUS_CANCELLED = 3;
    /** Payment confirmed — money taken */
    public const STATUS_CONFIRMED = 4;
    /** Pre-authorized — awaiting close/capture */
    public const STATUS_AUTHORIZED = 5;
    /** Rejected by bank */
    public const STATUS_REJECTED = 6;
    /** Payment reversed */
    public const STATUS_REVERSED = 7;
    /** Payment closed */
    public const STATUS_CLOSED = 8;

    /** Success */
    public const RESULT_OK = 0;
    /** 3DS authentication in progress */
    public const RESULT_3DS_IN_PROGRESS = 150;
}
