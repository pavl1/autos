<?php

namespace App;
// TLC Transients
include ( get_theme_root() . '/autos/vendor/markjaquith/wp-tlc-transients/class-tlc-transient.php' );
include ( get_theme_root() . '/autos/vendor/autodealer/_lib.php' );

add_filter('sage/template/frontpage/data', function($data) {

    $whitelist = ['acura', 'daihatsu', 'datsun', 'honda', 'isuzu', 'mazda', 'mitsubishi', 'subaru', 'suzuki', 'infiniti', 'nissan', 'lexus', 'scion', 'toyota', 'skoda', 'audi', 'volkswagen', 'bmw'];
    $east = ['acura', 'daihatsu', 'datsun', 'honda', 'infiniti', 'isuzu', 'lexus', 'mazda', 'mitsubishi', 'nissan', 'scion', 'subaru', 'suzuki', 'toyota'];
    $west = ['skoda', 'audi', 'bmw', 'volkswagen'];

    $marksOriginal = tlc_transient( 'marks_original' )
                ->expires_in( 30 )
                ->updates_with( __NAMESPACE__ . '\api_get_marks_original', array( $whitelist ) )
                ->get();
    if ( ! $marksOriginal )
        $marksOriginal = api_get_marks_original($whitelist);

    $marksAftermarket = tlc_transient( 'marks_aftermarket' )
                ->expires_in( 30 )
                ->updates_with( __NAMESPACE__ . '\api_get_marks_aftermarket', array( $whitelist ) )
                ->get();
    if ( ! $marksAftermarket )
        $marksAftermarket = api_get_marks_aftermarket($whitelist);

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
            // $entry =
            $url = "/$entry/?cat=$iface&{$var}={$mark}";
        }
        else $url = "/models/?cat=adc&type={$value->type_id}&mark={$value->mark_id}&flag={$value->flags}";

        $mark = new \StdClass;
        $mark->original = $value;
        $mark->original->url = $url;
        $mark->original->catalog = isset($iface) ? $iface : 'adc';
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

    $data = [ 'marks' => $items ];

    return $data;
});
function api_get_marks_original($whitelist) {
    include( get_theme_root() . '/autos/vendor/autodealer/adc/api.php' );
    $ADC = new \ADC();

    $items = array_filter($ADC->getMarkList(9)->marks, function($mark) use ($whitelist) {
        if ( !in_array(strtolower($mark->mark_name), $whitelist) ) return false;
        return 1000 != $mark->mark_id;
    });

    return $items;
}
function api_get_marks_aftermarket($whitelist) {
    include( get_theme_root() . '/autos/vendor/autodealer/td/api.php' );
    $TD = new \TD();

    $items = array_filter($TD->getTDMarks('pc')->marks, function($mark) use ($whitelist) {
        if ( !in_array( explode(' ', strtolower($mark->mfa_brand))[0], $whitelist) ) return false;
        return true;
    });

    return $items;
}
add_filter('sage/template/illustration/data', function($data) {
    $oid = new \StdClass();
    $car = new \StdClass();
    $oid->catalog = isset($_GET['cat']) ? $_GET['cat'] : '';
    $oid->mark = isset($_GET['mark']) ? $_GET['mark'] : '';
    $oid->model = isset($_GET['model']) ? $_GET['model'] : '';
    $oid->market = isset($_GET['market']) ? $_GET['market'] : '';
    $oid->production = isset($_GET['production']) ? $_GET['production'] : '';
    $oid->group = isset($_GET['group']) ? $_GET['group'] : '';
    $oid->subgroup = isset($_GET['subgroup']) ? $_GET['subgroup'] : '';

    // Обязательно к применению
    include( get_theme_root() . "/autos/vendor/autodealer/{$oid->catalog}/api.php" );

    switch ($oid->catalog) {
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
            $oid->production_year = isset($_GET['production']) ? $_GET['production'] : '';
            $oid->dir = isset($_GET['dir']) ? $_GET['dir'] : 'R';
            $oid->code = isset($_GET['code']) ? $_GET['code'] : '';
            $oid->graphic = isset($_GET['graphic']) ? $_GET['graphic'] : '';
            $response = $api->getETKAIllustration($oid->mark, $oid->market, $oid->model, $oid->production_year, $oid->code, $oid->dir, $oid->group, $oid->subgroup, $oid->graphic, 0.5);
            $car->illustration = $response;
            $car->url = "/illustration/?cat={ $oid->catalog }&mark={ $oid->mark }&market={ $oid->market }&model={ $oid->model }&production_year={ $oid->production_year }&code={ $oid->code }";
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
        case 'adc':
            $oid->model = isset($_GET['model']) ? $_GET['model'] : '';
            $oid->tree = isset($_GET['tree']) ? $_GET['tree'] : '';
            $oid->jump = isset($_GET['jump']) ? $_GET['jump'] : '';
            $api = new \ADC();
            $response = $api->getDetails($oid->model, $oid->tree, $oid->jump);
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
        'catalog' => $oid->catalog,
        'oid' => $oid,
        'car' => $car
    ];

    return $data;
});

