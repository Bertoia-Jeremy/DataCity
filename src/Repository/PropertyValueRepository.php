<?php

namespace App\Repository;

use App\Entity\City;
use App\Entity\PropertyValue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PropertyValue|null find($id, $lockMode = null, $lockVersion = null)
 * @method PropertyValue|null findOneBy(array $criteria, array $orderBy = null)
 * @method PropertyValue[]    findAll()
 * @method PropertyValue[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PropertyValueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PropertyValue::class);
    }

    public function NULLpricePerM2OfCity(City $city)
    {
        $city_id = $city->getId();

        // Select *
        // FROM property_value as
        //  WHERE city_id = $city_id
        //TODO - Revoir comment faire les statistiques correctement (prix du m2 pour du terrain à bâtir != maison à acheter)
        $sql = "SELECT id, price, surface_field, surface_building, nature_culture, building_type
                FROM property_value
                WHERE city_id = $city_id";
        /*(SELECT count(id) as numberProperties, sum(price)/(surface_field + surface_building) as priceM2
                    FROM property_value
                    WHERE building_type = 'maison' AND city_id = $city_id)
                    as house,
                    (SELECT count(id) as numberLocal, sum(price)/(surface_field + surface_building) as priceM2
                    FROM property_value
                    WHERE building_type = 'Local industriel. commercial ou assimilé' AND city_id = $city_id)
                    as local,
                    (SELECT count(id) as numberProperties, sum(price)/(surface_field + surface_building) as priceM2
                    FROM property_value
                    WHERE building_type = 'appartement' AND city_id = $city_id)
                    as appartment,
                    (SELECT count(id) as numberProperties, sum(price)/(surface_field + surface_building) as priceM2
                    FROM property_value
                    WHERE nature_culture LIKE 'terrains a batir' AND city_id = $city_id)
                    as buildField*/
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare($sql);
        dd($result = $stmt->execute());
    }
}
