<?php

namespace App;

add_filter('sage/template/frontpage/data', function($data) {
    include( get_theme_root() . '/autos/vendor/autodealer/_lib.php' );
    include( get_theme_root() . '/autos/vendor/autodealer/adc/api.php' );
    include( get_theme_root() . "/autos/vendor/autodealer/td/api.php" );

    $ADC = new \ADC();
    $TD = new \TD();
    $whitelist = ['acura', 'daihatsu', 'datsun', 'honda', 'isuzu', 'mazda', 'mitsubishi', 'subaru', 'suzuki', 'infiniti', 'nissan', 'lexus', 'scion', 'toyota', 'skoda', 'audi', 'volkswagen', 'bmw'];
    $east = ['acura', 'daihatsu', 'datsun', 'honda', 'infiniti', 'isuzu', 'lexus', 'mazda', 'mitsubishi', 'nissan', 'scion', 'subaru', 'suzuki', 'toyota'];
    $west = ['skoda', 'audi', 'bmw', 'volkswagen'];
    /// Раскомментировав строку нижу, можно посмотреть что вернул сервер
    // $ADC->e($oMarkList);

    $marksOriginal = array_filter($ADC->getMarkList(9)->marks, function($mark) use ($whitelist) {
        if ( !in_array(strtolower($mark->mark_name), $whitelist) ) return false;
        return 1000 != $mark->mark_id;
    });
    echo '<pre>'; var_dump($TD->getTDMarks('pc')); echo '</pre>';
    $marksAftermarket = array_filter($TD->getTDMarks('pc')->marks, function($mark) use ($whitelist) {
        if ( !in_array( explode(' ', strtolower($mark->mfa_brand))[0], $whitelist) ) return false;
        return true;
    });

    $marks = array();
    array_walk($marksOriginal, function ($value) use (&$marks) {
        if ( $value->external ) {
            $arr = explode('/',$value->route);
            $mark = end( $arr );
            $entry = "models";
            $iface = NULL;
            $var  = 'mark';   /// Передаваемая переменная
            if ( in_array($mark,['bmw','mini','moto','rr']) ){
                $iface  = 'bmw';    /// Директория со скриптами
                $action = 'series'; /// Входная точка
                $entry = $action;
            }
            if( in_array($mark,['nissan','infiniti']) ){
                $iface  = 'nissan';     /// Директория со скриптами
                $action = 'markets';    /// Входная точка
                $var    = 'mark';       /// Передаваемая переменная
            }
            if( in_array($mark,['nissan','infiniti']) ){
                $iface  = 'nissan';  /// Директория со скриптами
                $action = 'markets'; /// Входная точка
                $var = 'mark';
            }
            if( in_array($mark,['toyota','lexus']) ){
                $iface  = 'toyota';  /// Директория со скриптами
                $action = 'markets'; /// Входная точка
            }
            if( in_array($mark,['audi','volkswagen','seat','skoda']) ){
                $iface  = 'etka';    /// Директория со скриптами
                $action = 'markets'; /// Входная точка
            }
            if( in_array($mark,['pc','cv']) ){
                $iface  = 'td'; /// Директория со скриптами
                $action = 'marks';       /// Входная точка
                $var    = 'type';        /// Передаваемая переменная
            }
			$mcct=[];
			if(preg_match('/^(kia|hyundai)(_(c|s))?$/si',$mark,$mcct)) {
					$mark		= $mcct[1];
					$iface  = 'mcct';
					$action = 'index';
					$var    = 'type='.(!empty($mcct[2])?$mcct[3]:'').'&mark';
		    }
            if( in_array($mark,['fiat', 'lancia', 'abarth', 'alfa-romeo']) ){
                $iface = 'fiat';
                $action = 'models';
                $var = 'mark';
            }
            $entry =
            $url = "/$entry/?cat=$iface&{$var}={$mark}";
        }
        else $url = "/models/?cat=adc&type={$value->type_id}&mark={$value->mark_id}&flag={$value->flags}";

        $mark = new \StdClass;
        $mark->original = $value;
        $mark->original->url = $url;
        $mark->name = $value->mark_name;
        $mark->image = $value->mark_img_url;
        $key = preg_split("/\(|\s/", strtolower($value->mark_name))[0];

        $marks[$key] = $mark;
    });
    array_walk($marksAftermarket, function ($value) use (&$marks) {
        $key = preg_split("/\(|\s/", strtolower($value->mfa_brand))[0];
        if ( ! empty($key) && array_key_exists($key, $marks) ) {
            $marks[$key]->aftermarket[] = $value;
        } else {
            $mark = new \StdClass;
            $mark->name = $value->mfa_brand;
            $mark->image = $value->img_path;
            $mark->aftermarket[] = $value;
            $marks[$key] = $mark;
        }
    });
    usort($marks, function($a, $b) {
        $al = strtolower($a->name);
        $bl = strtolower($b->name);
        if ($al == $bl) return 0;
        return ($al > $bl) ? +1 : -1;
    });

    $items = new \StdClass();
    $items->east = array_filter($marks, function($mark) use ($east) {
        return in_array(strtolower($mark->name), $east);
    });
    $items->west = array_filter($marks, function($mark) use ($west) {
        return in_array(strtolower($mark->name), $west);
    });

    $data = [
        'marks' => $items
    ];

    return $data;
});
add_filter('sage/template/models/data', function($data) {
    // Общее
    $catalog = isset($_GET['cat']) ? $_GET['cat'] : '';
    $id = new \StdClass();
    $id->mark = isset($_GET['mark']) ? $_GET['mark'] : '';
    $id = new \StdClass();
    $car = new \StdClass();

    /** Обязательно к применению */
    include( get_theme_root() . '/autos/vendor/autodealer/_lib.php' );
    include( get_theme_root() . "/autos/vendor/autodealer/{$catalog}/api.php" );

    switch ($catalog) {
        case 'etka':
            $api = new \ETKA();
            $response = $api->getETKAMarkets($id->mark);
            $car->markets = $response->markets;
            $car->models = array();
            foreach ($car->markets as $value)
                $car->models[$value->code] = $api->getETKAModels($id->mark, $value->code)->models;
            break;
        case 'bmw':
            $id->type = isset($_GET['type']) ? $_GET['type'] : '';
            $id->series = isset($_GET['series']) ? $_GET['series'] : '';
            $api = new \BMW();
            $response = $api->getBMWModels($id->type, $id->series);
            $car->series = $api->_getSeries($response->aBreads->models->name);
            $car->mark = $api->_getMarkName($id->mark);
            $car->models = $response->aModels;
            break;
        case 'nissan':
            $api = new \NIS();

            // модели распределенные по регионам
            $car->markets = array();
            foreach ($api->getNisMarkets($id->mark) as $key => $value) {
                $car->markets[$key] = new \StdClass;
                $car->markets[$key]->name = $value;
                $car->markets[$key]->models = $api->getNisModels($id->mark, $key)->aModels;
            }
            // адрес для ссылок
            $car->url = "/modifications/?cat=nissan";
            break;
        case 'toyota':
            $api = new \TOY();

            // модели распределенные по регионам
            $car->markets = array();
            foreach ($api->getToyMarkets() as $key => $value) {
                $car->markets[$key] = new \StdClass;
                $car->markets[$key]->name = $value;
                $car->markets[$key]->models = $api->getToyModels($id->mark, $key)->aModels;
            }
            // адрес для ссылок
            $car->url = "/options/?cat=toyota&mark={$id->mark}";
            break;
        case 'td':
            $id->type = isset($_GET['type']) ? $_GET['type'] : '';
            $id->mark = isset($_GET['mark']) ? $_GET['mark'] : '';
            $api = new \TD();
            $response = $api->getTDModels('pc', $id->mark);
            $car->info = $response->modelInfo;
            $car->models = $response->models;
            break;
        case 'adc':
            $id->type = isset($_GET['type']) ? $_GET['type'] : '';
            $id->mark = isset($_GET['mark']) ? $_GET['mark'] : '';
            $api = new \ADC();
            $response = $api->getModelList($id->mark, $id->type);
            $car->models = $response->models;
            $car->url = "/tree/?cat=adc&mark{$id->mark}&type={$id->type}";
            break;
        default:
            echo 'Error';
            break;
    }

    $data = [
        'catalog' => $catalog,
        'oid' => $id,
        'id' => $id,
        'car' => $car
    ];

    return $data;
});
// Originals
add_filter('sage/template/series/data', function($data) {
    // Общее
    $catalog = isset($_GET['cat']) ? $_GET['cat'] : '';
    $id = new \StdClass();
    $id->mark = isset($_GET['mark']) ? $_GET['mark'] : '';
    // Выходные объекты
    $car = new \StdClass();

    /** Обязательно к применению */
    include( get_theme_root() . '/autos/vendor/autodealer/_lib.php' );
    include( get_theme_root() . "/autos/vendor/autodealer/{$catalog}/api.php" );

    switch ($catalog) {
        # Серии
        case 'bmw':
            $id->type = 'vt';
            $api = new \BMW();
            $response = $api->getBMWCatalogs($id->mark);
            $car->series = $response->vt;
            break;
        default:
            echo 'Error';
            break;
    }

    $data = [
        'catalog' => $catalog,
        'oid' => $id,
        'car' => $car
    ];

    return $data;
});
add_filter('sage/template/options/data', function($data) {
    $catalog = isset($_GET['cat']) ? $_GET['cat'] : '';
    $id = new \StdClass();
    $id->mark = isset($_GET['mark']) ? $_GET['mark'] : '';
    $id->model = isset($_GET['model']) ? $_GET['model'] : '';
    $car = new \StdClass();

    // Обязательно к применению
    include( get_theme_root() . '/autos/vendor/autodealer/_lib.php' );
    include( get_theme_root() . "/autos/vendor/autodealer/{$catalog}/api.php" );

    switch ($catalog) {
        case 'bmw':
            $id->type = isset($_GET['type']) ? $_GET['type'] : '';
            $id->series = isset($_GET['series']) ? $_GET['series'] : '';
            $id->body = isset($_GET['body']) ? $_GET['body'] : '';
            $id->market = isset($_GET['market']) ? $_GET['market'] : '';
            $api = new \BMW();
            $response = $api->getBMWOptions($id->type, $id->series, $id->body, $id->model, $id->market);
            $car->options = $response->aData;
            break;
        case 'toyota':
            $api = new \TOY();
            $id->market = isset($_GET['market']) ? $_GET['market'] : '';
            $response = $api->getToyModiff($id->market, $id->model);

            // опции автомобиля
            $car->options = $response->aModif;
            // адрес для ссылок
            $car->url = "/groups/?cat=toyota&mark={$id->mark}&market={$id->market}&model={$id->model}";
            // сокращения
            $shortening = [ 1 => "Двигатель", 2 => "Кузов", 3 => "Класс", 4 => "КПП", 5 => "Другое" ];
            $car->shortening = array();
            $i = 0;
            foreach ($response->info as $l) {
                $i++;
                if( $l->type==1 OR $l->type==2)      $k = 1;
                elseif( $l->type==3 )                $k = 2;
                elseif( $l->type==4 )                $k = 3;
                elseif( $l->type==5 OR $l->type==6 ) $k = 4;
                else                                 $k = 5;
                $car->shortening[$shortening[$k]][$i]['sign'] = $l->sign;
                $car->shortening[$shortening[$k]][$i]['description'] = $l->desc_en;
            }
            break;

        default:
            echo 'Error';
            break;
    }

    $data = [
        'catalog' => $catalog,
        'oid' => $id,
        'car' => $car
    ];

    return $data;
});
add_filter('sage/template/production/data', function($data) {
    $catalog = isset($_GET['cat']) ? $_GET['cat'] : '';
    $id = new \StdClass();
    $id->mark = isset($_GET['mark']) ? $_GET['mark'] : '';
    $id->model = isset($_GET['model']) ? $_GET['model'] : '';
    $car = new \StdClass();

    // Обязательно к применению
    include( get_theme_root() . '/autos/vendor/autodealer/_lib.php' );
    include( get_theme_root() . "/autos/vendor/autodealer/{$catalog}/api.php" );

    switch ($catalog) {
        case 'fiat':
            $api = new \Fiat();
            $response = $api->getFIATProduction($id->mark, $id->model);
            $car->productions = $response->prod;
            break;
        case 'etka':
            $id->market = isset($_GET['market']) ? $_GET['market'] : '';
            $id->dir = isset($_GET['dir']) ? $_GET['dir'] : 'R';
            $api = new \ETKA();
            $response = $api->getETKAProduction($id->mark, $id->market, $id->model);
            $car->productions = $response->prod;
            $car->url = "/groups/?cat=etka&mark={$id->mark}&market={$id->market}&model={$id->model}";
            break;
        case 'bmw':
            $id->type = isset($_GET['type']) ? $_GET['type'] : '';
            $id->series = isset($_GET['series']) ? $_GET['series'] : '';
            $id->body = isset($_GET['body']) ? $_GET['body'] : '';
            $id->market = isset($_GET['market']) ? $_GET['market'] : '';
            $id->rule = isset($_GET['rule']) ? $_GET['rule'] : '';
            $id->transmission = isset($_GET['transmission']) ? $_GET['transmission'] : '';
            $api = new \BMW();
            $response = $api->getBMWProduction($id->type, $id->series, $id->body, $id->model, $id->market, $id->rule, $id->transmission);
            $aData = $response->aData;
            $aData = current($aData);
            $aData = [
                "DateStart"  => $aData->DateStart,
                "DateEnd"    => $aData->DateEnd,
                "startYear"  => substr($aData->DateStart,0,4),
                "startMonth" => substr($aData->DateStart,4,2),
                "startDay"   => substr($aData->DateStart,6,2),
                "endYear"    => substr($aData->DateEnd,0,4),
                "endMonth"   => substr($aData->DateEnd,4,2),
                "endDay"     => substr($aData->DateEnd,6,2),
            ];
            $car->production = $aData;
            $car->url = "/groups/?cat={$catalog}&mark={$id->mark}&type={$id->type}&series={$id->series}&body={$id->body}&model={$id->model}&market={$id->market}&rule={$id->rule}&transmission={$id->transmission}";
            break;
        case 'nissan':
            $api = new \NIS();
            // $markets = $api->getNisMarkets($mark);
            $markets = array( 'jp' => '', 'gl' => '', 'gr' => '' );
            $models = array();
            foreach ($markets as $key => $value)
                $models = array_merge($models, $api->getNisModels($mark, $key)->aModels);
            break;

        case 'toyota':
            $api = new \TOY();
            $markets = $api->getToyMarkets();
            $models = array();
            foreach ($markets as $key => $value)
                $models = array_merge($models, $api->getToyModels($mark, $key)->aModels);
            break;

        default:
            echo 'Error';
            break;
    }

    $data = [
        'catalog' => $catalog,
        'oid' => $id,
        'car' => $car
    ];

    return $data;
});
add_filter('sage/template/modifications/data', function($data) {
    // Общее
    $catalog = isset($_GET['cat']) ? $_GET['cat'] : '';

    // Оригиналы
    $id = new \StdClass();
    $id->mark = isset($_GET['mark']) ? $_GET['mark'] : '';
    $id->model = isset($_GET['model']) ? $_GET['model'] : '';
    // Выходные объекты
    $car = new \StdClass();

    // Обязательно к применению
    include( get_theme_root() . '/autos/vendor/autodealer/_lib.php' );
    include( get_theme_root() . "/autos/vendor/autodealer/{$catalog}/api.php" );

    switch ($catalog) {
        case 'fiat':
            $api = new \Fiat();
            $response = $api->getFIATProduction($id->mark, $id->model);
            $car->productions = $response->prod;
            break;
        # Региональность - Audi, Volkswagen, Seat, Skoda
        case 'etka':
            $id->market = isset($_GET['market']) ? $_GET['market'] : '';
            $id->dir = isset($_GET['dir']) ? $_GET['dir'] : 'R';
            $api = new \ETKA();
            $response = $api->getETKAProduction($id->mark, $id->market, $id->model);
            $car->productions = $response->prod;
            break;
        # Региональность - Nissan.Infiniti
        case 'nissan':
            $api = new \NIS();
            $id->market = isset($_GET['market']) ? $_GET['market'] : '';
            $response = $api->getNisModiff($id->market, $id->model);
            $car->modifications = $response->aModif;
            $car->url = "/groups/?cat=nissan&market={$id->market}&model={$id->model}";
            break;
        # Региональность - Toyota, Lexus
        case 'toyota':
            $api = new \TOY();
            $markets = $api->getToyMarkets();
            $models = array();
            foreach ($markets as $key => $value)
                $models = array_merge($models, $api->getToyModels($mark, $key)->aModels);
            break;
        default:
            echo 'Error';
            break;
    }

    $data = [
        'catalog' => $catalog,
        'oid' => $id,
        'car' => $car
    ];

    return $data;
});
add_filter('sage/template/groups/data', function($data) {
    $catalog = isset($_GET['cat']) ? $_GET['cat'] : '';
    $id = new \StdClass();
    $id->mark = isset($_GET['mark']) ? $_GET['mark'] : '';
    $id->model = isset($_GET['model']) ? $_GET['model'] : '';
    $id->market = isset($_GET['market']) ? $_GET['market'] : '';
    $id->production = isset($_GET['production']) ? $_GET['production'] : '';
    $car = new \StdClass();

    // Обязательно к применению
    include( get_theme_root() . '/autos/vendor/autodealer/_lib.php' );
    include( get_theme_root() . "/autos/vendor/autodealer/{$catalog}/api.php" );

    switch ($catalog) {
        case 'fiat':
            $api = new \Fiat();
            $response = $api->getFIATGroup($id->mark, $id->model, $id->production);
            $car->groups = $response->groups;
            break;
        case 'etka':
            $id->production_year = isset($_GET['production_year']) ? $_GET['production_year'] : '';
            $id->code = isset($_GET['code']) ? $_GET['code'] : '';
            $id->dir = isset($_GET['dir']) ? $_GET['dir'] : '';
            $api = new \ETKA();
            $response = $api->getETKAGroups($id->mark, $id->market, $id->model, $id->production_year, $id->code, $id->dir);
            $car->groups = $response->hg;
            $car->image = get_stylesheet_directory_uri() . '/vendor/autodealer/media/images/etka/groups/';
            break;
        case 'bmw':
            $id->type = isset($_GET['type']) ? $_GET['type'] : '';
            $id->series = isset($_GET['series']) ? $_GET['series'] : '';
            $id->body = isset($_GET['body']) ? $_GET['body'] : '';
            $id->market = isset($_GET['market']) ? $_GET['market'] : '';
            $id->rule = isset($_GET['rule']) ? $_GET['rule'] : '';
            $id->transmission = isset($_GET['transmission']) ? $_GET['transmission'] : '';
            $id->production = isset($_GET['production']) ? $_GET['production'] : '';

            $api = new \BMW();
            $response = $api->getBMWGroups($id->type, $id->series, $id->body, $id->model, $id->market, $id->rule, $id->transmission, $id->production, 'ru');
            $car->url = "/subgroups/?cat={$catalog}&mark={$id->mark}&type={$id->type}&series={$id->series}&body={$id->body}&model={$id->model}&market={$id->market}&rule={$id->rule}&transmission={$id->transmission}&production={$id->production}";
            $car->series = $api->_getSeries($response->aBreads->models->name);
            $car->mark = $api->_getMarkName($id->mark);
            $car->modification = $response->aBreads->groups->name;
            $car->groups = $response->aData;
            $car->info = $response->modelInfo;
            break;
        case 'nissan':
            $api = new \NIS();
            $id->modification = isset($_GET['modification']) ? $_GET['modification'] : '';
            $id->mark = (strpos(strtolower($id->market), 'inf') > 1 ) ? 'infiniti' : 'nissan';
            $response = $api->getNisModInfo($id->market, $id->model, $id->modification);
            $car->image = $response->Img;
            $car->groups = $response->aModInfo;
            $car->url = "/subgroups/?cat=nissan&market={$id->market}&model={$id->model}&modification={$id->modification}";
            break;
        case 'toyota':
            $api = new \TOY();
            $id->compl = isset($_GET['compl']) ? $_GET['compl'] : '';
            $id->option = isset($_GET['option']) ? $_GET['option'] : '';
            $id->code = isset($_GET['code']) ? $_GET['code'] : '';
            // дополнительные данные
            $id->vin = isset($_GET['vin']) ? $_GET['vin'] : '';
            $id->vdate = isset($_GET['vdate']) ? $_GET['vdate'] : '';
            $id->siyopt = isset($_GET['siyopt']) ? $_GET['siyopt'] : '';
            $response = $api->getToyModCompl($id->market, $id->model, $id->compl, $id->option, $id->code, $id->vin, $id->vdate, $id->siyopt);

            // группы
            $car->groups = $response->aCompl;
            // адрес для ссылок
            $car->url = "/illustration/?cat=toyota&mark={$id->mark}&market={$id->market}&model={$id->model}&compl={$id->compl}&option={$id->option}&code={$id->code}";
            // дополнительный url
            $car->getString = ""
                . ( ( $id->vin )   ? "&vin={$id->vin}" : "" )
                . ( ( $id->vdate ) ? "&vdate={$id->vdate}" : "" )
                . ( ( $id->siyopt )? "&siyopt={$id->siyopt}" : "" );
            break;

        default:
            echo 'Error';
            break;
    }

    $data = [
        'catalog' => $catalog,
        'oid' => $id,
        'car' => $car
    ];

    return $data;
});
add_filter('sage/template/subgroups/data', function($data) {
    $catalog = isset($_GET['cat']) ? $_GET['cat'] : '';
    $id = new \StdClass();
    $car = new \StdClass();

    // Обязательно к применению
    include( get_theme_root() . '/autos/vendor/autodealer/_lib.php' );
    include( get_theme_root() . "/autos/vendor/autodealer/{$catalog}/api.php" );

    switch ($catalog) {
        case 'fiat':
            $api = new \Fiat();
            $id->mark = isset($_GET['mark']) ? $_GET['mark'] : '';
            $id->model = isset($_GET['model']) ? $_GET['model'] : '';
            $id->production = isset($_GET['production']) ? $_GET['production'] : '';
            $id->group = isset($_GET['group']) ? $_GET['group'] : '';

            $response = $api->getFIATSubGroup($id->mark, $id->model, $id->production, $id->group);
            $car->subgroups = $response->subGroups;
            break;
        case 'etka':
            $api = new \ETKA();
            $id->mark = isset($_GET['mark']) ? $_GET['mark'] : '';
            $id->model = isset($_GET['model']) ? $_GET['model'] : '';
            $id->market = isset($_GET['market']) ? $_GET['market'] : '';
            $id->production_year = isset($_GET['production_year']) ? $_GET['production_year'] : '';
            $id->group = isset($_GET['group']) ? $_GET['group'] : '';
            $id->dir = isset($_GET['dir']) ? $_GET['dir'] : 'R';
            $id->code = isset($_GET['code']) ? $_GET['code'] : '';

            $response = $api->getETKASubGroups($id->mark, $id->market, $id->model, $id->production_year, $id->code, $id->dir, $id->group);
            $car->subgroups = array_filter($response->ug, function($value) {
                return $value->ou != 'O';
            });
            break;
        case 'bmw':
            $api = new \BMW();
            $id->mark = isset($_GET['mark']) ? $_GET['mark'] : '';
            $id->type = isset($_GET['type']) ? $_GET['type'] : '';
            $id->series = isset($_GET['series']) ? $_GET['series'] : '';
            $id->body = isset($_GET['body']) ? $_GET['body'] : '';
            $id->model = isset($_GET['model']) ? $_GET['model'] : '';
            $id->market = isset($_GET['market']) ? $_GET['market'] : '';
            $id->rule = isset($_GET['rule']) ? $_GET['rule'] : '';
            $id->transmission = isset($_GET['transmission']) ? $_GET['transmission'] : '';
            $id->production = isset($_GET['production']) ? $_GET['production'] : '';
            $id->group = isset($_GET['group']) ? $_GET['group'] : '';

            $response = $api->getBMWSubGroups($id->type, $id->series, $id->body, $id->model, $id->market, $id->rule, $id->transmission, $id->production, $id->group, 'ru');
            $car->subgroups = $response->aData;
            $car->info = $response->modelInfo;
            $car->url = "/illustration/?cat=bmw&mark={$id->mark}&type={$id->type}&series={$id->series}&body={$id->body}&model={$id->model}&market={$id->market}&rule={$id->rule}&transmission={$id->transmission}&production={$id->production}&group={$id->group}";
            break;
        case 'nissan':
            $api = new \NIS();
            $id->model = isset($_GET['model']) ? $_GET['model'] : '';
            $id->market = isset($_GET['market']) ? $_GET['market'] : '';
            $id->mark = (strpos(strtolower($id->market), 'inf') > 1 ) ? 'infiniti' : 'nissan';
            $id->group = isset($_GET['group']) ? $_GET['group'] : '';
            $id->modification = isset($_GET['modification']) ? $_GET['modification'] : '';

            $response = $api->getNisGroup($id->market, $id->model, $id->modification, $id->group);
            $car->image = $response->Img;
            $car->subgroups = $response->aGroup;
            $car->url = "/illustration/?cat=nissan&market={$id->market}&model={$id->model}&modification={$id->modification}&group={$id->group}";
            break;
        case 'toyota':
            // $api = new \TOY();
            // $markets = $api->getToyMarkets();
            // $models = array();
            // foreach ($markets as $key => $value)
            //     $models = array_merge($models, $api->getToyModels($mark, $key)->aModels);
            break;
        default:
            echo 'Error';
            break;
    }

    $data = [
        'catalog' => $catalog,
        'oid' => $id,
        'car' => $car
    ];

    return $data;
});
// Aftermarket + ADC
add_filter('sage/template/equipments/data', function($data) {
    $catalog = isset($_GET['cat']) ? $_GET['cat'] : '';

    /** Обязательно к применению */
    include( get_theme_root() . '/autos/vendor/autodealer/_lib.php' );
    include( get_theme_root() . "/autos/vendor/autodealer/{$catalog}/api.php" );

    $api    = new \TD();
    $id     = new \StdClass();
    $car    = new \StdClass();
    $id->mark   = $api->rcv('mark');
    $id->model  = $api->rcv('model');

    $response = $api->getTDCompl('pc', $id->mark, $id->model);
    $car->equipments = $response->compl;
    $car->info = $response->modelInfo;

    $data = [
        'catalog' => $catalog,
        'id' => $id,
        'car' => $car
    ];

    return $data;
});
add_filter('sage/template/tree/data', function($data) {
    $id = new \StdClass();
    $car = new \StdClass();
    $catalog = isset($_GET['cat']) ? $_GET['cat'] : '';

    /** Обязательно к применению */
    include( get_theme_root() . '/autos/vendor/autodealer/_lib.php' );
    include( get_theme_root() . "/autos/vendor/autodealer/{$catalog}/api.php" );

    switch ($catalog) {
        case 'td':
            $api = new \TD();
            $id->mark = isset($_GET['mark']) ? $_GET['mark'] : '';
            $id->model = isset($_GET['model']) ? $_GET['model'] : '';
            $id->equipment = isset($_GET['equipment']) ? $_GET['equipment'] : '';

            $response = $api->getTDTree('pc', $id->mark, $id->model, $id->equipment);

            usort($response->tree, function($a, $b) {
                if ( $a->str_level == $b->str_level ) {
                    return 0;
                }
                return $a->str_level > $b->str_level ? -1 : 1;
            });
            $min = end($response->tree)->str_level;

            $response->tree = array_combine(array_column($response->tree, 'str_id'), $response->tree);
            foreach ($response->tree as $value) {
                if ($value->str_level == $min ) {
                    $value->ready = true;
                }
                $current = $value;
                $response->tree[$current->str_id_parent]->childrens[] = $current;
            };
            $car->tree = array_filter($response->tree, function($item) { return isset($item->ready); });
            break;
        case 'adc':
            $id->model = isset($_GET['model']) ? $_GET['model'] : '';
            $api = new \ADC();
            $response = $api->getTreeList($id->model, false);
            usort($response->details, function($a, $b) {
                if ( $a->parent_id == $b->parent_id ) {
                    return 0;
                }
                return $a->parent_id > $b->parent_id ? -1 : 1;
            });
            $response->details = array_combine(array_column($response->details, 'id'), $response->details);
            foreach ($response->details as $value) {
                if ($value->parent_id == 0 ) {
                    $value->ready = true;
                }
                $current = $value;
                $response->details[$current->parent_id]->childrens[] = $current;
            };
            $car->details = array_filter($response->details, function($item) { return isset($item->ready); });
            $car->url = "/illustration/?cat=adc&model={$id->model}";

            break;
        default:
            break;
    }



    $data = [
        'id' => $id,
        'car' => $car,
        'catalog' => $catalog
    ];

    return $data;
});
add_filter('sage/template/details/data', function($data) {
    $catalog = isset($_GET['cat']) ? $_GET['cat'] : '';

    /** Обязательно к применению */
    include( get_theme_root() . '/autos/vendor/autodealer/_lib.php' );
    include( get_theme_root() . "/autos/vendor/autodealer/{$catalog}/api.php" );

    $api    = new \TD();
    $id     = new \StdClass();
    $car    = new \StdClass();

    $id->mark       = isset($_GET['mark'])      ? $_GET['mark'] : '';
    $id->model      = isset($_GET['model'])     ? $_GET['model'] : '';
    $id->equipment  = isset($_GET['equipment']) ? $_GET['equipment'] : '';
    $id->tree       = isset($_GET['tree'])      ? $_GET['tree'] : '';

    $response = $api->getTDDetails('pc', $id->mark, $id->model, $id->equipment, $id->tree);
    $car->info      = $response->modelInfo;
    $car->group     = $response->group;
    $car->details   = $response->details;

    $data = [
        'catalog'   => $catalog,
        'id'        => $id,
        'car'       => $car
    ];

    return $data;
});