/* models function($data) {
    // Общее
    $oid = new \StdClass();
    $car = new \StdClass();
    $oid->catalog = isset($_GET['cat']) ? $_GET['cat'] : '';

    // Обязательно к применению
    include( get_theme_root() . '/autos/vendor/autodealer/_lib.php' );
    include( get_theme_root() . "/autos/vendor/autodealer/{$oid->catalog}/api.php" );

    switch ($oid->catalog) {
        case 'etka':
            $oid->mark = isset($_GET['mark']) ? $_GET['mark'] : '';
            $api = new \ETKA();
            $response = $api->getETKAMarkets($oid->mark);
            $car->markets = $response->markets;
            $car->models = array();
            foreach ($car->markets as $value)
                $car->models[$value->code] = $api->getETKAModels($oid->mark, $value->code)->models;
            break;
        case 'bmw':
            $oid->mark = isset($_GET['mark']) ? $_GET['mark'] : '';
            $oid->type = isset($_GET['type']) ? $_GET['type'] : '';
            $oid->series = isset($_GET['series']) ? $_GET['series'] : '';
            $api = new \BMW();
            $response = $api->getBMWModels($oid->type, $oid->series);
            $car->series = $api->_getSeries($response->aBreads->models->name);
            $car->mark = $api->_getMarkName($oid->mark);
            $car->models = $response->aModels;
            echo '<pre>'; var_dump($car->models); echo '</pre>';
            break;
        case 'nissan':
            $oid->mark = isset($_GET['mark']) ? $_GET['mark'] : '';
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
            $oid->mark = isset($_GET['mark']) ? $_GET['mark'] : '';
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
            $oid->type = isset($_GET['type']) ? $_GET['type'] : '';
            $oid->mark = isset($_GET['mark']) ? $_GET['mark'] : '';
            $api = new \TD();
            $response = $api->getTDModels('pc', $oid->mark);
            $car->info = $response->modelInfo;
            $car->models = $response->models;
            break;
        case 'adc':
            $oid->type = isset($_GET['type']) ? $_GET['type'] : '';
            $oid->mark = isset($_GET['mark']) ? $_GET['mark'] : '';
            $api = new \ADC();
            $response = $api->getModelList($oid->mark, $oid->type);
            $car->models = $response->models;
            $car->url = "/tree/?cat=adc&mark{$oid->mark}&type={$oid->type}";
            break;
        default:
            echo 'Error';
            break;
    }

    $data = [
        'oid' => $oid,
        'oid' => $oid,
        'car' => $car
    ];

    return $data;
});
*/
/* options function($data) {
    $oid = new \StdClass();
    $car = new \StdClass();
    $oid->catalog = isset($_GET['cat']) ? $_GET['cat'] : '';
    $oid->mark = isset($_GET['mark']) ? $_GET['mark'] : '';
    $oid->model = isset($_GET['model']) ? $_GET['model'] : '';

    // Обязательно к применению
    include( get_theme_root() . '/autos/vendor/autodealer/_lib.php' );
    include( get_theme_root() . "/autos/vendor/autodealer/{$oid->catalog}/api.php" );

    switch ($oid->catalog) {
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
        'catalog' => $oid->catalog,
        'oid' => $oid,
        'car' => $car
    ];

    return $data;
});
*/
/* productions function($data) {
    $oid = new \StdClass();
    $car = new \StdClass();
    $oid->catalog = isset($_GET['cat']) ? $_GET['cat'] : '';
    $oid->mark = isset($_GET['mark']) ? $_GET['mark'] : '';
    $oid->model = isset($_GET['model']) ? $_GET['model'] : '';

    // Обязательно к применению
    include( get_theme_root() . '/autos/vendor/autodealer/_lib.php' );
    include( get_theme_root() . "/autos/vendor/autodealer/{$oid->catalog}/api.php" );

    switch ($oid->catalog) {
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
            $car->url = "/groups/?cat={$oid->catalog}&mark={$oid->mark}&type={$oid->type}&series={$oid->series}&body={$oid->body}&model={$oid->model}&market={$oid->market}&rule={$oid->rule}&transmission={$oid->transmission}";
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
        'catalog' => $oid->catalog,
        'oid' => $oid,
        'car' => $car
    ];

    return $data;
});
*/
add_filter('sage/template/modifications/data', function($data) {
    $oid = new \StdClass();
    $car = new \StdClass();
    $oid->catalog = isset($_GET['cat']) ? $_GET['cat'] : '';
    $oid->mark = isset($_GET['mark']) ? $_GET['mark'] : '';
    $oid->model = isset($_GET['model']) ? $_GET['model'] : '';

    // Обязательно к применению
    include( get_theme_root() . '/autos/vendor/autodealer/_lib.php' );
    include( get_theme_root() . "/autos/vendor/autodealer/{$oid->catalog}/api.php" );

    switch ($oid->catalog) {
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
        'catalog' => $oid->catalog,
        'oid' => $oid,
        'car' => $car
    ];

    return $data;
});
add_filter('sage/template/groups/data', function($data) {
    $oid = new \StdClass();
    $car = new \StdClass();
    $oid->catalog = isset($_GET['cat']) ? $_GET['cat'] : '';
    $oid->mark = isset($_GET['mark']) ? $_GET['mark'] : '';
    $oid->model = isset($_GET['model']) ? $_GET['model'] : '';
    $oid->market = isset($_GET['market']) ? $_GET['market'] : '';
    $oid->production = isset($_GET['production']) ? $_GET['production'] : '';

    // Обязательно к применению
    include( get_theme_root() . '/autos/vendor/autodealer/_lib.php' );
    include( get_theme_root() . "/autos/vendor/autodealer/{$oid->catalog}/api.php" );

    switch ($oid->catalog) {
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
            $car->url = "/subgroups/?cat={$oid->catalog}&mark={$oid->mark}&type={$oid->type}&series={$oid->series}&body={$oid->body}&model={$oid->model}&market={$oid->market}&rule={$oid->rule}&transmission={$oid->transmission}&production={$oid->production}";
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
        'oid' => $oid,
        'car' => $car
    ];

    return $data;
});

