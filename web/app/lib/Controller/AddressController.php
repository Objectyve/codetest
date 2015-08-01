<?php
namespace Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AddressController
{
    public function getAllAction(Application $app)
    {
        return new JsonResponse($app['db']->fetchAll("SELECT * FROM address"));
    }

    public function getOneAction($id, Application $app)
    {
        return new JsonResponse($app['db']
            ->fetchAssoc("SELECT * FROM address WHERE id=:ID", ['ID' => $id]));
    }

    public function deleteOneAction($id, Application $app)
    {
        return $app['db']->delete('address', ['ID' => $id]);
    }

    public function addOneAction(Application $app, Request $request)
    {
        $payload = json_decode($request->getContent());;

        $geocoder = $app['geocoder'];

        $result   = $geocoder->geocode($payload->street.",".$payload->city.",".$payload->state);

        /*
        echo "Street: " . $result->getStreetNumber() . " ".$result->getStreetName()."\n";
        echo "City: " . $result->getCity() . "\n";
        echo "State: " . $result->getRegion() . "\n";
        echo "Zip: " . $result->getZipCode() . "\n";
        */

        #print_r($result); exit;

        // AIzaSyBzM7VfVA8uhFxLnVDlwpUFJzaJBhLEtn0

        $newResource = [
            'id'      => (integer)$app['db']
                    ->fetchColumn("SELECT max(id) FROM address") + 1,
            'street'  => $result->getStreetNumber() . " ".$result->getStreetName(),
            'city' => $result->getCity(),
            'state' => $result->getRegion(),
            'postalcode' => $result->getZipCode(),
        ];

        $app['db']->insert('address', $newResource);

        return new JsonResponse($newResource);
    }

    public function editOneAction($id, Application $app, Request $request)
    {
        $payload = json_decode($request->getContent());;

        $geocoder = $app['geocoder'];

        $result   = $geocoder->geocode($payload->street.",".$payload->city.",".$payload->state);

        $resource = [
            'street'  => $result->getStreetNumber() . " ".$result->getStreetName(),
                'city' => $result->getCity(),
                'state' => $result->getRegion(),
                'postalcode' => $result->getZipCode(),
        ];

        $app['db']->update('address', $resource, ['id' => $id]);

        return new JsonResponse($resource);
    }
}
