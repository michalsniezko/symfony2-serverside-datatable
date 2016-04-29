<?php
namespace IMTech\Common\CommonBundle\Controller\API\WorkRelated;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class WorkRelatedApiController extends FOSRestController
{
    private $routeToEntityDictionary = [
        'api_getactorslist' => 'FelixAppBundle:Actor',
        'api_getproducerslist' => 'FelixAppBundle:Producer',
        'api_getdirectorslist' => 'FelixAppBundle:Director',
        'api_getcartoonslist' => 'FelixAppBundle:Cartoon',
        'api_getcomposerslist' => 'FelixAppBundle:Composer',
        'api_getscreenwriterslist' => 'FelixAppBundle:ScreenWriter',
        'api_getscriptwriterslist' => 'FelixAppBundle:ScriptWriter',
    
    ];
    
    /**
     * @Get("actorslist", name="api_getactorslist")
     * @Get("producerslist", name="api_getproducerslist")
     * @Get("directorslist", name="api_getdirectorslist")
     * @Get("cartoonslist", name="api_getcartoonslist")
     * @Get("composerslist", name="api_getcomposerslist")
     * @Get("screenwriterslist", name="api_getscreenwriterslist")
     * @Get("scriptwriterslist", name="api_getscriptwriterslist")
     */
    public function getWorkRelatedListAction(Request $request)
    {
        $entityType = $this->routeToEntityDictionary[$request->get('_route')];
        
        $get = $request->query->all();
        
        $columns = array('id', 'firstName', 'surname');
        $get['columns'] = &$columns;
        
        $em = $this->getDoctrine()->getEntityManager();
        $rResult = $em->getRepository($entityType)
                      ->ajaxTable($get, true)
                      ->getArrayResult();
        
        $output = array(
            "sEcho" => intval($get['sEcho']),
            "iTotalRecords" => $em->getRepository($entityType)->getCount(),
            "iTotalDisplayRecords" => $em->getRepository($entityType)->getFilteredCount($get),
            "aaData" => array(),
        );
        
        foreach($rResult as $aRow)
        {
            $row = array();
            for($i = 0; $i < count($columns); $i++)
            {
                if($columns[$i] == "version")
                {
                    /* Special output formatting for 'version' column */
                    $row[] = ($aRow[$columns[$i]] == "0") ? '-' : $aRow[$columns[$i]];
                }
                elseif($columns[$i] != ' ')
                {
                    /* General output */
                    $row[] = $aRow[$columns[$i]];
                }
            }
            $output['aaData'][] = $row;
        }
        unset($rResult);
        
        return new Response(
            json_encode($output)
        );
    }
    
}