<?php

namespace App\Util;

use Illuminate\Support\Str;

trait HasId
{
    /**
     * The unique id.
     *
     * @var string|null
     */
    protected $uniqueId = null;

    /**
     * Get the unique id.
     *
     * @return string
     */
    public function getId()
    {
        if ( ! $this->uniqueId) {
            return $this->uniqueId = (string) Str::uuid();
        }

        return $this->uniqueId;
    }
}
