<?php
if (!empty($_GET) && !empty($_GET['search'])) {
    include "form-finded.php";
    $search = $_GET['search'];
    $zone = '37.620393,55.75396'; // Москва
    $yandex = 'https://geocode-maps.yandex.ru/1.x/?format=json&geocode=' . $search . '&ll=' . $zone;
    /**
     * Типы результатов API
     */
    $kinds = [
        'house' => 'Дом', 'street' => 'Улица', 'metro' => 'Станция метро', 'district' => 'Район города',
        'locality' => 'Населённый пункт', 'area' => 'Район области', 'province' => 'Область',
        'country' => 'Страна', 'hydro' => 'Река / озеро / ручей / водохранилище', 'railway' => 'ж.д. станция',
        'route' => 'Линия метро / шоссе / ж.д. линия', 'vegetation' => 'Лес / парк / сад', 'airport' => 'Аэропорт',
        'other' => 'Прочее',
    ];
    /**
     * точность соответствия между номером найденного дома и номером дома из запроса.
     */
    $precisions = [
        'exact' => 'Точное соответствие.', 'number' => 'Совпал номер дома, но не совпало строение или корпус.',
        'near' => 'Найден дом с номером, близким к запрошенному.', 'range' => 'Приблизительные координаты запрашиваемого дома.',
        'street' => 'Найдена только улица.', 'other' => 'Улица не найдена.'
    ];

    /**
     * CURL запрос к Яндекс.API
     */
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $yandex);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    /**
     * Получили данные от API
     */
    $response = curl_exec($ch);
    if (!empty($response)) {
        $json = json_decode($response);
        $data = (object)[
            'request' => $json->response->GeoObjectCollection->metaDataProperty->GeocoderResponseMetaData->request,
            'true_request' => $json->response->GeoObjectCollection->metaDataProperty->GeocoderResponseMetaData->suggest,
            'results' => $json->response->GeoObjectCollection->featureMember
        ];
        if (!empty($data->results)) {
            if (!empty($data->true_request)) {
                echo "<h2 class='header'>Найдено : <strong>" . count($data->results) . "</strong> результатов по запросу \"" . $data->true_request . "\" <span style='color:red;'>вместо введенного</span> \"" . $data->request . "\"</h2>";
            } else {
                echo "<h2 class='header'>Найдено : <strong>" . count($data->results) . "</strong> результатов по запросу \"" . $data->request . "\"</h2>";
            }

            $index = 1;
            foreach ($data->results as $item) {
                $result = (object)[
                    'kind' => $item->GeoObject->metaDataProperty->GeocoderMetaData->kind,
                    'text' => $item->GeoObject->metaDataProperty->GeocoderMetaData->text,
                    'precision' => $item->GeoObject->metaDataProperty->GeocoderMetaData->precision,
                    'country' =>
                        !empty($item->GeoObject->metaDataProperty->GeocoderMetaData->AddressDetails->Country)
                            ? $item->GeoObject->metaDataProperty->GeocoderMetaData->AddressDetails->Country->CountryName
                            : '',
                    'address' =>
                        !empty($item->GeoObject->metaDataProperty->GeocoderMetaData->AddressDetails->Country->AddressLine)
                            ? $item->GeoObject->metaDataProperty->GeocoderMetaData->AddressDetails->Country->AddressLine
                            : '',
                    'area' =>
                        !empty($item->GeoObject->metaDataProperty->GeocoderMetaData->AddressDetails->Country->AdministrativeArea)
                            ? $item->GeoObject->metaDataProperty->GeocoderMetaData->AddressDetails->Country->AdministrativeArea->AdministrativeAreaName
                            : '',
                    'subarea' =>
                        !empty($item->GeoObject->metaDataProperty->GeocoderMetaData->AddressDetails->Country->AdministrativeArea->SubAdministrativeArea)
                            ? $item->GeoObject->metaDataProperty->GeocoderMetaData->AddressDetails->Country->AdministrativeArea->
                        SubAdministrativeArea->SubAdministrativeAreaName
                            : '',
                    'description' =>
                        !empty($item->GeoObject->description)
                            ? $item->GeoObject->description
                            : '',
                    'name' =>
                        !empty($item->GeoObject->name)
                            ? $item->GeoObject->name
                            : '',
                    'point' =>
                        !empty($item->GeoObject->Point->pos)
                            ? str_replace(' ', ', ', $item->GeoObject->Point->pos)
                            : '',
                ]; ?>
                <table class="list-table">
                    <tr>
                        <td style="width: 100%;">
                            <h3><?php echo $index . '. '; ?><?php echo !empty($result->name) ? $result->name : ''; ?> (<?php echo $precisions[$result->precision]; ?>)</h3>
                            <table>
                                <tbody>
                                <tr>
                                    <td>Тип объекта:</td>
                                    <td><?php echo !empty($result->kind) ? $kinds[$result->kind] : ''; ?></td>
                                </tr>
                                <tr>
                                    <td>Страна:</td>
                                    <td><?php echo $result->country; ?></td>
                                </tr>
                                <tr>
                                    <td>Адрес:</td>
                                    <td><?php echo $result->address; ?></td>
                                </tr>
                                <tr>
                                    <td>Область:</td>
                                    <td><?php echo $result->area; ?></td>
                                </tr>
                                <tr>
                                    <td>Район:</td>
                                    <td><?php echo $result->subarea; ?></td>
                                </tr>
                                <tr>
                                    <td>Описание:</td>
                                    <td><?php echo $result->description; ?></td>
                                </tr>
                                <tr>
                                    <td>Имя:</td>
                                    <td><?php echo $result->name; ?></td>
                                </tr>
                                <tr>
                                    <td>Координаты:</td>
                                    <td><?php echo $result->point; ?></td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                        <td>
                            <div id="map-<?php echo $index; ?>" style="width: 600px; height: 400px"></div>
                            <script type="text/javascript">
                                ymaps.ready(init);
                                var myMap,
                                    myPlacemark;
                                function init() {
                                    myMap = new ymaps.Map("map-<?php echo $index;?>", {
                                        center: [<?php echo $result->point;?>],
                                        zoom: 16,
                                        coordorder: 'longlat'
                                    });

                                    myPlacemark = new ymaps.Placemark([<?php echo $result->point;?>], {
                                        hintContent: '<?php echo $result->name; ?>',
                                        balloonContent: '<?php echo $result->text; ?>'
                                    });
                                    myMap.geoObjects.add(myPlacemark);
                                }
                            </script>
                        </td>
                    </tr>
                </table>

                <?php
                $index++;
            }
        } else {
            echo 'По запросу <span class="color">"' . $_GET['search'] . '"</span> ничего не найдено!';
        }
    }
    /**
     * Ну, и закрыли соединение
     */
    curl_close($ch);
} else {
    include "form.php";
}