// Aftermarket + ADC
add_filter('sage/template/equipments/data', function($data) {
    $api    = new \TD();
    $oid     = new \StdClass();
    $car    = new \StdClass();
    $oid->catalog = isset($_GET['cat']) ? $_GET['cat'] : '';
    $oid->mark   = $api->rcv('mark');
    $oid->model  = $api->rcv('model');

    /** Обязательно к применению */
    include( get_theme_root() . '/autos/vendor/autodealer/_lib.php' );
    include( get_theme_root() . "/autos/vendor/autodealer/{$oid->catalog}/api.php" );


    $response = $api->getTDCompl('pc', $oid->mark, $oid->model);
    $car->equipments = $response->compl;
    $car->info = $response->modelInfo;

    $data = [
        'catalog' => $oid->catalog,
        'oid' => $oid,
        'car' => $car
    ];

    return $data;
});
add_filter('sage/template/tree/data', function($data) {
    $oid = new \StdClass();
    $car = new \StdClass();
    $oid->catalog = isset($_GET['cat']) ? $_GET['cat'] : '';

    /** Обязательно к применению */
    include( get_theme_root() . '/autos/vendor/autodealer/_lib.php' );
    include( get_theme_root() . "/autos/vendor/autodealer/{$oid->catalog}/api.php" );

    switch ($oid->catalog) {
        case 'td':
            $api = new \TD();
            $oid->mark = isset($_GET['mark']) ? $_GET['mark'] : '';
            $oid->model = isset($_GET['model']) ? $_GET['model'] : '';
            $oid->equipment = isset($_GET['equipment']) ? $_GET['equipment'] : '';

            $response = $api->getTDTree('pc', $oid->mark, $oid->model, $oid->equipment);

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
            $oid->model = isset($_GET['model']) ? $_GET['model'] : '';
            $api = new \ADC();
            $response = $api->getTreeList($oid->model, false);
            usort($response->details, function($a, $b) {
                if ( $a->parent_id == $b->parent_id ) {
                    return 0;
                }
                return $a->parent_id > $b->parent_id ? -1 : 1;
            });
            $response->details = array_combine(array_column($response->details, 'oid'), $response->details);
            foreach ($response->details as $value) {
                if ($value->parent_id == 0 ) {
                    $value->ready = true;
                }
                $current = $value;
                $response->details[$current->parent_id]->childrens[] = $current;
            };
            $car->details = array_filter($response->details, function($item) { return isset($item->ready); });
            $car->url = "/illustration/?cat=adc&model={$oid->model}";

            break;
        default:
            break;
    }



    $data = [
        'oid' => $oid,
        'car' => $car,
        'catalog' => $oid->catalog
    ];

    return $data;
});
add_filter('sage/template/details/data', function($data) {
    $api    = new \TD();
    $oid     = new \StdClass();
    $car    = new \StdClass();

    $oid->mark       = isset($_GET['mark'])      ? $_GET['mark'] : '';
    $oid->model      = isset($_GET['model'])     ? $_GET['model'] : '';
    $oid->equipment  = isset($_GET['equipment']) ? $_GET['equipment'] : '';
    $oid->tree       = isset($_GET['tree'])      ? $_GET['tree'] : '';
    $oid->catalog = isset($_GET['cat']) ? $_GET['cat'] : '';

    /** Обязательно к применению */
    include( get_theme_root() . '/autos/vendor/autodealer/_lib.php' );
    include( get_theme_root() . "/autos/vendor/autodealer/{$oid->catalog}/api.php" );


    $response = $api->getTDDetails('pc', $oid->mark, $oid->model, $oid->equipment, $oid->tree);
    $car->info      = $response->modelInfo;
    $car->group     = $response->group;
    $car->details   = $response->details;

    $data = [
        'catalog'   => $oid->catalog,
        'oid'        => $oid,
        'car'       => $car
    ];

    return $data;
});

function get_models($data = '') {
    // Общее
    $oid = new \StdClass();
    $car = new \StdClass();

    if ( ! wp_doing_ajax() ) :
        $oid->catalog = isset($_GET['cat']) ? $_GET['cat'] : '';
    else :
        $oid = isset($_POST['oid']) ? (object) $_POST['oid'] : '';
    endif;
    echo '<pre>'; var_dump($oid); echo '</pre>';

    /** Обязательно к применению */
    include( get_theme_root() . "/autos/vendor/autodealer/{$oid->catalog}/api.php" );

    switch ($oid->catalog) {
        case 'etka':
            $oid->mark = isset($_GET['mark']) ? $_GET['mark'] : '';
            $api = new \ETKA();
            $response = $api->getETKAMarkets($oid->mark);
            $car->markets = $response->markets;
            $car->models = array();
            foreach ($car->markets as $value)
                $car->models[$value->code] = $api->getETKAModels($oid->mark, $value->code)->models;
            break;
        case 'bmw':
            if ( ! wp_doing_ajax() ) :
                $oid->mark = isset($_GET['mark']) ? $_GET['mark'] : '';
                $oid->type = isset($_GET['type']) ? $_GET['type'] : '';
                $oid->series = isset($_GET['series']) ? $_GET['series'] : '';
            endif;

            $api = new \BMW();
            $response = $api->getBMWModels($oid->type, $oid->series);
            $car->series = $api->_getSeries($response->aBreads->models->name);
            $car->mark = $api->_getMarkName($oid->mark);
            $car->models = $response->aModels;
            break;
        case 'nissan':
            $oid->mark = isset($_GET['mark']) ? $_GET['mark'] : '';
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
            $oid->mark = isset($_GET['mark']) ? $_GET['mark'] : '';
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
            $oid->type = isset($_GET['type']) ? $_GET['type'] : '';
            $oid->mark = isset($_GET['mark']) ? $_GET['mark'] : '';
            $api = new \TD();
            $response = $api->getTDModels('pc', $oid->mark);
            $car->info = $response->modelInfo;
            $car->models = $response->models;
            break;
        case 'adc':
            $oid->type = isset($_GET['type']) ? $_GET['type'] : '';
            $oid->mark = isset($_GET['mark']) ? $_GET['mark'] : '';
            $api = new \ADC();
            $response = $api->getModelList($oid->mark, $oid->type);
            $car->models = $response->models;
            $car->url = "/tree/?cat=adc&mark{$oid->mark}&type={$oid->type}";
            break;
        default:
            echo 'Error';
            break;
    }

    $data = [
        'oid' => $oid,
        'car' => $car
    ];

    if ( wp_doing_ajax() ) wp_send_json_success( $data );
    return $data;
}
function get_options($data = '') {
    $oid = new \StdClass();
    $car = new \StdClass();

    if ( ! wp_doing_ajax() ) :
        $oid->catalog = isset($_GET['cat']) ? $_GET['cat'] : '';
        $oid->mark = isset($_GET['mark']) ? $_GET['mark'] : '';
        $oid->model = isset($_GET['model']) ? $_GET['model'] : '';
    else :
        $oid = isset($_POST['oid']) ? (object) $_POST['oid'] : '';
    endif;

    // Обязательно к применению
    include( get_theme_root() . '/autos/vendor/autodealer/_lib.php' );
    include( get_theme_root() . "/autos/vendor/autodealer/{$oid->catalog}/api.php" );

    switch ($oid->catalog) {
        case 'bmw':
            if ( ! wp_doing_ajax() ) :
                $oid->type = isset($_GET['type']) ? $_GET['type'] : '';
                $oid->series = isset($_GET['series']) ? $_GET['series'] : '';
                $oid->body = isset($_GET['body']) ? $_GET['body'] : '';
                $oid->market = isset($_GET['market']) ? $_GET['market'] : '';
            endif;
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
        'oid' => $oid,
        'car' => $car
    ];

    if ( wp_doing_ajax() ) wp_send_json_success( $data );
    return $data;
}
function get_production($data = '') {

    $oid = new \StdClass();
    $car = new \StdClass();

    if ( ! wp_doing_ajax() ) :
        $oid->catalog = isset($_GET['cat']) ? $_GET['cat'] : '';
        $oid->mark = isset($_GET['mark']) ? $_GET['mark'] : '';
        $oid->model = isset($_GET['model']) ? $_GET['model'] : '';
    else :
        $oid = isset($_POST['oid']) ? (object) $_POST['oid'] : '';
    endif;

    // Обязательно к применению
    include( get_theme_root() . '/autos/vendor/autodealer/_lib.php' );
    include( get_theme_root() . "/autos/vendor/autodealer/{$oid->catalog}/api.php" );

    switch ($oid->catalog) {
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
            if ( ! wp_doing_ajax() ) :
                $oid->type = isset($_GET['type']) ? $_GET['type'] : '';
                $oid->series = isset($_GET['series']) ? $_GET['series'] : '';
                $oid->body = isset($_GET['body']) ? $_GET['body'] : '';
                $oid->market = isset($_GET['market']) ? $_GET['market'] : '';
                $oid->rule = isset($_GET['rule']) ? $_GET['rule'] : '';
                $oid->transmission = isset($_GET['transmission']) ? $_GET['transmission'] : '';
            endif;
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
            $car->url = "/groups/?cat={$oid->catalog}&mark={$oid->mark}&type={$oid->type}&series={$oid->series}&body={$oid->body}&model={$oid->model}&market={$oid->market}&rule={$oid->rule}&transmission={$oid->transmission}";
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
        'oid' => $oid,
        'car' => $car
    ];

    if ( wp_doing_ajax() ) wp_send_json_success($data);
    return $data;
}
function get_subgroups($data = '') {
    $oid = new \StdClass();
    $car = new \StdClass();

    if ( ! wp_doing_ajax() ) :
        $oid->catalog = isset($_GET['cat']) ? $_GET['cat'] : '';
    else :
        $oid = isset($_POST['oid']) ? (object) $_POST['oid'] : '';
    endif;

    // Обязательно к применению
    include( get_theme_root() . "/autos/vendor/autodealer/{$oid->catalog}/api.php" );

    switch ($oid->catalog) {
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
            if ( ! wp_doing_ajax() ) :
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
            endif;
            $api = new \BMW();

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
        'oid' => $oid,
        'car' => $car
    ];

    if ( wp_doing_ajax() ) wp_send_json_success($data);
    return $data;
}





