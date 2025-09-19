<?php

namespace Foxbyte\InertiaDataProviders\AttributeNameFormatters;

interface AttributeNameFormatter
{
    public function __invoke(string $name): string;
}
