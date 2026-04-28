<?php

namespace App\Repository;

use App\Entity\Ticket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ticket>
 */
class TicketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ticket::class);
    }

    public function getDashboardTicketsStats(): array
    {
        $qb = $this->createQueryBuilder('t')
            ->select([

                // ========================
                // TOTAL
                // ========================
                'COUNT(DISTINCT t.id) as total',

                'SUM(CASE WHEN t.createdAt BETWEEN :todayStart AND :todayEnd THEN 1 ELSE 0 END) as total_today',

                'SUM(CASE WHEN t.createdAt BETWEEN :startOfWeek AND :endOfWeek THEN 1 ELSE 0 END) as total_this_week',
                'SUM(CASE WHEN t.createdAt BETWEEN :startOfLastWeek AND :endOfLastWeek THEN 1 ELSE 0 END) as total_last_week',

                // ========================
                // TYPE
                // ========================
                "SUM(CASE WHEN t.type = 'incident' THEN 1 ELSE 0 END) as incident",
                "SUM(CASE WHEN t.type = 'request' THEN 1 ELSE 0 END) as request",
                "SUM(CASE WHEN t.type = 'onboarding' THEN 1 ELSE 0 END) as onboarding",
                "SUM(CASE WHEN t.type = 'offboarding' THEN 1 ELSE 0 END) as offboarding",

                // TODAY
                "SUM(CASE WHEN t.type = 'incident' AND t.createdAt BETWEEN :todayStart AND :todayEnd THEN 1 ELSE 0 END) as incident_today",
                "SUM(CASE WHEN t.type = 'request' AND t.createdAt BETWEEN :todayStart AND :todayEnd THEN 1 ELSE 0 END) as request_today",
                "SUM(CASE WHEN t.type = 'onboarding' AND t.createdAt BETWEEN :todayStart AND :todayEnd THEN 1 ELSE 0 END) as onboarding_today",
                "SUM(CASE WHEN t.type = 'offboarding' AND t.createdAt BETWEEN :todayStart AND :todayEnd THEN 1 ELSE 0 END) as offboarding_today",

                // THIS WEEK
                "SUM(CASE WHEN t.type = 'incident' AND t.createdAt BETWEEN :startOfWeek AND :endOfWeek THEN 1 ELSE 0 END) as incident_this_week",
                "SUM(CASE WHEN t.type = 'request' AND t.createdAt BETWEEN :startOfWeek AND :endOfWeek THEN 1 ELSE 0 END) as request_this_week",
                "SUM(CASE WHEN t.type = 'onboarding' AND t.createdAt BETWEEN :startOfWeek AND :endOfWeek THEN 1 ELSE 0 END) as onboarding_this_week",
                "SUM(CASE WHEN t.type = 'offboarding' AND t.createdAt BETWEEN :startOfWeek AND :endOfWeek THEN 1 ELSE 0 END) as offboarding_this_week",

                // LAST WEEK
                "SUM(CASE WHEN t.type = 'incident' AND t.createdAt BETWEEN :startOfLastWeek AND :endOfLastWeek THEN 1 ELSE 0 END) as incident_last_week",
                "SUM(CASE WHEN t.type = 'request' AND t.createdAt BETWEEN :startOfLastWeek AND :endOfLastWeek THEN 1 ELSE 0 END) as request_last_week",
                "SUM(CASE WHEN t.type = 'onboarding' AND t.createdAt BETWEEN :startOfLastWeek AND :endOfLastWeek THEN 1 ELSE 0 END) as onboarding_last_week",
                "SUM(CASE WHEN t.type = 'offboarding' AND t.createdAt BETWEEN :startOfLastWeek AND :endOfLastWeek THEN 1 ELSE 0 END) as offboarding_last_week",

                // ========================
                // PRIORITY
                // ========================
                "SUM(CASE WHEN t.priority = 'low' THEN 1 ELSE 0 END) as low",
                "SUM(CASE WHEN t.priority = 'medium' THEN 1 ELSE 0 END) as medium",
                "SUM(CASE WHEN t.priority = 'high' THEN 1 ELSE 0 END) as high",
                "SUM(CASE WHEN t.priority = 'critical' THEN 1 ELSE 0 END) as critical",

                // TODAY
                "SUM(CASE WHEN t.priority = 'low' AND t.createdAt BETWEEN :todayStart AND :todayEnd THEN 1 ELSE 0 END) as low_today",
                "SUM(CASE WHEN t.priority = 'medium' AND t.createdAt BETWEEN :todayStart AND :todayEnd THEN 1 ELSE 0 END) as medium_today",
                "SUM(CASE WHEN t.priority = 'high' AND t.createdAt BETWEEN :todayStart AND :todayEnd THEN 1 ELSE 0 END) as high_today",
                "SUM(CASE WHEN t.priority = 'critical' AND t.createdAt BETWEEN :todayStart AND :todayEnd THEN 1 ELSE 0 END) as critical_today",

                // THIS WEEK
                "SUM(CASE WHEN t.priority = 'low' AND t.createdAt BETWEEN :startOfWeek AND :endOfWeek THEN 1 ELSE 0 END) as low_this_week",
                "SUM(CASE WHEN t.priority = 'medium' AND t.createdAt BETWEEN :startOfWeek AND :endOfWeek THEN 1 ELSE 0 END) as medium_this_week",
                "SUM(CASE WHEN t.priority = 'high' AND t.createdAt BETWEEN :startOfWeek AND :endOfWeek THEN 1 ELSE 0 END) as high_this_week",
                "SUM(CASE WHEN t.priority = 'critical' AND t.createdAt BETWEEN :startOfWeek AND :endOfWeek THEN 1 ELSE 0 END) as critical_this_week",

                // LAST WEEK
                "SUM(CASE WHEN t.priority = 'low' AND t.createdAt BETWEEN :startOfLastWeek AND :endOfLastWeek THEN 1 ELSE 0 END) as low_last_week",
                "SUM(CASE WHEN t.priority = 'medium' AND t.createdAt BETWEEN :startOfLastWeek AND :endOfLastWeek THEN 1 ELSE 0 END) as medium_last_week",
                "SUM(CASE WHEN t.priority = 'high' AND t.createdAt BETWEEN :startOfLastWeek AND :endOfLastWeek THEN 1 ELSE 0 END) as high_last_week",
                "SUM(CASE WHEN t.priority = 'critical' AND t.createdAt BETWEEN :startOfLastWeek AND :endOfLastWeek THEN 1 ELSE 0 END) as critical_last_week",

                // ========================
                // STATUS
                // ========================
                "SUM(CASE WHEN t.status = 'open' THEN 1 ELSE 0 END) as open",
                "SUM(CASE WHEN t.status = 'in_progress' THEN 1 ELSE 0 END) as in_progress",
                "SUM(CASE WHEN t.status = 'waiting_user' THEN 1 ELSE 0 END) as waiting_user",
                "SUM(CASE WHEN t.status = 'resolved' THEN 1 ELSE 0 END) as resolved",
                "SUM(CASE WHEN t.status = 'closed' THEN 1 ELSE 0 END) as closed",

                // TODAY
                "SUM(CASE WHEN t.status = 'open' AND t.createdAt BETWEEN :todayStart AND :todayEnd THEN 1 ELSE 0 END) as open_today",
                "SUM(CASE WHEN t.status = 'in_progress' AND t.createdAt BETWEEN :todayStart AND :todayEnd THEN 1 ELSE 0 END) as in_progress_today",
                "SUM(CASE WHEN t.status = 'waiting_user' AND t.createdAt BETWEEN :todayStart AND :todayEnd THEN 1 ELSE 0 END) as waiting_user_today",
                "SUM(CASE WHEN t.status = 'resolved' AND t.createdAt BETWEEN :todayStart AND :todayEnd THEN 1 ELSE 0 END) as resolved_today",
                "SUM(CASE WHEN t.status = 'closed' AND t.createdAt BETWEEN :todayStart AND :todayEnd THEN 1 ELSE 0 END) as closed_today",

                // THIS WEEK
                "SUM(CASE WHEN t.status = 'open' AND t.createdAt BETWEEN :startOfWeek AND :endOfWeek THEN 1 ELSE 0 END) as open_this_week",
                "SUM(CASE WHEN t.status = 'in_progress' AND t.createdAt BETWEEN :startOfWeek AND :endOfWeek THEN 1 ELSE 0 END) as in_progress_this_week",
                "SUM(CASE WHEN t.status = 'waiting_user' AND t.createdAt BETWEEN :startOfWeek AND :endOfWeek THEN 1 ELSE 0 END) as waiting_user_this_week",
                "SUM(CASE WHEN t.status = 'resolved' AND t.createdAt BETWEEN :startOfWeek AND :endOfWeek THEN 1 ELSE 0 END) as resolved_this_week",
                "SUM(CASE WHEN t.status = 'closed' AND t.createdAt BETWEEN :startOfWeek AND :endOfWeek THEN 1 ELSE 0 END) as closed_this_week",

                // LAST WEEK
                "SUM(CASE WHEN t.status = 'open' AND t.createdAt BETWEEN :startOfLastWeek AND :endOfLastWeek THEN 1 ELSE 0 END) as open_last_week",
                "SUM(CASE WHEN t.status = 'in_progress' AND t.createdAt BETWEEN :startOfLastWeek AND :endOfLastWeek THEN 1 ELSE 0 END) as in_progress_last_week",
                "SUM(CASE WHEN t.status = 'waiting_user' AND t.createdAt BETWEEN :startOfLastWeek AND :endOfLastWeek THEN 1 ELSE 0 END) as waiting_user_last_week",
                "SUM(CASE WHEN t.status = 'resolved' AND t.createdAt BETWEEN :startOfLastWeek AND :endOfLastWeek THEN 1 ELSE 0 END) as resolved_last_week",
                "SUM(CASE WHEN t.status = 'closed' AND t.createdAt BETWEEN :startOfLastWeek AND :endOfLastWeek THEN 1 ELSE 0 END) as closed_last_week",
            ]);

        // ========================
        // DATES
        // ========================
        $todayStart = (new \DateTime())->setTime(0, 0, 0);
        $todayEnd = (new \DateTime())->setTime(23, 59, 59);

        $startOfWeek = (new \DateTime())->modify('monday this week')->setTime(0, 0);
        $endOfWeek = (new \DateTime())->modify('sunday this week')->setTime(23, 59, 59);

        $startOfLastWeek = (new \DateTime())->modify('monday last week')->setTime(0, 0);
        $endOfLastWeek = (new \DateTime())->modify('sunday last week')->setTime(23, 59, 59);

        return $qb
            ->setParameter('todayStart', $todayStart)
            ->setParameter('todayEnd', $todayEnd)
            ->setParameter('startOfWeek', $startOfWeek)
            ->setParameter('endOfWeek', $endOfWeek)
            ->setParameter('startOfLastWeek', $startOfLastWeek)
            ->setParameter('endOfLastWeek', $endOfLastWeek)
            ->getQuery()
            ->getOneOrNullResult();
    }

//    /**
//     * @return Ticket[] Returns an array of Ticket objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Ticket
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