/* BMW */

// Series
add_action('wp_ajax_bmw_series', __NAMESPACE__ . '\bmw_series');
add_action('wp_ajax_nopriv_bmw_series', __NAMESPACE__ . '\bmw_series');
// Models
add_action('wp_ajax_bmw_models', __NAMESPACE__ . '\bmw_models');
add_action('wp_ajax_nopriv_bmw_models', __NAMESPACE__ . '\bmw_models');
// Options
add_action('wp_ajax_bmw_options', __NAMESPACE__ . '\bmw_options');
add_action('wp_ajax_nopriv_bmw_options', __NAMESPACE__ . '\bmw_options');
// Production
add_action('wp_ajax_bmw_production', __NAMESPACE__ . '\bmw_production');
add_action('wp_ajax_nopriv_bmw_production', __NAMESPACE__ . '\bmw_production');
// Groups
add_action('wp_ajax_bmw_groups', __NAMESPACE__ . '\bmw_groups');
add_action('wp_ajax_nopriv_bmw_groups', __NAMESPACE__ . '\bmw_groups');
// Subgroups
add_action('wp_ajax_bmw_subgroups', __NAMESPACE__ . '\bmw_subgroups');
add_action('wp_ajax_nopriv_bmw_subgroups', __NAMESPACE__ . '\bmw_subgroups');


function api_bmw_series($oid) {
    include( get_theme_root() . "/autos/vendor/autodealer/{$oid->catalog}/api.php" );
    $api = new \BMW();
    $series = [];
    foreach ($api->getBMWCatalogs($oid->mark)->vt as $value) {
        $item = new \StdClass();
        $item->id = $value->Baureihe;
        $item->name = $value->ExtBaureihe;
        $series[] = $item;
    }
    return $series;
}
function api_bmw_models($oid) {
    include( get_theme_root() . "/autos/vendor/autodealer/{$oid->catalog}/api.php" );
    $api = new \BMW();
    $response = new \StdClass();
    $models = [];
    $api_response = $api->getBMWModels($oid->type, $oid->series)->aModels;
    foreach ($api_response[0]->models as $value) {
        $models[] = $value;
    }
    $response = $api_response[0];
    $response->models = $models;
    return $response;
}
function api_bmw_options($oid) {
    include( get_theme_root() . "/autos/vendor/autodealer/{$oid->catalog}/api.php" );
    $api = new \BMW();
    return $api->getBMWOptions($oid->type, $oid->series, $oid->body, $oid->model, $oid->market)->aData;
}
function api_bmw_production($oid) {
    include( get_theme_root() . "/autos/vendor/autodealer/{$oid->catalog}/api.php" );
    $api = new \BMW();
    $response = $api->getBMWProduction($oid->type, $oid->series, $oid->body, $oid->model, $oid->market, $oid->rule, $oid->transmission)->aData;
    $response = current($response);
    $response = [
        "DateStart"  => $response->DateStart,
        "DateEnd"    => $response->DateEnd,
        "startYear"  => substr($response->DateStart,0,4),
        "startMonth" => substr($response->DateStart,4,2),
        "startDay"   => substr($response->DateStart,6,2),
        "endYear"    => substr($response->DateEnd,0,4),
        "endMonth"   => substr($response->DateEnd,4,2),
        "endDay"     => substr($response->DateEnd,6,2),
    ];
    return $response;
}
function api_bmw_groups($oid) {
    include( get_theme_root() . "/autos/vendor/autodealer/{$oid->catalog}/api.php" );
    $api = new \BMW();
    return $api->getBMWGroups($oid->type, $oid->series, $oid->body, $oid->model, $oid->market, $oid->rule, $oid->transmission, $oid->production, $oid->lang)->aData;
}
function api_bmw_subgroups($oid) {
    include( get_theme_root() . "/autos/vendor/autodealer/{$oid->catalog}/api.php" );
    $api = new \BMW();
    return $api->getBMWSubGroups($oid->type, $oid->series, $oid->body, $oid->model, $oid->market, $oid->rule, $oid->transmission, $oid->production, $oid->group)->aData;
}

