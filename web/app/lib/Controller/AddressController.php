<?php
namespace Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Geocoder\Exception\NoResultException;
use Geocoder\Exception\QuotaExceededException;
use Geocoder\Exception\Exception;


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

        $concat_input = $payload->street.",".$payload->city.",".$payload->state;

        $geocoder = $app['geocoder'];

        $process_api = true;




        // clean old entries out of the database
        //$app['db']->delete('address_geocoding_json WHERE data_added!= CURDATE()');

        // check the database to see if the json has been cached today
        $cache_check = $app['db']->fetchAssoc("SELECT json FROM address_geocoding_json WHERE address=:ADDRESS", ['ADDRESS' => $concat_input]);

        //print_r($check); die();
        /*
        $resource = [
            'error' => $check
        ];
        */
        //return new JsonResponse($resource);


        if (!$cache_check) { // if not cached, fetch the json from Google

            // cache in the database for future use

            try {
                $result = $geocoder->geocode($payload->street . "," . $payload->city . "," . $payload->state);
            } catch (\Geocoder\Exception\NoResultException $e) {
                $result = "No result was returned by the geocoder, the address appears to be malformed";
                //print_r($result); die();
                $process_api = false;
            } catch (\Geocoder\Exception\QuotaExceededException $e) {
                $result = "We met our daily quota";
                $process_api = false;
            } catch (Exception $e) {
                $result = "Error: " . $e->getMessage();
                $process_api = false;
            }


            #print_r($result); exit;

            // AIzaSyBzM7VfVA8uhFxLnVDlwpUFJzaJBhLEtn0

            if ($process_api) {
                $newResource = [
                    'id' => (integer)$app['db']
                            ->fetchColumn("SELECT max(id) FROM address") + 1,
                    'street' => $result->getStreetNumber() . " " . $result->getStreetName(),
                    'city' => $result->getCity(),
                    'state' => $result->getRegion(),
                    'postalcode' => $result->getZipCode(),
                ];

                $app['db']->insert('address', $newResource);

                if ($_SERVER['REMOTE_ADDR'] == '71.160.4.106') {

                    // insert into cache
                    $newCacheResource = [
                        'address' => $concat_input,
                        'json' => '', //$result->getJson(),
                        'date_added' => date('Y-m-d h:i:s') // 'NOW()',

                    ];

                    //$sql2 = "INSERT INTO address_geocoding_json (address,json,date_added) VALUES (?,?,NOW())";
                    $app['db']->insert('address_geocoding_json', $newCacheResource);
                }


            } else {
                // ap error
                $newResource = [
                    'error' => $result
                ];
            }

        } else {
            // address already cached
            $newResource = [
                'error' => 'cached'
            ];
        }

        return new JsonResponse($newResource);
    }

    public function editOneAction($id, Application $app, Request $request)
    {
        $payload = json_decode($request->getContent());;

        $geocoder = $app['geocoder'];

        $process_api = true;

        try {
            $result   = $geocoder->geocode($payload->street.",".$payload->city.",".$payload->state);
        } catch (\Geocoder\Exception\NoResultException $e) {
            $result = "No result was returned by the geocoder, the address appears to be malformed";
            //print_r($result); die();
            $process_api = false;
        } catch (\Geocoder\Exception\QuotaExceededException $e) {
            $result = "We met our daily quota";
            $process_api = false;
        } catch (Exception $e) {
            $result = "Error: " . $e->getMessage();
            $process_api = false;
        }

        if ($process_api) {
            $resource = [
                'street'  => $result->getStreetNumber() . " ".$result->getStreetName(),
                'city' => $result->getCity(),
                'state' => $result->getRegion(),
                'postalcode' => $result->getZipCode(),
            ];

            $app['db']->update('address', $resource, ['id' => $id]);
        } else {
            $resource = [
                'error' => $result
            ];
        }

        return new JsonResponse($resource);
    }
}
