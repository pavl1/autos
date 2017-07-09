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
        return (1 == $mark->flags) && (1000 != $mark->mark_id);
    });
    $marksAftermarket = array_filter($TD->getTDMarks('pc')->marks, function($mark) use ($whitelist) {
        if ( !in_array(strtolower($mark->mfa_brand), $whitelist) ) return false;
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
    $oid = new \StdClass();
    $oid->mark = isset($_GET['mark']) ? $_GET['mark'] : '';
    $id = new \StdClass();
    $car = new \StdClass();

    /** Обязательно к применению */
    include( get_theme_root() . '/autos/vendor/autodealer/_lib.php' );
    include( get_theme_root() . "/autos/vendor/autodealer/{$catalog}/api.php" );

    switch ($catalog) {
        case 'etka':
            $api = new \ETKA();
            $response = $api->getETKAMarkets($oid->mark);
            $car->markets = $response->markets;
            $car->models = array();
            foreach ($car->markets as $value)
                $car->models[$value->code] = $api->getETKAModels($oid->mark, $value->code)->models;
            break;

        case 'bmw':
            $oid->type = isset($_GET['type']) ? $_GET['type'] : '';
            $oid->series = isset($_GET['series']) ? $_GET['series'] : '';
            $api = new \BMW();
            $response = $api->getBMWModels($oid->type, $oid->series);
            $car->series = $api->_getSeries($response->aBreads->models->name);
            $car->mark = $api->_getMarkName($oid->mark);
            $car->models = $response->aModels;
            break;

        case 'nissan':
            $api = new \NIS();

            // модели распределенные по регионам
            $car->markets = array();
            foreach ($api->getNisMarkets($oid->mark) as $key => $value) {
                $car->markets[$key] = new \StdClass;
                $car->markets[$key]->name = $value;
                $car->markets[$key]->models = $api->getNisModels($oid->mark, $key)->aModels;
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
                $car->markets[$key]->models = $api->getToyModels($oid->mark, $key)->aModels;
            }
            // адрес для ссылок
            $car->url = "/options/?cat=toyota&mark={$oid->mark}";
            break;

        case 'td':
            $id->type = isset($_GET['type']) ? $_GET['type'] : '';
            $id->mark = isset($_GET['mark']) ? $_GET['mark'] : '';
            $api = new \TD();
            $response = $api->getTDModels('pc', $id->mark);
            $car->info = $response->modelInfo;
            $car->models = $response->models;
            break;
        default:
            echo 'Error';
            break;
    }

    $data = [
        'catalog' => $catalog,
        'oid' => $oid,
        'id' => $id,
        'car' => $car
    ];

    return $data;
});
// Оригиналы
add_filter('sage/template/series/data', function($data) {
    // Общее
    $catalog = isset($_GET['cat']) ? $_GET['cat'] : '';
    $oid = new \StdClass();
    $oid->mark = isset($_GET['mark']) ? $_GET['mark'] : '';
    // Выходные объекты
    $car = new \StdClass();

    /** Обязательно к применению */
    include( get_theme_root() . '/autos/vendor/autodealer/_lib.php' );
    include( get_theme_root() . "/autos/vendor/autodealer/{$catalog}/api.php" );

    switch ($catalog) {
        # Серии
        case 'bmw':
            $oid->type = 'vt';
            $api = new \BMW();
            $response = $api->getBMWCatalogs($oid->mark);
            $car->series = $response->vt;
            break;
        default:
            echo 'Error';
            break;
    }

    $data = [
        'catalog' => $catalog,
        'oid' => $oid,
        'car' => $car
    ];

    return $data;
});
add_filter('sage/template/options/data', function($data) {
    $catalog = isset($_GET['cat']) ? $_GET['cat'] : '';
    $oid = new \StdClass();
    $oid->mark = isset($_GET['mark']) ? $_GET['mark'] : '';
    $oid->model = isset($_GET['model']) ? $_GET['model'] : '';
    $car = new \StdClass();

    // Обязательно к применению
    include( get_theme_root() . '/autos/vendor/autodealer/_lib.php' );
    include( get_theme_root() . "/autos/vendor/autodealer/{$catalog}/api.php" );

    switch ($catalog) {
        case 'bmw':
            $oid->type = isset($_GET['type']) ? $_GET['type'] : '';
            $oid->series = isset($_GET['series']) ? $_GET['series'] : '';
            $oid->body = isset($_GET['body']) ? $_GET['body'] : '';
            $oid->market = isset($_GET['market']) ? $_GET['market'] : '';
            $api = new \BMW();
            $response = $api->getBMWOptions($oid->type, $oid->series, $oid->body, $oid->model, $oid->market);
            $car->options = $response->aData;
            break;
        case 'toyota':
            $api = new \TOY();
            $oid->market = isset($_GET['market']) ? $_GET['market'] : '';
            $response = $api->getToyModiff($oid->market, $oid->model);

            // опции автомобиля
            $car->options = $response->aModif;
            // адрес для ссылок
            $car->url = "/groups/?cat=toyota&mark={$oid->mark}&market={$oid->market}&model={$oid->model}";
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
        'oid' => $oid,
        'car' => $car
    ];

    return $data;
});
add_filter('sage/template/production/data', function($data) {
    $catalog = isset($_GET['cat']) ? $_GET['cat'] : '';
    $oid = new \StdClass();
    $oid->mark = isset($_GET['mark']) ? $_GET['mark'] : '';
    $oid->model = isset($_GET['model']) ? $_GET['model'] : '';
    $car = new \StdClass();

    // Обязательно к применению
    include( get_theme_root() . '/autos/vendor/autodealer/_lib.php' );
    include( get_theme_root() . "/autos/vendor/autodealer/{$catalog}/api.php" );

    switch ($catalog) {
        case 'fiat':
            $api = new \Fiat();
            $response = $api->getFIATProduction($oid->mark, $oid->model);
            $car->productions = $response->prod;
            break;
        case 'etka':
            $oid->market = isset($_GET['market']) ? $_GET['market'] : '';
            $oid->dir = isset($_GET['dir']) ? $_GET['dir'] : 'R';
            $api = new \ETKA();
            $response = $api->getETKAProduction($oid->mark, $oid->market, $oid->model);
            $car->productions = $response->prod;
            $car->url = "/groups/?cat=etka&mark={$oid->mark}&market={$oid->market}&model={$oid->model}";
            break;
        case 'bmw':
            $oid->type = isset($_GET['type']) ? $_GET['type'] : '';
            $oid->series = isset($_GET['series']) ? $_GET['series'] : '';
            $oid->body = isset($_GET['body']) ? $_GET['body'] : '';
            $oid->market = isset($_GET['market']) ? $_GET['market'] : '';
            $oid->rule = isset($_GET['rule']) ? $_GET['rule'] : '';
            $oid->transmission = isset($_GET['transmission']) ? $_GET['transmission'] : '';
            $api = new \BMW();
            $response = $api->getBMWProduction($oid->type, $oid->series, $oid->body, $oid->model, $oid->market, $oid->rule, $oid->transmission);
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
            $car->url = "/groups/?cat={$catalog}&mark={$oid->mark}&type={$oid->type}&series={$oid->series}&body={$oid->body}&model={$oid->model}&market={$oid->market}&rule={$oid->rule}&transmission={$oid->transmission}";
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
        'oid' => $oid,
        'car' => $car
    ];

    return $data;
});
add_filter('sage/template/modifications/data', function($data) {
    // Общее
    $catalog = isset($_GET['cat']) ? $_GET['cat'] : '';

    // Оригиналы
    $oid = new \StdClass();
    $oid->mark = isset($_GET['mark']) ? $_GET['mark'] : '';
    $oid->model = isset($_GET['model']) ? $_GET['model'] : '';
    // Выходные объекты
    $car = new \StdClass();

    // Обязательно к применению
    include( get_theme_root() . '/autos/vendor/autodealer/_lib.php' );
    include( get_theme_root() . "/autos/vendor/autodealer/{$catalog}/api.php" );

    switch ($catalog) {
        case 'fiat':
            $api = new \Fiat();
            $response = $api->getFIATProduction($oid->mark, $oid->model);
            $car->productions = $response->prod;
            break;
        # Региональность - Audi, Volkswagen, Seat, Skoda
        case 'etka':
            $oid->market = isset($_GET['market']) ? $_GET['market'] : '';
            $oid->dir = isset($_GET['dir']) ? $_GET['dir'] : 'R';
            $api = new \ETKA();
            $response = $api->getETKAProduction($oid->mark, $oid->market, $oid->model);
            $car->productions = $response->prod;
            break;
        # Региональность - Nissan.Infiniti
        case 'nissan':
            $api = new \NIS();
            $oid->market = isset($_GET['market']) ? $_GET['market'] : '';
            $response = $api->getNisModiff($oid->market, $oid->model);
            $car->modifications = $response->aModif;
            $car->url = "/groups/?cat=nissan&market={$oid->market}&model={$oid->model}";
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
        'oid' => $oid,
        'car' => $car
    ];

    return $data;
});
add_filter('sage/template/groups/data', function($data) {
    $catalog = isset($_GET['cat']) ? $_GET['cat'] : '';
    $oid = new \StdClass();
    $oid->mark = isset($_GET['mark']) ? $_GET['mark'] : '';
    $oid->model = isset($_GET['model']) ? $_GET['model'] : '';
    $oid->market = isset($_GET['market']) ? $_GET['market'] : '';
    $oid->production = isset($_GET['production']) ? $_GET['production'] : '';
    $car = new \StdClass();

    // Обязательно к применению
    include( get_theme_root() . '/autos/vendor/autodealer/_lib.php' );
    include( get_theme_root() . "/autos/vendor/autodealer/{$catalog}/api.php" );

    switch ($catalog) {
        case 'fiat':
            $api = new \Fiat();
            $response = $api->getFIATGroup($oid->mark, $oid->model, $oid->production);
            $car->groups = $response->groups;
            break;
        case 'etka':
            $oid->production_year = isset($_GET['production_year']) ? $_GET['production_year'] : '';
            $oid->code = isset($_GET['code']) ? $_GET['code'] : '';
            $oid->dir = isset($_GET['dir']) ? $_GET['dir'] : '';
            $api = new \ETKA();
            $response = $api->getETKAGroups($oid->mark, $oid->market, $oid->model, $oid->production_year, $oid->code, $oid->dir);
            $car->groups = $response->hg;
            $car->image = get_stylesheet_directory_uri() . '/vendor/autodealer/media/images/etka/groups/';
            break;
        case 'bmw':
            $oid->type = isset($_GET['type']) ? $_GET['type'] : '';
            $oid->series = isset($_GET['series']) ? $_GET['series'] : '';
            $oid->body = isset($_GET['body']) ? $_GET['body'] : '';
            $oid->market = isset($_GET['market']) ? $_GET['market'] : '';
            $oid->rule = isset($_GET['rule']) ? $_GET['rule'] : '';
            $oid->transmission = isset($_GET['transmission']) ? $_GET['transmission'] : '';
            $oid->production = isset($_GET['production']) ? $_GET['production'] : '';

            $api = new \BMW();
            $response = $api->getBMWGroups($oid->type, $oid->series, $oid->body, $oid->model, $oid->market, $oid->rule, $oid->transmission, $oid->production, 'ru');
            $car->url = "/subgroups/?cat={$catalog}&mark={$oid->mark}&type={$oid->type}&series={$oid->series}&body={$oid->body}&model={$oid->model}&market={$oid->market}&rule={$oid->rule}&transmission={$oid->transmission}&production={$oid->production}";
            $car->series = $api->_getSeries($response->aBreads->models->name);
            $car->mark = $api->_getMarkName($oid->mark);
            $car->modification = $response->aBreads->groups->name;
            $car->groups = $response->aData;
            $car->info = $response->modelInfo;
            break;
        case 'nissan':
            $api = new \NIS();
            $oid->modification = isset($_GET['modification']) ? $_GET['modification'] : '';
            $oid->mark = (strpos(strtolower($oid->market), 'inf') > 1 ) ? 'infiniti' : 'nissan';
            $response = $api->getNisModInfo($oid->market, $oid->model, $oid->modification);
            $car->image = $response->Img;
            $car->groups = $response->aModInfo;
            $car->url = "/subgroups/?cat=nissan&market={$oid->market}&model={$oid->model}&modification={$oid->modification}";
            break;
        case 'toyota':
            $api = new \TOY();
            $oid->compl = isset($_GET['compl']) ? $_GET['compl'] : '';
            $oid->option = isset($_GET['option']) ? $_GET['option'] : '';
            $oid->code = isset($_GET['code']) ? $_GET['code'] : '';
            // дополнительные данные
            $oid->vin = isset($_GET['vin']) ? $_GET['vin'] : '';
            $oid->vdate = isset($_GET['vdate']) ? $_GET['vdate'] : '';
            $oid->siyopt = isset($_GET['siyopt']) ? $_GET['siyopt'] : '';
            $response = $api->getToyModCompl($oid->market, $oid->model, $oid->compl, $oid->option, $oid->code, $oid->vin, $oid->vdate, $oid->siyopt);

            // группы
            $car->groups = $response->aCompl;
            // адрес для ссылок
            $car->url = "/illustration/?cat=toyota&mark={$oid->mark}&market={$oid->market}&model={$oid->model}&compl={$oid->compl}&option={$oid->option}&code={$oid->code}";
            // дополнительный url
            $car->getString = ""
                . ( ( $oid->vin )   ? "&vin={$oid->vin}" : "" )
                . ( ( $oid->vdate ) ? "&vdate={$oid->vdate}" : "" )
                . ( ( $oid->siyopt )? "&siyopt={$oid->siyopt}" : "" );
            break;

        default:
            echo 'Error';
            break;
    }

    $data = [
        'catalog' => $catalog,
        'oid' => $oid,
        'car' => $car
    ];

    return $data;
});
add_filter('sage/template/subgroups/data', function($data) {
    $catalog = isset($_GET['cat']) ? $_GET['cat'] : '';
    $oid = new \StdClass();
    $car = new \StdClass();

    // Обязательно к применению
    include( get_theme_root() . '/autos/vendor/autodealer/_lib.php' );
    include( get_theme_root() . "/autos/vendor/autodealer/{$catalog}/api.php" );

    switch ($catalog) {
        case 'fiat':
            $api = new \Fiat();
            $oid->mark = isset($_GET['mark']) ? $_GET['mark'] : '';
            $oid->model = isset($_GET['model']) ? $_GET['model'] : '';
            $oid->production = isset($_GET['production']) ? $_GET['production'] : '';
            $oid->group = isset($_GET['group']) ? $_GET['group'] : '';

            $response = $api->getFIATSubGroup($oid->mark, $oid->model, $oid->production, $oid->group);
            $car->subgroups = $response->subGroups;
            break;
        case 'etka':
            $api = new \ETKA();
            $oid->mark = isset($_GET['mark']) ? $_GET['mark'] : '';
            $oid->model = isset($_GET['model']) ? $_GET['model'] : '';
            $oid->market = isset($_GET['market']) ? $_GET['market'] : '';
            $oid->production_year = isset($_GET['production_year']) ? $_GET['production_year'] : '';
            $oid->group = isset($_GET['group']) ? $_GET['group'] : '';
            $oid->dir = isset($_GET['dir']) ? $_GET['dir'] : 'R';
            $oid->code = isset($_GET['code']) ? $_GET['code'] : '';

            $response = $api->getETKASubGroups($oid->mark, $oid->market, $oid->model, $oid->production_year, $oid->code, $oid->dir, $oid->group);
            $car->subgroups = array_filter($response->ug, function($value) {
                return $value->ou != 'O';
            });
            break;
        case 'bmw':
            $api = new \BMW();
            $oid->mark = isset($_GET['mark']) ? $_GET['mark'] : '';
            $oid->type = isset($_GET['type']) ? $_GET['type'] : '';
            $oid->series = isset($_GET['series']) ? $_GET['series'] : '';
            $oid->body = isset($_GET['body']) ? $_GET['body'] : '';
            $oid->model = isset($_GET['model']) ? $_GET['model'] : '';
            $oid->market = isset($_GET['market']) ? $_GET['market'] : '';
            $oid->rule = isset($_GET['rule']) ? $_GET['rule'] : '';
            $oid->transmission = isset($_GET['transmission']) ? $_GET['transmission'] : '';
            $oid->production = isset($_GET['production']) ? $_GET['production'] : '';
            $oid->group = isset($_GET['group']) ? $_GET['group'] : '';

            $response = $api->getBMWSubGroups($oid->type, $oid->series, $oid->body, $oid->model, $oid->market, $oid->rule, $oid->transmission, $oid->production, $oid->group, 'ru');
            $car->subgroups = $response->aData;
            $car->info = $response->modelInfo;
            $car->url = "/illustration/?cat=bmw&mark={$oid->mark}&type={$oid->type}&series={$oid->series}&body={$oid->body}&model={$oid->model}&market={$oid->market}&rule={$oid->rule}&transmission={$oid->transmission}&production={$oid->production}&group={$oid->group}";
            break;
        case 'nissan':
            $api = new \NIS();
            $oid->model = isset($_GET['model']) ? $_GET['model'] : '';
            $oid->market = isset($_GET['market']) ? $_GET['market'] : '';
            $oid->mark = (strpos(strtolower($oid->market), 'inf') > 1 ) ? 'infiniti' : 'nissan';
            $oid->group = isset($_GET['group']) ? $_GET['group'] : '';
            $oid->modification = isset($_GET['modification']) ? $_GET['modification'] : '';

            $response = $api->getNisGroup($oid->market, $oid->model, $oid->modification, $oid->group);
            $car->image = $response->Img;
            $car->subgroups = $response->aGroup;
            $car->url = "/illustration/?cat=nissan&market={$oid->market}&model={$oid->model}&modification={$oid->modification}&group={$oid->group}";
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
        'oid' => $oid,
        'car' => $car
    ];

    return $data;
});
add_filter('sage/template/illustration/data', function($data) {
    $catalog = isset($_GET['cat']) ? $_GET['cat'] : '';
    $oid = new \StdClass();
    $oid->mark = isset($_GET['mark']) ? $_GET['mark'] : '';
    $oid->model = isset($_GET['model']) ? $_GET['model'] : '';
    $oid->market = isset($_GET['market']) ? $_GET['market'] : '';
    $oid->production = isset($_GET['production']) ? $_GET['production'] : '';
    $oid->group = isset($_GET['group']) ? $_GET['group'] : '';
    $oid->subgroup = isset($_GET['subgroup']) ? $_GET['subgroup'] : '';
    $car = new \StdClass();

    // Обязательно к применению
    include( get_theme_root() . '/autos/vendor/autodealer/_lib.php' );
    include( get_theme_root() . "/autos/vendor/autodealer/{$catalog}/api.php" );

    switch ($catalog) {
        case 'fiat':
            $api = new \Fiat();
            $oid->table = isset($_GET['table']) ? urldecode(base64_decode($_GET['table'])) : '';
            $response = $api->getFIATPartDrawData($oid->production, $oid->group, $oid->subgroup, $oid->table);
            $variant = $response->partDrawData->variants[0]->variante;
            $car->parts = $response;
            $response = $api->getFIATDraw($oid->mark, $oid->model, $oid->production, $oid->group, $oid->subgroup, $oid->table, $variant, 0.5);
            $car->illustration = $response;
            break;
        case 'etka':
            $api = new \ETKA();
            $oid->production_year = isset($_GET['production_year']) ? $_GET['production_year'] : '';
            $oid->dir = isset($_GET['dir']) ? $_GET['dir'] : 'R';
            $oid->code = isset($_GET['code']) ? $_GET['code'] : '';
            $oid->graphic = isset($_GET['graphic']) ? $_GET['graphic'] : '';
            $response = $api->getETKAIllustration($oid->mark, $oid->market, $oid->model, $oid->production_year, $oid->code, $oid->dir, $oid->group, $oid->subgroup, $oid->graphic, 0.5);
            $car->illustration = $response;
            $car->url = "/illustration/?cat={ $catalog }&mark={ $oid->mark }&market={ $oid->market }&model={ $oid->model }&production_year={ $oid->production_year }&code={ $oid->code }";
            break;
        case 'bmw':
            $api = new \BMW();
            $oid->mark   = $api->rcv('mark');
            $oid->type   = $api->rcv('type');
            $oid->series = $api->rcv('series');
            $oid->body   = $api->rcv('body');
            $oid->model  = $api->rcv('model');
            $oid->market = $api->rcv('market');
            $oid->rule   = $api->rcv('rule');
            $oid->trans  = $api->rcv('transmission');
            $oid->prod   = $api->rcv('production');
            $oid->group  = $api->rcv('group');
            $oid->graphic= $api->rcv('graphic');

            $response = $api->getBMWDetailsMap($oid->type,$oid->series,$oid->body,$oid->model,$oid->market,$oid->rule,$oid->trans,$oid->prod,$oid->group,$oid->graphic, "ru");
            $car->aLabels   = $response->labels;
            $car->aDetails  = $response->details;
            $car->aComments = $response->comments;
            $car->imgInfo = $response->imgInfo;
            $car->url = "/illustration/?cat=bmw&mark={$oid->mark}&type={$oid->type}&series={$oid->series}&body={$oid->body}&model={$oid->model}&market={$oid->market}&rule={$oid->rule}&transmission={$oid->trans}&production={$oid->prod}&group={$oid->group}";
            break;
        case 'nissan':
            $api = new \NIS();
            $oid->modification = isset($_GET['modification']) ? $_GET['modification'] : '';
            $oid->figure = $api->rcv('figure');
            $oid->subimage = '';
            $oid->sec = '';
            $response = $api->getNISPic($oid->market, $oid->model, $oid->modification, $oid->group, $oid->figure, $oid->subimage, $oid->sec);

            $car->illustration = $response;
            $car->nextUrl = "/detail/?cat=nissan&market={$oid->market}&model={$oid->model}&modif={$oid->modification}&group={$oid->group}&figure={$oid->figure}&subfig=";
            $car->nextSecUrl = "/illustration/?cat=nissan&market={$oid->market}&model={$oid->model}&modif={$oid->modification}&group={$oid->group}&figure={$oid->figure}&subfig=";
            $car->secUrl = "/illustration/?cat=nissan&market={$oid->market}&model={$oid->model}&modif={$oid->modification}&group={$oid->group}&figure=";

            break;
        case 'toyota':
            $api = new \TOY();
            $oid->compl = isset($_GET['compl']) ? $_GET['compl'] : '';
            $oid->option = isset($_GET['option']) ? $_GET['option'] : '';
            $oid->code = isset($_GET['code']) ? $_GET['code'] : '';
            $oid->group = isset($_GET['group']) ? $_GET['group'] : '';
            $oid->graphic = isset($_GET['graphic']) ? $_GET['graphic'] : '';
            // дополнительные данные
            $oid->vin = isset($_GET['vin']) ? $_GET['vin'] : '';
            $oid->vdate = isset($_GET['vdate']) ? $_GET['vdate'] : '';
            $oid->siyopt = isset($_GET['siyopt']) ? $_GET['siyopt'] : '';

            $car->getString = ""
                . ( ( $oid->vin )    ? "&vin=$oid->vin"      : "" )
                . ( ( $oid->vdate )  ? "&vdate=$oid->vdate"  : "" )
                . ( ( $oid->siyopt ) ? "&siyopt=$oid->siyopt" : "" );
            break;
        default:
            echo 'Error';
            break;
    }

    $data = [
        'catalog' => $catalog,
        'oid' => $oid,
        'car' => $car
    ];

    return $data;
});
// Aftermarket
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

    $catalog = isset($_GET['cat']) ? $_GET['cat'] : '';

    /** Обязательно к применению */
    include( get_theme_root() . '/autos/vendor/autodealer/_lib.php' );
    include( get_theme_root() . "/autos/vendor/autodealer/{$catalog}/api.php" );

    $api    = new \TD();
    $id     = new \StdClass();
    $car    = new \StdClass();
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

    $data = [
        'catalog' => $catalog,
        'id' => $id,
        'car' => $car
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