function bmw_series() {
    $oid = new \StdClass();
    $oid = isset($_POST['oid']) ? (object) $_POST['oid'] : '';

    $series = tlc_transient( "{$oid->catalog}-series" )
            ->expires_in( 30 )
            ->updates_with( __NAMESPACE__ . '\api_bmw_series', array( $oid ) )
            ->get();
    if ( ! $series ) $series = api_bmw_series($oid);
    $data = [
        'series' => $series,
        'oid' => $oid
    ];
    wp_send_json_success($data);
}
function bmw_models() {
    $oid = new \StdClass();
    $oid = isset($_POST['oid']) ? (object) $_POST['oid'] : '';

    $markets = tlc_transient( "{$oid->catalog}-{$oid->series}" )
            ->expires_in( 0 )
            ->updates_with( __NAMESPACE__ . '\api_bmw_models', array( $oid ) )
            ->get();
    if ( ! $markets ) $markets = api_bmw_models($oid);

    $data = [
        'markets' => $markets,
        'oid' => $oid
    ];
    wp_send_json_success($data);
}
function bmw_options() {
    $oid = new \StdClass();
    $oid = isset($_POST['oid']) ? (object) $_POST['oid'] : '';
    $oid->catalog = 'bmw';
    $oid->mark = 'bmw';
    $oid->type = 'vt';

    $key = "{$oid->catalog}-{$oid->series}-{$oid->body}-{$oid->model}-{$oid->market}";
    $items = tlc_transient( $key )
            ->expires_in( 0 )
            ->updates_with( __NAMESPACE__ . '\api_bmw_options', array( $oid ) )
            ->get();
    if ( ! $items ) $items = api_bmw_options($oid);

    $data = [
        'oid' => $oid,
        'options' => $items
    ];

    wp_send_json_success( $data );
}
function bmw_production() {
    $oid = new \StdClass();
    $oid = isset($_POST['oid']) ? (object) $_POST['oid'] : '';
    $oid->catalog = 'bmw';
    $oid->mark = 'bmw';
    $oid->type = 'vt';

    $key = "{$oid->catalog}-{$oid->series}-{$oid->body}-{$oid->model}-{$oid->market}-{$oid->rule}-{$oid->transmission}";
    $items = tlc_transient( $key )
            ->expires_in( 0 )
            ->updates_with( __NAMESPACE__ . '\api_bmw_production', array( $oid ) )
            ->get();
    if ( ! $items ) $items = api_bmw_production($oid);

    $data = [
        'oid' => $oid,
        'production' => $items
    ];

    wp_send_json_success( $data );
}
function bmw_groups() {
    $oid = new \StdClass();
    $oid = isset($_POST['oid']) ? (object) $_POST['oid'] : '';
    $oid->catalog = 'bmw';
    $oid->mark = 'bmw';
    $oid->type = 'vt';
    $oid->lang = 'ru';

    $key = "{$oid->catalog}-{$oid->series}-{$oid->body}-{$oid->model}-{$oid->market}-{$oid->rule}-{$oid->transmission}-{$oid->production}";
    $items = tlc_transient( $key )
            ->expires_in( 0 )
            ->updates_with( __NAMESPACE__ . '\api_bmw_groups', array( $oid ) )
            ->get();
    if ( ! $items ) $items = api_bmw_groups($oid);

    $data = [
        'oid' => $oid,
        'groups' => $items
    ];

    wp_send_json_success( $data );
}
function bmw_subgroups() {
    $oid = new \StdClass();
    $oid = isset($_POST['oid']) ? (object) $_POST['oid'] : '';
    $oid->catalog = 'bmw';
    $oid->mark = 'bmw';
    $oid->type = 'vt';

    $key = "{$oid->catalog}-{$oid->series}-{$oid->body}-{$oid->model}-{$oid->market}-{$oid->rule}-{$oid->transmission}-{$oid->production}-{$oid->group}";
    $items = tlc_transient( $key )
            ->expires_in( 0 )
            ->updates_with( __NAMESPACE__ . '\api_bmw_subgroups', array( $oid ) )
            ->get();
    if ( ! $items ) $items = api_bmw_subgroups($oid);

    $data = [
        'oid' => $oid,
        'subgroups' => $items,
        'url' => "/illustration/?cat=bmw&mark={$oid->mark}&type={$oid->type}&series={$oid->series}&body={$oid->body}&model={$oid->model}&market={$oid->market}&rule={$oid->rule}&transmission={$oid->transmission}&production={$oid->production}&group={$oid->group}"
    ];

    wp_send_json_success( $data );
}

/* ETKA */

// Models
add_action('wp_ajax_etka_models', __NAMESPACE__ . '\etka_models');
add_action('wp_ajax_nopriv_etka_models', __NAMESPACE__ . '\etka_models');
// Production
add_action('wp_ajax_etka_production', __NAMESPACE__ . '\etka_production');
add_action('wp_ajax_nopriv_etka_production', __NAMESPACE__ . '\etka_production');
// Groups
add_action('wp_ajax_etka_groups', __NAMESPACE__ . '\etka_groups');
add_action('wp_ajax_nopriv_etka_groups', __NAMESPACE__ . '\etka_groups');
// Subgroups
add_action('wp_ajax_etka_subgroups', __NAMESPACE__ . '\etka_subgroups');
add_action('wp_ajax_nopriv_etka_subgroups', __NAMESPACE__ . '\etka_subgroups');

function api_etka_models($oid) {
    include( get_theme_root() . "/autos/vendor/autodealer/{$oid->catalog}/api.php" );
    $api = new \ETKA();

    $response = $api->getETKAMarkets($oid->mark);
    $markets = $response->markets;
    $models = array();
    foreach ($markets as $value)
        $models[$value->code] = $api->getETKAModels($oid->mark, $value->code)->models;

    return $models;
}
function api_etka_production($oid) {
    include( get_theme_root() . "/autos/vendor/autodealer/{$oid->catalog}/api.php" );
    $api = new \ETKA();
    return $api->getETKAProduction($oid->mark, $oid->market, $oid->model)->prod;
}
function api_etka_groups($oid) {
    include( get_theme_root() . "/autos/vendor/autodealer/{$oid->catalog}/api.php" );
    $api = new \ETKA();
    $groups = array();
    $response = $api->getETKAGroups($oid->mark, $oid->market, $oid->model, $oid->production, $oid->code, $oid->dir)->hg;
    foreach ($response as $value) $groups[] = $value;
    return $groups;
}
function api_etka_subgroups($oid) {
    include( get_theme_root() . "/autos/vendor/autodealer/{$oid->catalog}/api.php" );
    $api = new \ETKA();
    $subgroups = array();
    $response = $api->getETKASubGroups($oid->mark, $oid->market, $oid->model, $oid->production, $oid->code, $oid->dir, $oid->group)->ug;
    foreach ($response as $value) if ( $value->ou != 'O' ) $subgroups[] = $value;
    return $subgroups;
}

