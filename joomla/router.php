<?php


// no direct access
defined('_JEXEC') or die('Restricted access');

function JeaBuildRoute(&$query) {
//       var_dump($query);die;
    $segments = array();

    if (isset($query['view'])) {
        unset($query['view']);
    }

    if (isset($query['layout'])) {
        $segments[] = $query['layout'];
        unset($query['layout']);
    }

    if (isset($query['id'])) {
        $query['id'] = str_replace(':', '/', $query['id']);
        $segments[] = $query['id'];
        unset($query['id']);

        // var_dump($segments);
    }

    if (isset($query['task']) && $query['task'] == 'properties.search') {
        $segments[] = 'procura';
        unset($query['task']);

        if (isset($query['filter_transaction_type'])) {
            if ($query['filter_transaction_type'] == 'SELLING') {
                $segments[] = 'venda';
            }
            if ($query['filter_transaction_type'] == 'RENTING') {
                $segments[] = 'locacao';
            }
            unset($query['filter_transaction_type']);
        }
        if (isset($query['filter_tipos']) && $query['filter_tipos'] != '') {


            $db = JFactory::getDbo();
            $dbQuery = $db->getQuery(true)
                    ->select('value')
                    ->from('#__jea_tipos')
                    ->where('id=' . (int) $query['filter_tipos']);
            $db->setQuery($dbQuery);
            $alias = $db->loadResult();
            $alias = str_replace(' ', '_', $alias);
            $segments[] = $alias;

            unset($query['filter_tipos']);
        } else {
            $segments[] = 'todos_tipos';
            unset($query['filter_tipos']);
        }

        if (isset($query['filter_area_id']) && $query['filter_area_id'] != '0') {

            $db = JFactory::getDbo();
            $db->setQuery("SET NAMES 'utf8'");
            $dbQuery = $db->getQuery(true)
                    ->select('value')
                    ->from('#__jea_areas')
                    ->where('id=' . (int) $query['filter_area_id']);
            $db->setQuery($dbQuery);
            $alias = $db->loadResult();
            var_dump($alias);  echo 'teste';         
            
       $alias= stripAccents($alias);
            $alias = str_replace(' ', '_', $alias);

            $segments[] = 'florianopolis/' . $alias;
            unset($query['filter_area_id']);
        } else {
            $segments[] = 'florianopolis/' . 'todos_bairros';
            unset($query['filter_area_id']);
        }
        
        


        if (isset($query['filter_search'])) {
            $segments[] = $query['filter_search'];
            unset($query['filter_search']);
        }
        
        if (isset($query['filter_bedrooms_min'])) {
            //   $segments[] = $query['filter_bedrooms_min'];
            unset($query['filter_bedrooms_min']);
        }
        
    }



    return $segments;
}

function JeaParseRoute($segments) {
    $vars = array();

    //Get the active menu item
    $app = JFactory::getApplication();
    $menu = $app->getMenu();
    $item = $menu->getActive();



//hack for SEF propouses
    if ($segments[0] != 'procura') {
        unset($segments[1]);
    }
//     var_dump($segments);die;
    //   var_dump($segments);die;
    // Count route segments
    $count = count($segments);

//Standard routing for property
    if (!isset($item)) {
        $vars['view'] = 'property';
        $vars['id'] = $segments[$count - 1];
        return $vars;
    }

    if ($count == 1 && is_numeric($segments[0])) {
        // If there is only one numeric segment, then it points to a property detail
        if (strpos($segments[0], ':') === false) {
            $id = (int) $segments[0];
        } else {
            $exp = explode(':', $segments[0], 2);
            $id = (int) $exp[0];
        }

        $vars['view'] = 'property';
        $vars['id'] = $id;
    }
    //   var_dump($item);die;
    if ($item->query['view'] == 'properties') {

        $layout = isset($item->query['layout']) ? $item->query['layout'] : 'default';
        //     var_dump($segments);die;
        switch ($layout) {
            case 'default' :
                if ($segments[0] == 'search') {

                    //                   var_dump($segments);die;
                    $vars['view'] = 'properties';
                    $vars['layout'] = 'procurasef';
                }

                if ($segments[0] == 'procura') {





                    $vars['view'] = 'properties';
                    //   $vars['layout'] = 'procurasef';
                    //                 $vars['filter_search'] = 'xxx';
                    //                $vars['filter_area_id']=2;
                    //                $vars['filter_transaction_type']='RENTING';

                    switch ($segments[1]) {
                        case 'venda':
                            $vars['filter_transaction_type'] = 'SELLING';
                            break;
                        case 'locacao' :
                            $vars['filter_transaction_type'] = 'RENTING';
                            break;
                    }
                    if ($segments[2] != 'todos_tipos') {


                        $db = JFactory::getDbo();
                        $dbQuery = $db->getQuery(true)
                                ->select('id')
                                ->from('#__jea_tipos')
                                ->where('value="' . $segments[2] . '"');
                        $db->setQuery($dbQuery);
                        $tipo = $db->loadResult();
                        //$alias = str_replace(' ', '_', $alias);
                        $vars['filter_tipos'] = $tipo;
       
                    } else {
                        $vars['filter_tipos'] = '0';
                    }
                    if ($segments[4] != 'todos_bairros') {
                        $id_bairro= str_replace('_', ' ', $segments[4]);
                        
                        $db = JFactory::getDbo();
                        $dbQuery = $db->getQuery(true)
                                ->select('id')
                                ->from('#__jea_areas')
                                ->where('value="' .$id_bairro . '"');
                        $db->setQuery($dbQuery);
                        $area_id = $db->loadResult();
                    
                        $vars['filter_area_id'] = $area_id;
                    } else {
                        //$vars['filter_area_id'] = '0';
                    }
                     //                               var_dump($segments);die;
					 //var_dump($query);die;
                    //                  die;
                }
                break;

            case 'search':

            case 'searchmap':

                $vars['view'] = 'properties';
                $vars['layout'] = $layout;

                if ($count == 1) {
                    // If there is only one, then it points to a property detail
                    if (is_numeric($segments[0])) {
                        $vars['view'] = 'property';
                        $vars['id'] = (int) $segments[0];
                    } elseif (strpos($segments[0], ':') !== false) {

                        $exp = explode(':', $segments[0], 2);
                        $vars['id'] = (int) $exp[0];
                        $vars['view'] = 'property';
                    }
                }
                break;

            case 'manage' :
                $vars['view'] = 'properties';
                $vars['layout'] = 'manage';

                if ($count > 0 && $segments[0] == 'edit') {
                    $vars['view'] = 'form';
                    $vars['layout'] = 'edit';
                    if ($count == 2) {
                        $vars['id'] = (int) $segments[1];
                    }
                }
                break;
        }
    } elseif ($item->query['view'] == 'form') {
        $vars['view'] = 'form';
        $vars['layout'] = 'edit';

        if ($count > 0) {
            if ($segments[0] == 'edit' && $count == 2) {
                $vars['id'] = (int) $segments[1];
            } elseif ($segments[0] == 'manage') {
                $vars['view'] = 'properties';
                $vars['layout'] = 'manage';
            }
        }
    }

    return $vars;
}

function stripAccents($string){ 

    $from = "áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ";
    $to = "aaaaeeiooouucAAAAEEIOOOUUC";
    $keys = array();
    $values = array();
    preg_match_all('/./u', $from, $keys);
    preg_match_all('/./u', $to, $values);
    $mapping = array_combine($keys[0], $values[0]);
    $string= strtr($string, $mapping);

 
    
    
    return $string;
}
