<?php

namespace App\DataFixtures;

use App\Entity\Ticket;
use App\Entity\User;
use App\Entity\Company;
use App\Entity\EquipmentAsset;
use App\Entity\EquipmentModel;
use App\Entity\TicketEquipment;
use App\Enum\TicketStatus;
use App\Enum\TicketType;
use App\Enum\TicketPriority;
use App\Enum\EquipmentStockStatus;
use App\Enum\TicketEquipmentStatus;
use App\Enum\ConditionEquipmentStock;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        /*
        =========================
        COMPANIES
        =========================
        */
        $companies = [];

        foreach (['TechCorp Paris', 'Nova Systems', 'BlueIT Services'] as $name) {
            $company = new Company($name);
            $company->setCity('Paris')
                ->setCountry('France')
                ->setEmail('contact@' . strtolower(str_replace(' ', '', $name)) . '.com');

            $manager->persist($company);
            $companies[] = $company;
        }

        /*
        =========================
        USERS (REALISTIC)
        =========================
        */
        $usersData = [
            ['Alice', 'Dupont', 'IT Support'],
            ['Marc', 'Durand', 'System Administrator'],
            ['Sophie', 'Martin', 'HR Manager'],
            ['Julien', 'Petit', 'Developer'],
            ['Laura', 'Bernard', 'IT Manager'],
        ];

        $users = [];

        $c = $companies[array_rand($companies)];

        $user = new User();         
        $user->setFirstName('alae-eddine')                 
        ->setName('Akhamal')
        ->setEmail('admin@gmail.com')
        ->setRoles(['ROLE_ADMIN'])             
        ->setPassword('$2y$13$BmSspONuY9x0VPzAsnbcveQ8kG6BaIGSFZYIGERCMw/pFiw8yZcZi')              
        ->setCompanyUser($c); 
        $manager->persist($user);

        $user = new User();         
        $user->setFirstName('user FirstName')                 
        ->setName('user Name')
        ->setEmail('user@gmail.com')
        ->setRoles(['ROLE_USER'])             
        ->setPassword('$2y$13$BmSspONuY9x0VPzAsnbcveQ8kG6BaIGSFZYIGERCMw/pFiw8yZcZi')              
        ->setCompanyUser($c); 
        $manager->persist($user);
        
        foreach ($usersData as [$first, $last, $job]) {
            $user = new User();
            $user->setFirstName($first)
                ->setName($last)
                ->setJob($job)
                ->setEmail(strtolower("$first.$last@company.com"))
                ->setPassword('$2y$13$BmSspONuY9x0VPzAsnbcveQ8kG6BaIGSFZYIGERCMw/pFiw8yZcZi')
                ->setCompanyUser($companies[array_rand($companies)]);

            $manager->persist($user);
            $users[] = $user;
        }

        /*
        =========================
        EQUIPMENT MODELS (IMPORTANT)
        =========================
        */
        $modelsData = [
            'Apple MacBook Pro',
            'Apple MacBook Air',
            'Dell Latitude',
            'HP EliteBook',
            'Lenovo ThinkPad',
            'Dell UltraSharp Monitor',
            'IPad',
            'Mouse',
            'Keyboard',
            'Cisco IP Phone',
            'iPhone',
            'Samsung Galaxy',
        ];

        $models = [];

        foreach ($modelsData as $name) {
            $model = new EquipmentModel();
            $model->setName($name)
                ->setCompany($companies[array_rand($companies)]);

            $manager->persist($model);
            $models[] = $model;
        }

        /*
        =========================
        EQUIPMENT ASSETS (REAL STOCK)
        =========================
        */
        $assets = [];

        foreach ($models as $model) {
            for ($i = 0; $i < rand(2, 5); $i++) {

                $asset = new EquipmentAsset();
                $asset->setReference($model->getName())
                    ->setSerialNumber('SN-' . strtoupper(substr(md5(uniqid()), 0, 8)))
                    ->setAssetTag('AST-' . rand(10000, 99999))
                    ->setCompany($model->getCompany())
                    ->setStatus(EquipmentStockStatus::AVAILABLE)
                    ->setAssetCondition(
                        ConditionEquipmentStock::cases()[array_rand(ConditionEquipmentStock::cases())]
                    )
                    ->setLocation('Paris HQ - Floor ' . rand(1, 5))
                    ->setStorageLocation('Stock Room ' . rand(1, 3));

                $manager->persist($asset);
                $assets[] = $asset;
            }
        }

        /*
        =========================
        TICKETS (REAL SCENARIOS)
        =========================
        */
        $ticketsData = [
            [
                'title' => 'MacBook Pro not starting',
                'desc' => 'User cannot boot MacBook Pro, black screen at startup.',
                'type' => TicketType::INCIDENT,
                'priority' => TicketPriority::HIGH,
                'status' => TicketStatus::OPEN,
            ],
            [
                'title' => 'Request new laptop for developer',
                'desc' => 'Need Dell or MacBook for new project onboarding.',
                'type' => TicketType::REQUEST,
                'priority' => TicketPriority::MEDIUM,
                'status' => TicketStatus::OPEN,
            ],
            [
                'title' => 'Monitor flickering issue',
                'desc' => 'Dell UltraSharp screen flickers intermittently.',
                'type' => TicketType::INCIDENT,
                'priority' => TicketPriority::MEDIUM,
                'status' => TicketStatus::IN_PROGRESS,
            ],
            [
                'title' => 'Employee onboarding - IT setup',
                'desc' => 'Prepare laptop, email, VPN and access rights.',
                'type' => TicketType::ONBOARDING,
                'priority' => TicketPriority::HIGH,
                'status' => TicketStatus::IN_PROGRESS,
            ],
            [
                'title' => 'Employee offboarding',
                'desc' => 'Revoke access and recover all equipment.',
                'type' => TicketType::OFFBOARDING,
                'priority' => TicketPriority::HIGH,
                'status' => TicketStatus::RESOLVED,
            ],
        ];

        foreach ($ticketsData as $data) {

            $ticket = new Ticket(
                $data['title'],
                $data['desc']
            );

            $ticket->setType($data['type'])
                ->setPriority($data['priority'])
                ->setStatus($data['status'])
                ->setCompany($companies[array_rand($companies)])
                ->setCreatedBy($users[array_rand($users)])
                ->setAssignedTo($users[array_rand($users)]);

            $manager->persist($ticket);
        }

        $manager->flush();
    }
}