function etka_models() {
    $oid = new \StdClass();
    $oid = isset($_POST['oid']) ? (object) $_POST['oid'] : '';

    $markets = tlc_transient( "{$oid->catalog}-{$oid->mark}" )
            ->expires_in( 0 )
            ->updates_with( __NAMESPACE__ . '\api_etka_models', array( $oid ) )
            ->get();
    if ( ! $markets ) $markets = api_etka_models($oid);

    $data = [
        'markets' => $markets,
        'oid' => $oid
    ];
    wp_send_json_success($data);
}
function etka_production() {
    $oid = new \StdClass();
    $oid = isset($_POST['oid']) ? (object) $_POST['oid'] : '';

    $key = "{$oid->catalog}-{$oid->mark}-{$oid->market}-{$oid->model}";
    $items = tlc_transient( $key )
            ->expires_in( 0 )
            ->updates_with( __NAMESPACE__ . '\api_etka_production', array( $oid ) )
            ->get();
    if ( ! $items ) $items = api_etka_production($oid);

    $data = [
        'oid' => $oid,
        'items' => $items
    ];

    wp_send_json_success( $data );
}
function etka_groups() {
    $oid = new \StdClass();
    $oid = isset($_POST['oid']) ? (object) $_POST['oid'] : '';

    $key = "{$oid->catalog}-{$oid->mark}-{$oid->market}-{$oid->model}-{$oid->production}-{$oid->code}-{$oid->dir}";
    $items = tlc_transient( $key )
            ->expires_in( 0 )
            ->updates_with( __NAMESPACE__ . '\api_etka_groups', array( $oid ) )
            ->get();
    if ( ! $items ) $items = api_etka_groups($oid);

    $data = [
        'oid' => $oid,
        'items' => $items
    ];

    wp_send_json_success( $data );
}
function etka_subgroups() {
    $oid = new \StdClass();
    $oid = isset($_POST['oid']) ? (object) $_POST['oid'] : '';

    $key = "{$oid->catalog}-{$oid->mark}-{$oid->market}-{$oid->model}-{$oid->production}-{$oid->code}-{$oid->dir}-{$oid->group}";
    $items = tlc_transient( $key )
            ->expires_in( 0 )
            ->updates_with( __NAMESPACE__ . '\api_etka_subgroups', array( $oid ) )
            ->get();
    if ( ! $items ) $items = api_etka_subgroups($oid);

    $data = [
        'oid' => $oid,
        'items' => $items,
        'url' => "/illustration/?cat={$oid->catalog}&mark={$oid->mark}&market={$oid->market}&model={$oid->model}&production={$oid->production}&code={$oid->code}&group={$oid->group}"
    ];

    wp_send_json_success( $data );
}

/* Nissan */

// Models
add_action('wp_ajax_nissan_models', __NAMESPACE__ . '\nissan_models');
add_action('wp_ajax_nopriv_nissan_models', __NAMESPACE__ . '\nissan_models');
// Production
add_action('wp_ajax_nissan_modifications', __NAMESPACE__ . '\nissan_modifications');
add_action('wp_ajax_nopriv_nissan_modifications', __NAMESPACE__ . '\nissan_modifications');
// Groups
add_action('wp_ajax_nissan_groups', __NAMESPACE__ . '\nissan_groups');
add_action('wp_ajax_nopriv_nissan_groups', __NAMESPACE__ . '\nissan_groups');
// Subgroups
add_action('wp_ajax_nissan_subgroups', __NAMESPACE__ . '\nissan_subgroups');
add_action('wp_ajax_nopriv_nissan_subgroups', __NAMESPACE__ . '\nissan_subgroups');

function api_nissan_models($oid) {
    include( get_theme_root() . "/autos/vendor/autodealer/{$oid->catalog}/api.php" );
    $api = new \NIS();

    $markets = array();
    foreach ($api->getNisMarkets($oid->mark) as $key => $value) {
        $markets[$key] = new \StdClass;
        $markets[$key]->name = $value;
        $markets[$key]->models = $api->getNisModels($oid->mark, $key)->aModels;
    }

    return $markets;
}
function api_nissan_modifications($oid) {
    include( get_theme_root() . "/autos/vendor/autodealer/{$oid->catalog}/api.php" );
    $api = new \NIS();
    return $api->getNisModiff($oid->market, $oid->model)->aModif;
}
function api_nissan_groups($oid) {
    include( get_theme_root() . "/autos/vendor/autodealer/{$oid->catalog}/api.php" );
    $api = new \NIS();
    return $api->getNisModInfo($oid->market, $oid->model, $oid->modification)->aModInfo;
}
function api_nissan_subgroups($oid) {
    include( get_theme_root() . "/autos/vendor/autodealer/{$oid->catalog}/api.php" );
    $api = new \NIS();
    $subgroups = array();
    return $api->getNisGroup($oid->market, $oid->model, $oid->modification, $oid->group)->aGroup;
}

