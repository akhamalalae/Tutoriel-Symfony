<?php

namespace App\Tests\UseCase\Ticket;

use App\Entity\EquipmentAsset;
use App\Entity\Ticket;
use App\Enum\TicketStatus;
use App\UseCase\Exception\ValidationException;
use App\UseCase\Ticket\ValidatorTicketUseCase;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

class ValidatorTicketUseCaseTest extends TestCase
{
    private function createValidator(?EntityManagerInterface $em = null): ValidatorTicketUseCase
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnCallback(fn (string $key) => $key);

        $em ??= $this->createMock(EntityManagerInterface::class);

        return new ValidatorTicketUseCase($translator, $em);
    }

    private function validData(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Ticket title',
            'description' => 'Ticket description',
            'type' => 'incident',
            'priority' => 'high',
            'company' => 1,
            'targetUser' => 2,
        ], $overrides);
    }

    public function testValidateThrowsWhenRequiredFieldsAreMissing(): void
    {
        $validator = $this->createValidator();

        try {
            $validator->validate([]);
            $this->fail('Expected ValidationException was not thrown');
        } catch (ValidationException $e) {
            $errors = $e->getErrors();

            $this->assertArrayHasKey('title', $errors);
            $this->assertArrayHasKey('description', $errors);
            $this->assertArrayHasKey('type', $errors);
            $this->assertArrayHasKey('priority', $errors);
            $this->assertArrayHasKey('company', $errors);
            $this->assertArrayHasKey('targetUser', $errors);
        }
    }

    public function testValidateThrowsWhenClosingWithoutResolvedStatus(): void
    {
        $validator = $this->createValidator();
        $ticket = new Ticket('Title', 'Description');

        try {
            $validator->validate($this->validData(['status' => 'closed']), $ticket);
            $this->fail('Expected ValidationException was not thrown');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('status', $e->getErrors());
            $this->assertSame('ticket.error.status_must_be_resolved', $e->getErrors()['status']);
        }
    }

    public function testValidateThrowsWhenTicketAlreadyClosed(): void
    {
        $validator = $this->createValidator();
        $ticket = new Ticket('Title', 'Description');
        $ticket->changeStatus(TicketStatus::RESOLVED);
        $ticket->changeStatus(TicketStatus::CLOSED);

        try {
            $validator->validate($this->validData(['status' => 'open']), $ticket);
            $this->fail('Expected ValidationException was not thrown');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('status', $e->getErrors());
            $this->assertSame('ticket.error.status_closed_final', $e->getErrors()['status']);
        }
    }

    public function testValidateThrowsOnboardingRequiredFields(): void
    {
        $validator = $this->createValidator();

        try {
            $validator->validate($this->validData(['type' => 'onboarding']));
            $this->fail('Expected ValidationException was not thrown');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('onboardingPosition', $e->getErrors());
            $this->assertArrayHasKey('onboardingStartDate', $e->getErrors());
        }
    }

    public function testValidatePassesWithValidData(): void
    {
        $validator = $this->createValidator();

        $validator->validate($this->validData());

        $this->addToAssertionCount(1);
    }

    public function testValidateThrowsWhenEquipmentAssetBelongsToAnotherCompany(): void
    {
        $asset = $this->createMock(EquipmentAsset::class);
        $company = $this->createMock(\App\Entity\Company::class);
        $company->method('getId')->willReturn(99);
        $asset->method('getCompany')->willReturn($company);

        $repo = $this->createMock(ObjectRepository::class);
        $repo->method('findBy')->willReturn([$asset]);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('getRepository')->with(EquipmentAsset::class)->willReturn($repo);

        $validator = $this->createValidator($em);

        try {
            $validator->validate($this->validData([
                'ticketEquipment' => [
                    ['equipmentModel' => 1, 'equipmentAsset' => 10],
                ],
            ]));
            $this->fail('Expected ValidationException was not thrown');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('company', $e->getErrors());
            $this->assertSame('ticket.error.equipment_company_mismatch', $e->getErrors()['company']);
        }
    }
}
