<?php

declare(strict_types=1);

use App\Entity\Label;
use App\Exception\Label\LabelNotFoundException;
use App\Repository\LabelRepository;
use App\Service\Label\GetLabelByIdService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GetLabelByIdServiceTest extends TestCase
{
    protected LabelRepository|MockObject $labelRepository;

    private GetLabelByIdService $service;


    public function setUp(): void
    {
        parent::setUp();

        $this->labelRepository = $this->getMockBuilder(LabelRepository::class)
            ->disableOriginalConstructor()->getMock();

        $this->service = new GetLabelByIdService($this->labelRepository);
    }


    public function testFindLabelById_ok(): void
    {
        $id = 1;
        $description = 'CERO EMISIONES';
        $mockedLabel = new Label($id, '16T0', $description);

        $this->labelRepository
            ->expects($this->once())
            ->method('findLabelByIdOrFail')
            ->with($id)
            ->willReturn($mockedLabel);

        $label = $this->service->findLabelById($id);

        $this->assertEquals($mockedLabel->getTag(), $label->getTag());
        $this->assertEquals($mockedLabel->getDescription(), $label->getDescription());
    }

    public function testFindLabelById_ko(): void
    {
        $id = 99;
        $exceptionMessage = \sprintf('Environmental label with ID "%s" not found.', $id);

        $this->labelRepository
            ->expects($this->once())
            ->method('findLabelByIdOrFail')
            ->with($id)
            ->willThrowException(new LabelNotFoundException($exceptionMessage));

        $this->expectException(LabelNotFoundException::class);
        $this->expectExceptionMessage($exceptionMessage);

        $this->service->findLabelById($id);
    }

}