function nissan_models() {
    $oid = new \StdClass();
    $oid = isset($_POST['oid']) ? (object) $_POST['oid'] : '';

    $markets = tlc_transient( "{$oid->catalog}-{$oid->mark}" )
            ->expires_in( 0 )
            ->updates_with( __NAMESPACE__ . '\api_nissan_models', array( $oid ) )
            ->get();
    if ( ! $markets ) $markets = api_nissan_models($oid);

    $data = [
        'items' => $markets,
        'oid' => $oid
    ];
    wp_send_json_success($data);
}
function nissan_modifications() {
    $oid = new \StdClass();
    $oid = isset($_POST['oid']) ? (object) $_POST['oid'] : '';

    $key = "{$oid->catalog}-{$oid->mark}-{$oid->model}-{$oid->market}";
    $items = tlc_transient( $key )
            ->expires_in( 0 )
            ->updates_with( __NAMESPACE__ . '\api_nissan_modifications', array( $oid ) )
            ->get();
    if ( ! $items ) $items = api_nissan_modifications($oid);

    $data = [
        'oid' => $oid,
        'items' => $items
    ];

    wp_send_json_success( $data );
}
function nissan_groups() {
    $oid = new \StdClass();
    $oid = isset($_POST['oid']) ? (object) $_POST['oid'] : '';

    $key = "{$oid->catalog}-{$oid->mark}-{$oid->model}-{$oid->market}-{$oid->modification}";
    $items = tlc_transient( $key )
            ->expires_in( 0 )
            ->updates_with( __NAMESPACE__ . '\api_nissan_groups', array( $oid ) )
            ->get();
    if ( ! $items ) $items = api_nissan_groups($oid);

    $data = [
        'oid' => $oid,
        'items' => $items
    ];

    wp_send_json_success( $data );
}
function nissan_subgroups() {
    $oid = new \StdClass();
    $oid = isset($_POST['oid']) ? (object) $_POST['oid'] : '';

    $key = "{$oid->catalog}-{$oid->mark}-{$oid->model}-{$oid->market}-{$oid->modification}-{$oid->group}";
    $items = tlc_transient( $key )
            ->expires_in( 0 )
            ->updates_with( __NAMESPACE__ . '\api_nissan_subgroups', array( $oid ) )
            ->get();
    if ( ! $items ) $items = api_nissan_subgroups($oid);

    $data = [
        'oid' => $oid,
        'items' => $items,
        'url' => "/illustration/?cat=nissan&market={$oid->market}&model={$oid->model}&modification={$oid->modification}&group={$oid->group}"
    ];

    wp_send_json_success( $data );
}

/* Toyota */

// Models
add_action('wp_ajax_toyota_models', __NAMESPACE__ . '\toyota_models');
add_action('wp_ajax_nopriv_toyota_models', __NAMESPACE__ . '\toyota_models');
// Options
add_action('wp_ajax_toyota_options', __NAMESPACE__ . '\toyota_options');
add_action('wp_ajax_nopriv_toyota_options', __NAMESPACE__ . '\toyota_options');
// Groups
add_action('wp_ajax_toyota_groups', __NAMESPACE__ . '\toyota_groups');
add_action('wp_ajax_nopriv_toyota_groups', __NAMESPACE__ . '\toyota_groups');

function api_toyota_models($oid) {
    include( get_theme_root() . "/autos/vendor/autodealer/{$oid->catalog}/api.php" );
    $api = new \TOY();

    $markets = array();
    foreach ($api->getToyMarkets() as $key => $value) {
        $markets[$key] = new \StdClass;
        $markets[$key]->name = $value;
        $markets[$key]->models = $api->getToyModels($oid->mark, $key)->aModels;
    }

    return $markets;
}
function api_toyota_options($oid) {
    include( get_theme_root() . "/autos/vendor/autodealer/{$oid->catalog}/api.php" );
    $api = new \TOY();
    return $api->getToyModiff($oid->market, $oid->model)->aModif;
}
function api_toyota_groups($oid) {
    include( get_theme_root() . "/autos/vendor/autodealer/{$oid->catalog}/api.php" );
    $api = new \TOY();
    $response = $api->getToyModCompl($oid->market, $oid->model, $oid->compl, $oid->option, $oid->code, $oid->vin, $oid->vdate, $oid->siyopt)->aCompl;
    return $response;
}

function toyota_models() {
    $oid = new \StdClass();
    $oid = isset($_POST['oid']) ? (object) $_POST['oid'] : '';

    $markets = tlc_transient( "{$oid->catalog}-{$oid->mark}" )
            ->expires_in( 0 )
            ->updates_with( __NAMESPACE__ . '\api_toyota_models', array( $oid ) )
            ->get();
    if ( ! $markets ) $markets = api_toyota_models($oid);

    $data = [
        'items' => $markets,
        'oid' => $oid
    ];
    wp_send_json_success($data);
}
function toyota_options() {
    $oid = new \StdClass();
    $oid = isset($_POST['oid']) ? (object) $_POST['oid'] : '';

    $key = "{$oid->catalog}-{$oid->mark}-{$oid->model}-{$oid->market}";
    $items = tlc_transient( $key )
            ->expires_in( 0 )
            ->updates_with( __NAMESPACE__ . '\api_toyota_options', array( $oid ) )
            ->get();
    if ( ! $items ) $items = api_toyota_options($oid);

    $data = [
        'oid' => $oid,
        'items' => $items
    ];

    wp_send_json_success( $data );
}
function toyota_groups() {
    $oid = new \StdClass();
    $oid = isset($_POST['oid']) ? (object) $_POST['oid'] : '';
    // дополнительные данные
    $oid->vin = ''; // isset($_GET['vin']) ? $_GET['vin'] : '';
    $oid->vdate = ''; // isset($_GET['vdate']) ? $_GET['vdate'] : '';
    $oid->siyopt = ''; // isset($_GET['siyopt']) ? $_GET['siyopt'] : '';
    // $key = "{$oid->catalog}-{$oid->mark}-{$oid->model}-{$oid->market}-{$oid->compl}-{$oid->sysopt}-{$oid->code}";
    // $items = tlc_transient( $key )
    //         ->expires_in( 0 )
    //         ->updates_with( __NAMESPACE__ . '\api_toyota_groups', array( $oid ) )
    //         ->get();
    // if ( ! $items )
    $items = api_toyota_groups($oid);

    $url = "/illustration/?cat=toyota&mark={$oid->mark}&market={$oid->market}&model={$oid->model}&compl={$oid->compl}&option={$oid->option}&code={$oid->code}";
    // дополнительный url
    $car->getString = ""
        . ( ( $oid->vin )   ? "&vin={$oid->vin}" : "" )
        . ( ( $oid->vdate ) ? "&vdate={$oid->vdate}" : "" )
        . ( ( $oid->siyopt )? "&siyopt={$oid->siyopt}" : "" );

    $data = [
        'oid' => $oid,
        'items' => $items,
        'url' => $url
    ];

    wp_send_json_success( $data );
}

/* ADC */

// Models
add_action('wp_ajax_adc_models', __NAMESPACE__ . '\adc_models');
add_action('wp_ajax_nopriv_adc_models', __NAMESPACE__ . '\adc_models');
// Tree
add_action('wp_ajax_adc_tree', __NAMESPACE__ . '\adc_tree');
add_action('wp_ajax_nopriv_adc_tree', __NAMESPACE__ . '\adc_tree');

