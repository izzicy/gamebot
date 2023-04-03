<?php

namespace App\Routines\Concerns;

use Illuminate\Support\Str;

trait HasId
{
    /** @var string|null */
    protected $uniqueId = null;

    /**
     * Get the unique id.
     *
     * @return string
     */
    public function id()
    {
        return (
            $this->uniqueId
            ?? (
                $this->uniqueId = (string)Str::uuid()
            )
        );
    }
}