add_filter('sage/template/illustration/data', function($data) {
    $id = new \StdClass();
    $catalog = isset($_GET['cat']) ? $_GET['cat'] : '';
    $id->mark = isset($_GET['mark']) ? $_GET['mark'] : '';
    $id->model = isset($_GET['model']) ? $_GET['model'] : '';
    $id->market = isset($_GET['market']) ? $_GET['market'] : '';
    $id->production = isset($_GET['production']) ? $_GET['production'] : '';
    $id->group = isset($_GET['group']) ? $_GET['group'] : '';
    $id->subgroup = isset($_GET['subgroup']) ? $_GET['subgroup'] : '';
    $car = new \StdClass();

    // Обязательно к применению
    include( get_theme_root() . '/autos/vendor/autodealer/_lib.php' );
    include( get_theme_root() . "/autos/vendor/autodealer/{$catalog}/api.php" );

    switch ($catalog) {
        case 'fiat':
            $api = new \Fiat();
            $id->table = isset($_GET['table']) ? urldecode(base64_decode($_GET['table'])) : '';
            $response = $api->getFIATPartDrawData($id->production, $id->group, $id->subgroup, $id->table);
            $variant = $response->partDrawData->variants[0]->variante;
            $car->parts = $response;
            $response = $api->getFIATDraw($id->mark, $id->model, $id->production, $id->group, $id->subgroup, $id->table, $variant, 0.5);
            $car->illustration = $response;
            break;
        case 'etka':
            $api = new \ETKA();
            $id->production_year = isset($_GET['production_year']) ? $_GET['production_year'] : '';
            $id->dir = isset($_GET['dir']) ? $_GET['dir'] : 'R';
            $id->code = isset($_GET['code']) ? $_GET['code'] : '';
            $id->graphic = isset($_GET['graphic']) ? $_GET['graphic'] : '';
            $response = $api->getETKAIllustration($id->mark, $id->market, $id->model, $id->production_year, $id->code, $id->dir, $id->group, $id->subgroup, $id->graphic, 0.5);
            $car->illustration = $response;
            $car->url = "/illustration/?cat={ $catalog }&mark={ $id->mark }&market={ $id->market }&model={ $id->model }&production_year={ $id->production_year }&code={ $id->code }";
            break;
        case 'bmw':
            $api = new \BMW();
            $id->mark   = $api->rcv('mark');
            $id->type   = $api->rcv('type');
            $id->series = $api->rcv('series');
            $id->body   = $api->rcv('body');
            $id->model  = $api->rcv('model');
            $id->market = $api->rcv('market');
            $id->rule   = $api->rcv('rule');
            $id->trans  = $api->rcv('transmission');
            $id->prod   = $api->rcv('production');
            $id->group  = $api->rcv('group');
            $id->graphic= $api->rcv('graphic');

            $response = $api->getBMWDetailsMap($id->type,$id->series,$id->body,$id->model,$id->market,$id->rule,$id->trans,$id->prod,$id->group,$id->graphic, "ru");
            $car->aLabels   = $response->labels;
            $car->aDetails  = $response->details;
            $car->aComments = $response->comments;
            $car->imgInfo = $response->imgInfo;
            $car->url = "/illustration/?cat=bmw&mark={$id->mark}&type={$id->type}&series={$id->series}&body={$id->body}&model={$id->model}&market={$id->market}&rule={$id->rule}&transmission={$id->trans}&production={$id->prod}&group={$id->group}";
            break;
        case 'nissan':
            $api = new \NIS();
            $id->modification = isset($_GET['modification']) ? $_GET['modification'] : '';
            $id->figure = $api->rcv('figure');
            $id->subimage = '';
            $id->sec = '';
            $response = $api->getNISPic($id->market, $id->model, $id->modification, $id->group, $id->figure, $id->subimage, $id->sec);

            $car->illustration = $response;
            $car->nextUrl = "/detail/?cat=nissan&market={$id->market}&model={$id->model}&modif={$id->modification}&group={$id->group}&figure={$id->figure}&subfig=";
            $car->nextSecUrl = "/illustration/?cat=nissan&market={$id->market}&model={$id->model}&modif={$id->modification}&group={$id->group}&figure={$id->figure}&subfig=";
            $car->secUrl = "/illustration/?cat=nissan&market={$id->market}&model={$id->model}&modif={$id->modification}&group={$id->group}&figure=";

            break;
        case 'toyota':
            $api = new \TOY();
            $id->compl = isset($_GET['compl']) ? $_GET['compl'] : '';
            $id->option = isset($_GET['option']) ? $_GET['option'] : '';
            $id->code = isset($_GET['code']) ? $_GET['code'] : '';
            $id->group = isset($_GET['group']) ? $_GET['group'] : '';
            $id->graphic = isset($_GET['graphic']) ? $_GET['graphic'] : '';
            // дополнительные данные
            $id->vin = isset($_GET['vin']) ? $_GET['vin'] : '';
            $id->vdate = isset($_GET['vdate']) ? $_GET['vdate'] : '';
            $id->siyopt = isset($_GET['siyopt']) ? $_GET['siyopt'] : '';

            $car->getString = ""
                . ( ( $id->vin )    ? "&vin=$id->vin"      : "" )
                . ( ( $id->vdate )  ? "&vdate=$id->vdate"  : "" )
                . ( ( $id->siyopt ) ? "&siyopt=$id->siyopt" : "" );
            break;
        case 'adc':
            $id->model = isset($_GET['model']) ? $_GET['model'] : '';
            $id->tree = isset($_GET['tree']) ? $_GET['tree'] : '';
            $id->jump = isset($_GET['jump']) ? $_GET['jump'] : '';
            $api = new \ADC();
            $response = $api->getDetails($id->model, $id->tree, $id->jump);
            $car->illustration = $response->mapImg;
            $car->details = $response->details;
            $car->next = $response->nav->next;
            $car->prev = $response->nav->prev;

            break;
        default:
            echo 'Error';
            break;
    }

    $data = [
        'catalog' => $catalog,
        'oid' => $id,
        'car' => $car
    ];

    return $data;
});