function api_adc_models($oid) {
    include( get_theme_root() . "/autos/vendor/autodealer/{$oid->catalog}/api.php" );
    $api = new \ADC();
    $models = array();
    foreach ($api->getModelList($oid->mark, $oid->type)->models as $value) {
        $models[] = $value;
    }
    return $models;
}
function api_adc_tree($oid) {
    include( get_theme_root() . "/autos/vendor/autodealer/{$oid->catalog}/api.php" );
    $api = new \ADC();

    $response = $api->getTreeList($oid->model, false);
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
    $tree = array_filter($response->details, function($item) { return isset($item->ready); });
    return current($tree)->childrens;
}

function adc_models() {
    $oid = new \StdClass();
    $oid = isset($_POST['oid']) ? (object) $_POST['oid'] : '';

    $items = tlc_transient( "{$oid->catalog}-{$oid->mark}" )
            ->expires_in( 0 )
            ->updates_with( __NAMESPACE__ . '\api_adc_models', array( $oid ) )
            ->get();
    if ( ! $items ) $items = api_adc_models($oid);

    $data = [
        'items' => $items,
        'oid' => $oid
    ];
    wp_send_json_success($data);
}
function adc_tree() {
    $oid = new \StdClass();
    $oid = isset($_POST['oid']) ? (object) $_POST['oid'] : '';

    $key = "{$oid->catalog}-{$oid->mark}-{$oid->model}";
    $items = tlc_transient( $key )
            ->expires_in( 0 )
            ->updates_with( __NAMESPACE__ . '\api_adc_options', array( $oid ) )
            ->get();
    if ( ! $items ) $items = api_adc_tree($oid);
    $url = "/illustration/?cat=adc&model={$oid->model}";

    $data = [
        'oid' => $oid,
        'items' => $items,
        'url' => $url
    ];

    wp_send_json_success( $data );
}

/* TD */

// Models
add_action('wp_ajax_td_models', __NAMESPACE__ . '\td_models');
add_action('wp_ajax_nopriv_td_models', __NAMESPACE__ . '\td_models');
// Equipments
add_action('wp_ajax_td_equipments', __NAMESPACE__ . '\td_equipments');
add_action('wp_ajax_nopriv_td_equipments', __NAMESPACE__ . '\td_equipments');
// Tree
add_action('wp_ajax_td_tree', __NAMESPACE__ . '\td_tree');
add_action('wp_ajax_nopriv_td_tree', __NAMESPACE__ . '\td_tree');
// Details
add_action('wp_ajax_td_details', __NAMESPACE__ . '\td_details');
add_action('wp_ajax_nopriv_td_details', __NAMESPACE__ . '\td_details');

function api_td_models($oid) {
    include( get_theme_root() . "/autos/vendor/autodealer/{$oid->catalog}/api.php" );
    $api = new \TD();
    return $api->getTDModels('pc', $oid->mark)->models;
}
function api_td_equipments($oid) {
    include( get_theme_root() . "/autos/vendor/autodealer/{$oid->catalog}/api.php" );
    $api = new \TD();
    return $api->getTDCompl('pc', $oid->mark, $oid->model)->compl;
}
function api_td_tree($oid) {
    include( get_theme_root() . "/autos/vendor/autodealer/{$oid->catalog}/api.php" );
    $api = new \TD();
    $response = $api->getTDTree('pc', $oid->mark, $oid->model, $oid->equipment);

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
    $tree = array();
    foreach (array_filter($response->tree, function($item) { return isset($item->ready); }) as $value) {
        $tree[] = $value;
    }
    return $tree;
}
function api_td_details($oid) {
    include( get_theme_root() . "/autos/vendor/autodealer/{$oid->catalog}/api.php" );
    $api = new \TD();
    return $api->getTDDetails('pc', $oid->mark, $oid->model, $oid->equipment, $oid->tree)->details;
}

function td_models() {
    $oid = new \StdClass();
    $oid = isset($_POST['oid']) ? (object) $_POST['oid'] : '';

    $items = tlc_transient( "{$oid->catalog}-{$oid->mark}" )
            ->expires_in( 0 )
            ->updates_with( __NAMESPACE__ . '\api_td_models', array( $oid ) )
            ->get();
    if ( ! $items ) $items = api_td_models($oid);

    $data = [
        'items' => $items,
        'oid' => $oid
    ];
    wp_send_json_success($data);
}
function td_equipments() {
    $oid = new \StdClass();
    $oid = isset($_POST['oid']) ? (object) $_POST['oid'] : '';

    $key = "{$oid->catalog}-{$oid->mark}-{$oid->model}";
    $items = tlc_transient( $key )
            ->expires_in( 0 )
            ->updates_with( __NAMESPACE__ . '\api_td_equipments', array( $oid ) )
            ->get();
    if ( ! $items ) $items = api_td_equipments($oid);

    $data = [
        'oid' => $oid,
        'items' => $items,
    ];

    wp_send_json_success( $data );
}
function td_tree() {
    $oid = new \StdClass();
    $oid = isset($_POST['oid']) ? (object) $_POST['oid'] : '';

    $key = "{$oid->catalog}-{$oid->mark}-{$oid->model}-{$oid->equipment}";
    $items = tlc_transient( $key )
            ->expires_in( 0 )
            ->updates_with( __NAMESPACE__ . '\api_td_tree', array( $oid ) )
            ->get();
    if ( ! $items ) $items = api_td_tree($oid);

    $data = [
        'oid' => $oid,
        'items' => $items,
    ];

    wp_send_json_success( $data );
}
function td_details() {
    $oid = new \StdClass();
    $oid = isset($_POST['oid']) ? (object) $_POST['oid'] : '';

    // $key = "{$oid->catalog}-{$oid->mark}-{$oid->model}-{$oid->equipment}-{$oid->tree}";
    // $items = tlc_transient( $key )
    //         ->expires_in( 0 )
    //         ->updates_with( __NAMESPACE__ . '\api_td_details', array( $oid ) )
    //         ->get();
    // if ( ! $items )
    $items = api_td_details($oid);
    // $url = "/illustration/?cat=adc&model={$oid->model}";

    $data = [
        'oid' => $oid,
        'items' => $items,
        // 'url' => $url
    ];

    wp_send_json_success( $data );
}
