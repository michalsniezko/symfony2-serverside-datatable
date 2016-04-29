<?php

namespace IMTech\Felix\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;

class WorkRelatedRepository extends EntityRepository
{
    private $alias = 'wr';

    /**
     * @param array $get
     * @param bool  $flag
     * @return array|\Doctrine\ORM\Query
     */
    public function ajaxTable(array $get, $flag = false)
    {
        if(!isset($get['columns']) || empty($get['columns']))
        {
            $get['columns'] = array('id');
        }
        
        $aColumns = array();
        foreach($get['columns'] as $value)
        {
            $aColumns[] = $this->alias . '.' . $value;
        }
        
        $cb = $this->getEntityManager()
                   ->getRepository($this->_entityName)
                   ->createQueryBuilder($this->alias)
                   ->select(str_replace(" , ", " ", implode(", ", $aColumns)));
        
        if(isset($get['iDisplayStart']) && $get['iDisplayLength'] != '-1')
        {
            $cb->setFirstResult((int)$get['iDisplayStart'])
               ->setMaxResults((int)$get['iDisplayLength']);
        }
        
        if(isset($get['iSortCol_0']))
        {
            for($i = 0; $i < intval($get['iSortingCols']); $i++)
            {
                if($get['bSortable_' . intval($get['iSortCol_' . $i])] == "true")
                {
                    $cb->orderBy($aColumns[(int)$get['iSortCol_' . $i]], $get['sSortDir_' . $i]);
                }
            }
        }
        
        if(isset($get['sSearch']) && $get['sSearch'] != '')
        {
            $aLike = array();
            for($i = 0; $i < count($aColumns); $i++)
            {
                if(isset($get['bSearchable_' . $i]) && $get['bSearchable_' . $i] == "true")
                {
                    $aLike[] = $cb->expr()->like($aColumns[$i], '\'%' . $get['sSearch'] . '%\'');
                }
            }
            if(count($aLike) > 0)
                $cb->andWhere(new Expr\Orx($aLike));
            else unset($aLike);
        }
        
        $query = $cb->getQuery();
        $query->useResultCache(true, 60, 'work_related_ajax_table');

        if($flag)
            return $query;
        else
            return $query->getResult();
    }
    
    /**
     * @return int
     */
    public function getCount()
    {
        $aResultTotal = $this->getEntityManager()
                             ->createQuery('SELECT COUNT(' . $this->alias . ') FROM ' . $this->_entityName . ' ' . $this->alias)
                             ->useResultCache(true, 60, 'work_related_get_count')
                             ->setMaxResults(1)
                             ->getResult();
        
        return $aResultTotal[0][1];
    }
    
    public function getFilteredCount(array $get)
    {
        /* DB table to use */
    
        $aColumns = array();
        foreach($get['columns'] as $value)
        {
            $aColumns[] = $this->alias . '.' . $value;
        }
    
    
        $cb = $this->getEntityManager()
                   ->getRepository($this->_entityName)
                   ->createQueryBuilder($this->alias)
                   ->select("count(" . $this->alias . ".id)");
        
        /*
        * Filtering
        * NOTE this does not match the built-in DataTables filtering which does it
        * word by word on any field. It's possible to do here, but concerned about efficiency
        * on very large tables, and MySQL's regex functionality is very limited
        */
        if(isset($get['sSearch']) && $get['sSearch'] != '')
        {
            $aLike = array();
            for($i = 0; $i < count($aColumns); $i++)
            {
                if(isset($get['bSearchable_' . $i]) && $get['bSearchable_' . $i] == "true")
                {
                    $aLike[] = $cb->expr()->like($aColumns[$i], '\'%' . $get['sSearch'] . '%\'');
                }
            }
            if(count($aLike) > 0)
                $cb->andWhere(new Expr\Orx($aLike));
            else unset($aLike);
        }
    
        $query = $cb->getQuery();
        $query->useResultCache(true, 60, 'work_related_get_filtered_count');
        $aResultTotal = $query->getResult();
    
        return $aResultTotal[0][1];
    }
    
    
}