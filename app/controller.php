<?php

namespace App;

add_filter('sage/template/marks/data', function($data) {
    include( get_theme_root() . '/autos/vendor/autodealer/_lib.php' );
    include( get_theme_root() . '/autos/vendor/autodealer/adc/api.php' );
    include( get_theme_root() . "/autos/vendor/autodealer/td/api.php" );

    $ADC = new \ADC();
    $TD = new \TD();
    /// Раскомментировав строку нижу, можно посмотреть что вернул сервер
    // $ADC->e($oMarkList);

    $marksOriginal = array_filter($ADC->getMarkList(9)->marks, function($mark) {
        return (1 == $mark->flags) && (1000 != $mark->mark_id);
    });
    $marksAftermarket = $TD->getTDMarks('pc')->marks;

    $marks = array();
    array_walk($marksOriginal, function ($value) use (&$marks) {
        if ( $value->external ) {
            $arr = explode('/',$value->route);
            $mark = end( $arr );
            $action = "main";
            $iface = NULL;
            $var  = 'mark';   /// Передаваемая переменная
            if ( in_array($mark,['bmw','mini','moto','rr']) ){
                $iface  = 'bmw';    /// Директория со скриптами
                $action = 'series'; /// Входная точка
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

            $url = "/models/?cat=$iface&{$var}={$mark}";
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

    $data = [
        'marks' => $marks
    ];

    return $data;
});
add_filter('sage/template/models/data', function($data) {

    // Общее
    $catalog = isset($_GET['cat']) ? $_GET['cat'] : '';
    // Оригиналы
    $oid = new \StdClass();
    $oid->mark = isset($_GET['mark']) ? $_GET['mark'] : '';
    // Aftermarket
    $id = new \StdClass();
    $id->type = isset($_GET['type']) ? $_GET['type'] : '';
    $id->mark = isset($_GET['mark']) ? $_GET['mark'] : '';
    // Выходные объекты
    $car = new \StdClass();

    /** Обязательно к применению */
    include( get_theme_root() . '/autos/vendor/autodealer/_lib.php' );
    include( get_theme_root() . "/autos/vendor/autodealer/{$catalog}/api.php" );

    switch ($catalog) {
        case 'fiat':
            $api = new \Fiat();
            $car = new \StdClass();
            $response = $api->getFIATModels($oid->mark);
            $car->models = $response->models;
            break;
        # Региональность - Audi, Volkswagen, Seat, Skoda
        case 'etka':
            $api = new \ETKA();
            $response = $api->getETKAMarkets($oid->mark);
            $car->markets = $response->markets;
            $car->models = array();
            foreach ($car->markets as $value)
                $car->models[$value->code] = $api->getETKAModels($oid->mark, $value->code)->models;
            break;
        # Серии
        case 'bmw':
            $api = new \BMW();
            $models = $api->getBMWCatalogs($mark)->vt;
            break;
        # KIA, Hyundai - не работает
        case 'mcct':
            $api = new \MCCT();
            $models = $api->getMcctIndex('', '');
            break;
        # Региональность - Nissan.Infiniti
        case 'nissan':
            $api = new \NIS();
            // $markets = $api->getNisMarkets($mark);
            $markets = array( 'jp' => '', 'gl' => '', 'gr' => '' );
            $models = array();
            foreach ($markets as $key => $value)
                $models = array_merge($models, $api->getNisModels($mark, $key)->aModels);
            break;
        # Региональность - Toyota, Lexus
        case 'toyota':
            $api = new \TOY();
            $markets = $api->getToyMarkets();
            $models = array();
            foreach ($markets as $key => $value)
                $models = array_merge($models, $api->getToyModels($mark, $key)->aModels);
            break;
        # ADC
        case 'adc':
            $api = new \ADC();
            $response = $api->getModelList($markID, $typeID);
            $brand = $response->markName;
            $models = $response->models;
            break;
        case 'td':
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
add_filter('sage/template/productions/data', function($data) {
    // Общее
    $catalog = isset($_GET['cat']) ? $_GET['cat'] : '';

    // Оригиналы
    $oid = new \StdClass();
    $oid->mark = isset($_GET['mark']) ? $_GET['mark'] : '';
    $oid->model = isset($_GET['model']) ? $_GET['model'] : '';
    $oid->market = isset($_GET['market']) ? $_GET['market'] : '';
    $oid->dir = isset($_GET['dir']) ? $_GET['dir'] : 'R';
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
            $api = new \ETKA();
            $response = $api->getETKAProduction($oid->mark, $oid->market, $oid->model);
            $car->productions = $response->prod;
            break;
        # Серии
        case 'bmw':
            $api = new \BMW();
            $models = $api->getBMWCatalogs($mark)->vt;
            break;
        # KIA, Hyundai - не работает
        case 'mcct':
            $api = new \MCCT();
            $models = $api->getMcctIndex('', '');
            break;
        # Региональность - Nissan.Infiniti
        case 'nissan':
            $api = new \NIS();
            // $markets = $api->getNisMarkets($mark);
            $markets = array( 'jp' => '', 'gl' => '', 'gr' => '' );
            $models = array();
            foreach ($markets as $key => $value)
                $models = array_merge($models, $api->getNisModels($mark, $key)->aModels);
            break;
        # Региональность - Toyota, Lexus
        case 'toyota':
            $api = new \TOY();
            $markets = $api->getToyMarkets();
            $models = array();
            foreach ($markets as $key => $value)
                $models = array_merge($models, $api->getToyModels($mark, $key)->aModels);
            break;
        # ADC
        case 'adc':
            $api = new \ADC();
            $response = $api->getModelList($markID, $typeID);
            $brand = $response->markName;
            $models = $response->models;
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
    // Общее
    $catalog = isset($_GET['cat']) ? $_GET['cat'] : '';

    // Оригиналы
    $oid = new \StdClass();
    $oid->mark = isset($_GET['mark']) ? $_GET['mark'] : '';
    $oid->model = isset($_GET['model']) ? $_GET['model'] : '';
    $oid->market = isset($_GET['market']) ? $_GET['market'] : '';
    $oid->production = isset($_GET['production']) ? $_GET['production'] : '';
    $oid->production_year = isset($_GET['production_year']) ? $_GET['production_year'] : '';
    $oid->dir = isset($_GET['dir']) ? $_GET['dir'] : '';
    // Выходные объекты
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
        # Региональность - Audi, Volkswagen, Seat, Skoda
        case 'etka':
            $api = new \ETKA();
            $response = $api->getETKAGroups($oid->mark, $oid->market, $oid->model, $oid->production_year, $oid->production, $oid->dir);
            $car->groups = $response->hg;
            $car->image = get_stylesheet_directory_uri() . '/vendor/autodealer/media/images/etka/groups/';
            break;
        # Серии
        case 'bmw':
            $api = new \BMW();
            $models = $api->getBMWCatalogs($mark)->vt;
            break;
        # KIA, Hyundai - не работает
        case 'mcct':
            $api = new \MCCT();
            $models = $api->getMcctIndex('', '');
            break;
        # Региональность - Nissan.Infiniti
        case 'nissan':
            $api = new \NIS();
            // $markets = $api->getNisMarkets($mark);
            $markets = array( 'jp' => '', 'gl' => '', 'gr' => '' );
            $models = array();
            foreach ($markets as $key => $value)
                $models = array_merge($models, $api->getNisModels($mark, $key)->aModels);
            break;
        # Региональность - Toyota, Lexus
        case 'toyota':
            $api = new \TOY();
            $markets = $api->getToyMarkets();
            $models = array();
            foreach ($markets as $key => $value)
                $models = array_merge($models, $api->getToyModels($mark, $key)->aModels);
            break;
        # ADC
        case 'adc':
            $api = new \ADC();
            $response = $api->getModelList($markID, $typeID);
            $brand = $response->markName;
            $models = $response->models;
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
    // Общее
    $catalog = isset($_GET['cat']) ? $_GET['cat'] : '';

    // Оригиналы
    $oid = new \StdClass();
    $oid->mark = isset($_GET['mark']) ? $_GET['mark'] : '';
    $oid->model = isset($_GET['model']) ? $_GET['model'] : '';
    $oid->market = isset($_GET['market']) ? $_GET['market'] : '';
    $oid->production = isset($_GET['production']) ? $_GET['production'] : '';
    $oid->production_year = isset($_GET['production_year']) ? $_GET['production_year'] : '';
    $oid->group = isset($_GET['group']) ? $_GET['group'] : '';
    $oid->dir = isset($_GET['dir']) ? $_GET['dir'] : 'R';
    // Выходные объекты
    $car = new \StdClass();

    // Обязательно к применению
    include( get_theme_root() . '/autos/vendor/autodealer/_lib.php' );
    include( get_theme_root() . "/autos/vendor/autodealer/{$catalog}/api.php" );

    switch ($catalog) {
        case 'fiat':
            $api = new \Fiat();
            $response = $api->getFIATSubGroup($oid->mark, $oid->model, $oid->production, $oid->group);
            $car->subgroups = $response->subGroups;
            break;
        # Региональность - Audi, Volkswagen, Seat, Skoda
        case 'etka':
            $api = new \ETKA();
            $response = $api->getETKASubGroups($oid->mark, $oid->market, $oid->model, $oid->production_year, $oid->production, $oid->dir, $oid->group);
            $car->subgroups = array_filter($response->ug, function($value) {
                return $value->ou != 'O';
            });
            break;
        # Серии
        case 'bmw':
            $api = new \BMW();
            $models = $api->getBMWCatalogs($mark)->vt;
            break;
        # KIA, Hyundai - не работает
        case 'mcct':
            $api = new \MCCT();
            $models = $api->getMcctIndex('', '');
            break;
        # Региональность - Nissan.Infiniti
        case 'nissan':
            $api = new \NIS();
            // $markets = $api->getNisMarkets($mark);
            $markets = array( 'jp' => '', 'gl' => '', 'gr' => '' );
            $models = array();
            foreach ($markets as $key => $value)
                $models = array_merge($models, $api->getNisModels($mark, $key)->aModels);
            break;
        # Региональность - Toyota, Lexus
        case 'toyota':
            $api = new \TOY();
            $markets = $api->getToyMarkets();
            $models = array();
            foreach ($markets as $key => $value)
                $models = array_merge($models, $api->getToyModels($mark, $key)->aModels);
            break;
        # ADC
        case 'adc':
            $api = new \ADC();
            $response = $api->getModelList($markID, $typeID);
            $brand = $response->markName;
            $models = $response->models;
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
    // Общее
    $catalog = isset($_GET['cat']) ? $_GET['cat'] : '';

    // Оригиналы
    $oid = new \StdClass();
    $oid->mark = isset($_GET['mark']) ? $_GET['mark'] : '';
    $oid->model = isset($_GET['model']) ? $_GET['model'] : '';
    $oid->market = isset($_GET['market']) ? $_GET['market'] : '';
    $oid->production = isset($_GET['production']) ? $_GET['production'] : '';
    $oid->production_year = isset($_GET['production_year']) ? $_GET['production_year'] : '';
    $oid->group = isset($_GET['group']) ? $_GET['group'] : '';
    $oid->subgroup = isset($_GET['subgroup']) ? $_GET['subgroup'] : '';
    $oid->table = isset($_GET['table']) ? urldecode(base64_decode($_GET['table'])) : '';
    $oid->dir = isset($_GET['dir']) ? $_GET['dir'] : 'R';
    $oid->image = isset($_GET['image']) ? $_GET['image'] : '';
    // Выходные объекты
    $car = new \StdClass();
    $car->image = new \StdClass();

    // Обязательно к применению
    include( get_theme_root() . '/autos/vendor/autodealer/_lib.php' );
    include( get_theme_root() . "/autos/vendor/autodealer/{$catalog}/api.php" );

    switch ($catalog) {
        case 'fiat':
            $api = new \Fiat();
            $response = $api->getFIATPartDrawData($oid->production, $oid->group, $oid->subgroup, $oid->table);
            $variant = $response->partDrawData->variants[0]->variante;
            $car->parts = $response;
            $response = $api->getFIATDraw($oid->mark, $oid->model, $oid->production, $oid->group, $oid->subgroup, $oid->table, $variant, 0.5);
            $car->draw = $response;
            break;
        # Региональность - Audi, Volkswagen, Seat, Skoda
        case 'etka':
            $api = new \ETKA();
            $response = $api->getETKAIllustration($oid->mark, $oid->market, $oid->model, $oid->production_year, $oid->production, $oid->dir, $oid->group, $oid->subgroup, $oid->image, 0.5);
            $car->image = $response->imgInfo;
            $car->zoom = 'illustration';
            $car->jump = "/illustration/?cat={ $catalog }&mark={ $oid->mark }&market={ $oid->market }&model={ $oid->model }&production_year={ $oid->production_year }&production={ $oid->production }";
            echo '<pre>';
            var_dump($car->image);
            echo '</pre>';
            break;
        # Серии
        case 'bmw':
            $api = new \BMW();
            $models = $api->getBMWCatalogs($mark)->vt;
            break;
        # KIA, Hyundai - не работает
        case 'mcct':
            $api = new \MCCT();
            $models = $api->getMcctIndex('', '');
            break;
        # Региональность - Nissan.Infiniti
        case 'nissan':
            $api = new \NIS();
            // $markets = $api->getNisMarkets($mark);
            $markets = array( 'jp' => '', 'gl' => '', 'gr' => '' );
            $models = array();
            foreach ($markets as $key => $value)
                $models = array_merge($models, $api->getNisModels($mark, $key)->aModels);
            break;
        # Региональность - Toyota, Lexus
        case 'toyota':
            $api = new \TOY();
            $markets = $api->getToyMarkets();
            $models = array();
            foreach ($markets as $key => $value)
                $models = array_merge($models, $api->getToyModels($mark, $key)->aModels);
            break;
        # ADC
        case 'adc':
            $api = new \ADC();
            $response = $api->getModelList($markID, $typeID);
            $brand = $response->markName;
            $models = $response->models;
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
    $brand = isset($_GET['brand']) ? $_GET['brand'] : '';
    $type = isset($_GET['type']) ? $_GET['type'] : '';

    $mark = isset($_GET['mark']) ? $_GET['mark'] : '';
    $typeID = isset($_GET['typeID']) ? $_GET['typeID'] : '';
    $markID = isset($_GET['markID']) ? $_GET['markID'] : '';
    $modelID = isset($_GET['modelID']) ? $_GET['modelID'] : '';
    $flag = isset($_GET['flag']) ? $_GET['flag'] : '';

    /** Обязательно к применению */
    include( get_theme_root() . '/autos/vendor/autodealer/_lib.php' );
    include( get_theme_root() . "/autos/vendor/autodealer/{$catalog}/api.php" );

    switch ($catalog) {
        case 'fiat':
            $car = new \Fiat();
            $models = $car->getFIATModels($brand)->models;
            break;
        # Региональность - Audi, Volkswagen, Seat, Skoda
        case 'etka':
            $car = new \ETKA();
            $markets = $car->getETKAMarkets($mark)->markets;
            $models = array();
            foreach ($markets as $key => $value)
                $models = array_merge($models, $car->getETKAModels($mark, $key)->models);
            break;
        # Серии
        case 'bmw':
            $car = new \BMW();
            $models = $car->getBMWCatalogs($mark)->vt;
            break;
        # KIA, Hyundai - не работает
        case 'mcct':
            $car = new \MCCT();
            $models = $car->getMcctIndex('', '');
            break;
        # Региональность - Nissan.Infiniti
        case 'nissan':
            $car = new \NIS();
            // $markets = $car->getNisMarkets($mark);
            $markets = array( 'jp' => '', 'gl' => '', 'gr' => '' );
            $models = array();
            foreach ($markets as $key => $value)
                $models = array_merge($models, $car->getNisModels($mark, $key)->aModels);
            break;
        # Региональность - Toyota, Lexus
        case 'toyota':
            $car = new \TOY();
            $markets = $car->getToyMarkets();
            $models = array();
            foreach ($markets as $key => $value)
                $models = array_merge($models, $car->getToyModels($mark, $key)->aModels);
            break;
        # ADC
        case 'adc':
            $car = new \ADC();
            $response = $car->getModelList($markID, $typeID);
            $brand = $response->markName;
            $models = $response->models;
            break;
        case 'td':
            $car = new \TD();
            $response = $car->getTDCompl('pc', $markID, $modelID);
            $items = $response->compl;
            $carInfo = $response->modelInfo;
            break;
        default:
            echo 'Error';
            break;
    }

    $data = [
        'brand' => $brand,
        'catalog' => $catalog,
        'carInfo' => $carInfo,
        'markID' => $markID,
        'modelID' => $modelID,
        'items' => $items
    ];

    return $data;
});
add_filter('sage/template/tree/data', function($data) {

    $catalog = isset($_GET['cat']) ? $_GET['cat'] : '';
    $brand = isset($_GET['brand']) ? $_GET['brand'] : '';
    $type = isset($_GET['type']) ? $_GET['type'] : '';
    $mark = isset($_GET['mark']) ? $_GET['mark'] : '';
    $flag = isset($_GET['flag']) ? $_GET['flag'] : '';

    $typeID = isset($_GET['typeID']) ? $_GET['typeID'] : '';
    $markID = isset($_GET['markID']) ? $_GET['markID'] : '';
    $modelID = isset($_GET['modelID']) ? $_GET['modelID'] : '';
    $equipmentID = isset($_GET['equipmentID']) ? $_GET['equipmentID'] : '';

    /** Обязательно к применению */
    include( get_theme_root() . '/autos/vendor/autodealer/_lib.php' );
    include( get_theme_root() . "/autos/vendor/autodealer/{$catalog}/api.php" );

    switch ($catalog) {
        case 'fiat':
            $car = new \Fiat();
            $models = $car->getFIATModels($brand)->models;
            break;
        # Региональность - Audi, Volkswagen, Seat, Skoda
        case 'etka':
            $car = new \ETKA();
            $markets = $car->getETKAMarkets($mark)->markets;
            $models = array();
            foreach ($markets as $key => $value)
                $models = array_merge($models, $car->getETKAModels($mark, $key)->models);
            break;
        # Серии
        case 'bmw':
            $car = new \BMW();
            $models = $car->getBMWCatalogs($mark)->vt;
            break;
        # KIA, Hyundai - не работает
        case 'mcct':
            $car = new \MCCT();
            $models = $car->getMcctIndex('', '');
            break;
        # Региональность - Nissan.Infiniti
        case 'nissan':
            $car = new \NIS();
            // $markets = $car->getNisMarkets($mark);
            $markets = array( 'jp' => '', 'gl' => '', 'gr' => '' );
            $models = array();
            foreach ($markets as $key => $value)
                $models = array_merge($models, $car->getNisModels($mark, $key)->aModels);
            break;
        # Региональность - Toyota, Lexus
        case 'toyota':
            $car = new \TOY();
            $markets = $car->getToyMarkets();
            $models = array();
            foreach ($markets as $key => $value)
                $models = array_merge($models, $car->getToyModels($mark, $key)->aModels);
            break;
        # ADC
        case 'adc':
            $car = new \ADC();
            $response = $car->getModelList($markID, $typeID);
            $brand = $response->markName;
            $models = $response->models;
            break;
        case 'td':
            $car = new \TD();
            $response = $car->getTDTree('pc', $markID, $modelID, $equipmentID);

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
            $items = array_filter($response->tree, function($item) { return isset($item->ready); });
            break;
        default:
            echo 'Error';
            break;
    }

    $data = [
        'brand' => $brand,
        'catalog' => $catalog,
        'carInfo' => $carInfo,
        'markID' => $markID,
        'modelID' => $modelID,
        'equipmentID' => $equipmentID,
        'items' => $items
    ];

    return $data;
});
add_filter('sage/template/details/data', function($data) {

    $catalog = isset($_GET['cat']) ? $_GET['cat'] : '';
    $brand = isset($_GET['brand']) ? $_GET['brand'] : '';
    $type = isset($_GET['type']) ? $_GET['type'] : '';

    $mark = isset($_GET['mark']) ? $_GET['mark'] : '';
    $typeID = isset($_GET['typeID']) ? $_GET['typeID'] : '';
    $markID = isset($_GET['markID']) ? $_GET['markID'] : '';
    $modelID = isset($_GET['modelID']) ? $_GET['modelID'] : '';
    $equipmentID = isset($_GET['equipmentID']) ? $_GET['equipmentID'] : '';
    $treeID = isset($_GET['treeID']) ? $_GET['treeID'] : '';
    $flag = isset($_GET['flag']) ? $_GET['flag'] : '';

    /** Обязательно к применению */
    include( get_theme_root() . '/autos/vendor/autodealer/_lib.php' );
    include( get_theme_root() . "/autos/vendor/autodealer/{$catalog}/api.php" );

    switch ($catalog) {
        case 'fiat':
            $car = new \Fiat();
            $models = $car->getFIATModels($brand)->models;
            break;
        # Региональность - Audi, Volkswagen, Seat, Skoda
        case 'etka':
            $car = new \ETKA();
            $markets = $car->getETKAMarkets($mark)->markets;
            $models = array();
            foreach ($markets as $key => $value)
                $models = array_merge($models, $car->getETKAModels($mark, $key)->models);
            break;
        # Серии
        case 'bmw':
            $car = new \BMW();
            $models = $car->getBMWCatalogs($mark)->vt;
            break;
        # KIA, Hyundai - не работает
        case 'mcct':
            $car = new \MCCT();
            $models = $car->getMcctIndex('', '');
            break;
        # Региональность - Nissan.Infiniti
        case 'nissan':
            $car = new \NIS();
            // $markets = $car->getNisMarkets($mark);
            $markets = array( 'jp' => '', 'gl' => '', 'gr' => '' );
            $models = array();
            foreach ($markets as $key => $value)
                $models = array_merge($models, $car->getNisModels($mark, $key)->aModels);
            break;
        # Региональность - Toyota, Lexus
        case 'toyota':
            $car = new \TOY();
            $markets = $car->getToyMarkets();
            $models = array();
            foreach ($markets as $key => $value)
                $models = array_merge($models, $car->getToyModels($mark, $key)->aModels);
            break;
        # ADC
        case 'adc':
            $car = new \ADC();
            $response = $car->getModelList($markID, $typeID);
            $brand = $response->markName;
            $models = $response->models;
            break;
        case 'td':
            $api = new \TD();
            $car = new \StdClass();
            $response = $api->getTDDetails('pc', $markID, $modelID, $equipmentID, $treeID);
            $car->info = $response->modelInfo;
            $car->group = $response->group;
            $car->details = $response->details;
            break;
        default:
            echo 'Error';
            break;
    }

    $data = [
        'brand' => $brand,
        'car' => $car,
        'catalog' => $catalog,
        'markID' => $markID,
        'modelID' => $modelID,
        'treeID' => $treeID,
    ];

    return $data;
});
