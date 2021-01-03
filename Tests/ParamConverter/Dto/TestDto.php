<?php

declare(strict_types=1);

/**
 * @author Timo Förster <tfoerster@webfoersterei.de>
 * @date 02.01.21
 */

namespace Webfoersterei\Bundle\JsonParamConverterBundle\Tests\ParamConverter\Dto;

use Webfoersterei\Bundle\JsonParamConverterBundle\ParamConverter\JsonDto;

#[JsonDto]
class TestDto
{
    public string $testProperty;
}
