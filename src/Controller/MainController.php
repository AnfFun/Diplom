<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Hero;
use App\Entity\Links;
use App\Repository\CompanyRepository;
use App\Repository\HeroRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateValidator;
use function PHPUnit\Framework\isEmpty;
use function Symfony\Component\DependencyInjection\Loader\Configurator\expr;
use function Symfony\Component\String\s;

class MainController extends AbstractController
{
    #[Route("/", name: "main")]
    public function homepage(ManagerRegistry $doctrine): Response
    {
        $links = $doctrine
            ->getRepository(Links::class)
            ->find(['id' => 1]);

        return $this->render('index.html.twig', [
            'impressumUrl' => $links ? $links->getImpressum() : '#',
            'datenschutzUrl' => $links ? $links->getDatenschutz() : '#',
        ]);
    }

    #[Route("/sort", name: "sort")]
    public function sorting(EntityManagerInterface $em,
                            ManagerRegistry        $doctrine,
                            HeroRepository         $heroRepository,
                            CompanyRepository      $companyRepository,
                            Request                $request): Response
    {

        $selectedAreas = $request->get('area');
        $selectedAge = $request->get('age');
        $selectedCity = $request->get('city');
        $selectedRegion = $request->get('region');
        $selectedCountry = $request->get('country');
        $companies = $this->sort($em, $selectedAreas, $selectedCity, $selectedRegion, $selectedCountry, $selectedAge);
        $comments = $this->UserMessage($em, $selectedAreas, $selectedCity, $selectedRegion, $selectedCountry, $selectedAge, count($companies));
        $commentsAge = $this->UserMessageAge($em,$selectedAge,$selectedAreas);


        return $this->render('render_heroes.html.twig', [
            'companies' => $companies ?: null,
            'comments' => $comments,
            'commAge' => $commentsAge,
        ]);
    }

    public function sort(EntityManager $em, $selectedAreas, $selectedCity, $selectedRegion, $selectedCountry, $selectedAge)
    {
        $qb = $em->createQueryBuilder();
        $qb->select('company')
            ->from('App:Company', 'company')
            ->Join('company.hero', 'hero')
            ->andWhere('hero.visible = 1')
            ->andWhere('company.area = :varArea')
            ->setParameter('varArea', $selectedAreas);
        if ($selectedCity) {
            $qb
                ->andWhere(
                    $qb->expr()->orX(
                        $qb->expr()->eq('company.city', ':varCity'),
                        $qb->expr()->eq('company.region', ':varRegion'),
                        $qb->expr()->eq('company.country', ':varCountry')

                    )
                )
                ->setParameter('varCity', $selectedCity)
                ->setParameter('varCountry', $selectedCountry)
                ->setParameter('varRegion', $selectedRegion);
        } elseif (!$selectedCity && $selectedRegion) {
            $qb
                ->andWhere(
                    $qb->expr()->orX(
                        $qb->expr()->eq('company.region', ':varRegion'),
                        $qb->expr()->eq('company.country', ':varCountry')

                    )
                )
                ->setParameter('varRegion', $selectedRegion)
                ->setParameter('varCountry', $selectedCountry);
        } elseif (!$selectedRegion && $selectedCountry) {
            $qb
                ->andWhere('company.country = :varCountry')
                ->setParameter('varCountry', $selectedCountry);
        }
        if (count($qb->getQuery()->getResult()) > 10 && $selectedAge) {
            $qb
                ->andWhere(
                    $qb->expr()->orX(
                        $qb->expr()->eq('hero.age', ':varAge'),
                        $qb->expr()->between('hero.age', ':varAge1', ':varAge2')
                    )
                )
                ->setParameter('varAge', $selectedAge)
                ->setParameter('varAge1', $selectedAge - 10)
                ->setParameter('varAge2', $selectedAge + 10);
        }

        $qb->setMaxResults('10');

//        returntDQL();
        return $qb->getQuery()->getResult();
    }

    public function UserMessage(EntityManager $em, $selectedAreas, $selectedCity, $selectedRegion, $selectedCountry, $selectedAge, $lenght)
    {
        $qb = $em->createQueryBuilder();
        $qb->select('company')
            ->from('App:Company', 'company')
            ->Join('company.hero', 'hero')
            ->andWhere('hero.visible = 1')
            ->andWhere('company.area = :varArea')
            ->setParameter('varArea', $selectedAreas);
        if ($selectedCity) {
            $qb
                ->andWhere(
                    $qb->expr()->andX(
                        $qb->expr()->eq('company.city', ':varCity')
                    ))
                ->setParameter('varCity', $selectedCity);
            $count = count($qb->getQuery()->getResult());
            if ($count < 10) {
                return "The results that are selected in the city are not enough, taken " . $lenght - $count . " results from the region or the country";
            } elseif ($count == 0) {
                return "There are no results for the specified criteria";
            }
        }
        if ($selectedRegion) {
            $qb
                ->andWhere(
                    $qb->expr()->andX(
                        $qb->expr()->eq('company.region', ':varRegion')
                    ))
                ->setParameter('varRegion', $selectedRegion);
            $count = count($qb->getQuery()->getResult());
            if ($count < 10) {
                return "The results of those selected by region are not enough, they are taken " . $lenght - $count . " results by country";
            } elseif ($count == 0) {
                return "There are no results for the specified criteria";
            }

        }


        return null;
    }

    public function UserMessageAge(EntityManager $em, $selectedAge, $selectedAreas)
    {
        $qb = $em->createQueryBuilder();
        $qb->select('company')
            ->from('App:Company', 'company')
            ->Join('company.hero', 'hero')
            ->andWhere('hero.visible = 1')
            ->andWhere('company.area = :varArea')
            ->setParameter('varArea', $selectedAreas);
        if ($selectedAge) {
            $qb
                ->andWhere(
                    $qb->expr()->orX(
                        $qb->expr()->eq('hero.age', ':varAge'),
                        $qb->expr()->between('hero.age', ':varAge1', ':varAge2')
                    )
                )
                ->setParameter('varAge', $selectedAge)
                ->setParameter('varAge1', $selectedAge - 10)
                ->setParameter('varAge2', $selectedAge + 10);
            $count = count($qb->getQuery()->getResult());
            if ($count < 10) {
                return "There are not enough results for the selected age, but there are results with other age groups";
            } elseif ($count == 0) {
                return "There are no results for the specified criteria";
            }
        }
        return null;

    }
}