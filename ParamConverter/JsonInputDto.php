<?php

declare(strict_types=1);

/**
 * @author Timo Förster <tfoerster@webfoersterei.de>
 * @date 02.01.21
 */

namespace Webfoersterei\Bundle\JsonParamConverterBundle\ParamConverter;

#[\Attribute(\Attribute::TARGET_CLASS)]
class JsonInputDto
{
    public function __construct()
    {
    }
}
