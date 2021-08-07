<?php

namespace Mapbender\CoordinatesUtilityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Mapbender\CoreBundle\Entity\SRS;

class CoordinatesUtilityController extends Controller
{
    /**
     * Quantity of query results
     */
    const RESULTS_QUANTITY = 10;

    /**
     * Provide autocomplete for SRS
     *
     * @Route("/srs-autocomplete", name="srs_autocomplete", options={"expose"=true})
     * @param Request $request
     * @return JsonResponse
     */
    public function srsAutocompleteAction(Request $request)
    {
        $term = $request
            ->query
            ->get('term');

        $repository = $this
            ->getDoctrine()
            ->getRepository(SRS::class);

        $query = $repository
            ->createQueryBuilder('srs')
            ->select("CONCAT(srs.name, ' | ', srs.title) as name")
            ->where('srs.name LIKE :term')
            ->setParameter('term', '%'.$term.'%')
            ->setMaxResults(self::RESULTS_QUANTITY)
            ->getQuery();

        $srsArray = $query->getResult();

        return new JsonResponse(array_column($srsArray, 'name'));
    }
}
