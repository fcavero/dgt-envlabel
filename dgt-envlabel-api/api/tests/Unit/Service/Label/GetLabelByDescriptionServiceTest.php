<?php

declare(strict_types=1);

use App\Entity\Label;
use App\Exception\Label\LabelNotFoundException;
use App\Repository\LabelRepository;
use App\Service\Label\GetLabelByDescriptionService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GetLabelByDescriptionServiceTest extends TestCase
{
    protected LabelRepository|MockObject $labelRepository;

    private GetLabelByDescriptionService $service;


    public function setUp(): void
    {
        parent::setUp();

        $this->labelRepository = $this->getMockBuilder(LabelRepository::class)
            ->disableOriginalConstructor()->getMock();

        $this->service = new GetLabelByDescriptionService($this->labelRepository);
    }


    public function testFindLabelByDescription_ok(): void
    {
        $description = 'CERO EMISIONES';
        $mockedLabel = new Label(1, '16T0', $description);

        $this->labelRepository
            ->expects($this->once())
            ->method('findLabelByDescriptionOrFail')
            ->with($description)
            ->willReturn($mockedLabel);

        $label = $this->service->findLabelByDescription($description);

        $this->assertEquals($mockedLabel->getId(), $label->getId());
        $this->assertEquals($mockedLabel->getTag(), $label->getTag());
    }

    public function testFindLabelByDescription_ko(): void
    {
        $fakeDescription = 'SIN DISTINTIVO ALGUNO';
        $exceptionMessage = \sprintf('Environmental label with description "%s" not found.', $fakeDescription);

        $this->labelRepository
            ->expects($this->once())
            ->method('findLabelByDescriptionOrFail')
            ->with($fakeDescription)
            ->willThrowException(new LabelNotFoundException($exceptionMessage));

        $this->expectException(LabelNotFoundException::class);
        $this->expectExceptionMessage($exceptionMessage);

        $this->service->findLabelByDescription($fakeDescription);
    }

}
