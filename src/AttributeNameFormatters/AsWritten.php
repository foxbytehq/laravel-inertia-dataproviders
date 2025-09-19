<?php

namespace Foxbyte\InertiaDataProviders\AttributeNameFormatters;

class AsWritten implements AttributeNameFormatter
{
    public function __invoke(string $name): string
    {
        return $name;
    }
}
