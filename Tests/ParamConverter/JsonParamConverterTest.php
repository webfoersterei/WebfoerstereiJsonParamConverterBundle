<?php

declare(strict_types=1);

/**
 * @author Timo Förster <tfoerster@webfoersterei.de>
 * @date 02.01.21
 */

namespace Webfoersterei\Bundle\JsonParamConverterBundle\Tests\ParamConverter;

use PHPUnit\Framework\MockObject\MockObject;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\RecursiveValidator;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Webfoersterei\Bundle\JsonParamConverterBundle\ParamConverter\JsonParamConverter;
use PHPUnit\Framework\TestCase;
use Webfoersterei\Bundle\JsonParamConverterBundle\Tests\ParamConverter\Dto\NoDto;
use Webfoersterei\Bundle\JsonParamConverterBundle\Tests\ParamConverter\Dto\TestDto;

class JsonParamConverterTest extends TestCase
{
    private JsonParamConverter $converter;

    public function testApply(): void
    {
        $payload = ['testProperty' => 'valu3'];
        $request = new Request(
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode($payload, JSON_THROW_ON_ERROR)
        );
        $config = $this->createConfiguration(TestDto::class);

        $ret = $this->converter->apply($request, $config);

        self::assertTrue($ret);
        /** @var TestDto $arg */
        $arg = $request->attributes->get('arg');
        self::assertInstanceOf(TestDto::class, $arg);
        self::assertEquals('valu3', $arg->testProperty);
    }

    /**
     * @param null $class
     * @param string $name
     * @param false $isOptional
     *
     * @return ParamConverter|MockObject
     */
    public function createConfiguration($class = null, $name = 'arg', $isOptional = false): ParamConverter|MockObject
    {
        $methods = ['getClass', 'getAliasName', 'getOptions', 'getName', 'allowArray'];
        if (null !== $isOptional) {
            $methods[] = 'isOptional';
        }
        $config = $this
            ->getMockBuilder(ParamConverter::class)
            ->setMethods($methods)
            ->disableOriginalConstructor()
            ->getMock();
        if (null !== $class) {
            $config->expects(self::atLeastOnce())
                   ->method('getClass')
                   ->willReturn($class);
        }
        $config->method('getName')
               ->willReturn($name);

        return $config;
    }

    public function testApplyValidationFailed(): void
    {
        $payload = ['testProperty' => 'wrongValue'];
        $request = new Request(
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode($payload, JSON_THROW_ON_ERROR)
        );
        $config = $this->createConfiguration(TestDto::class);

        $expectedConstraintViolations = new ConstraintViolationList();
        $expectedConstraintViolations->add(
            new ConstraintViolation('Test Violation', null, [], null, 'testProperty', 'wrongValue')
        );
        $this->converter = new JsonParamConverter(
            $this->getSerializer(),
            $this->getValidator($expectedConstraintViolations)
        );
        $ret = $this->converter->apply($request, $config);

        self::assertTrue($ret);
        /** @var TestDto $arg */
        $arg = $request->attributes->get('arg');
        self::assertInstanceOf(TestDto::class, $arg);
        self::assertEquals('wrongValue', $arg->testProperty);
        /** @var ConstraintViolationListInterface $constraintViolations */
        $constraintViolations = $request->attributes->get(JsonParamConverter::VALIDATION_ERRORS_ARGUMENT);
        self::assertInstanceOf(ConstraintViolationListInterface::class, $constraintViolations);
        self::assertEquals($expectedConstraintViolations, $constraintViolations);
    }

    protected function getSerializer(): Serializer
    {
        return new Serializer(
            [new ArrayDenormalizer(), new ObjectNormalizer(), new DateTimeNormalizer()],
            [new JsonEncoder()]
        );
    }

    protected function getValidator(ConstraintViolationListInterface $constraintViolationList = null)
    {
        $mock = $this->getMockBuilder(RecursiveValidator::class)
                     ->disableOriginalConstructor()
                     ->onlyMethods(['validate'])
                     ->getMock();

        if ($constraintViolationList) {
            $mock->expects(self::once())->method('validate')->willReturn($constraintViolationList);
        }

        return $mock;
    }

    public function testSupports(): void
    {
        self::assertTrue($this->converter->supports($this->createConfiguration(TestDto::class)));
    }

    public function testSupportsNoClass(): void
    {
        self::assertFalse($this->converter->supports($this->createConfiguration()));
    }

    public function testSupportsClassNoJsonDto(): void
    {
        self::assertFalse($this->converter->supports($this->createConfiguration(NoDto::class)));
    }

    protected function setUp(): void
    {
        parent::setUp();
        /** @var SerializerInterface $serializer */
        $serializer = $this->getSerializer();
        /** @var ValidatorInterface $validator */
        $validator = $this->getValidator();
        $this->converter = new JsonParamConverter($serializer, $validator);
    }
}
