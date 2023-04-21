<?php

namespace App\Utils;

class StringCoder
{
    /**
     * Whether the stringcode's name equals the given name.
     *
     * @param string $stringcode
     * @param string $name
     * @return boolean
     */
    public function is($stringcode, $name)
    {
        return str_starts_with($stringcode, $name . '?');
    }

    /**
     * Encode the name and data into a stringcode
     *
     * @param string $name
     * @param array $data
     * @return string
     */
    public function encode($name, $data)
    {
        return $name . '?' . http_build_query($data, '', '&');
    }

    /**
     * Decode the stringcode params.
     *
     * @param string $stringcode
     * @return array
     */
    public function decode($stringcode)
    {
        $parts = parse_url($stringcode);

        parse_str($parts['query'], $values);

        return $values;
    }
}
