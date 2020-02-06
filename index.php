<?php

// формируем URL в переменной $queryUrl
// как получить ссылку, написано здесь: https://helpdesk.bitrix24.ru/open/5408147/
$queryUrl = '';
// формируем параметры для создания лида в переменной $queryData
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nameForm = $_POST['name'];
    $utmSource = $_POST['utm_source'];
    $utmMedium = $_POST['utm_medium'];
    $utmCampaign = $_POST['utm_campaign'];
    parse_str($_POST['data'], $values);
    $queryData = http_build_query(array(
        'fields' => array(
            'TITLE' => $nameForm .' '.$_SERVER['SERVER_NAME'],
            'COMPANY_TITLE' => 'Название компании',
            'NAME' => $values['FIELDS']['name_zv_FID2'], // name_zv_FID2 - id инпута
            'EMAIL_HOME' => $values['FIELDS']['mail_zv_FID2'], // mail_zv_FID2 - id инпута
            'UF_CRM_112313213' => 123, // пользовательское свойство, если не нужно - убрать
            'UTM_CAMPAIGN' => $utmCampaign ? $utmCampaign : "",
            'UTM_MEDIUM' => $utmMedium ? $utmMedium : "",
            'UTM_SOURCE' => $utmSource ? $utmSource : "",
            'EMAIL' => Array(
                "n0" => Array(
                    "VALUE" => $values['FIELDS']['mail_zv_FID2'], // name_zv_FID2 - id инпута
                    "VALUE_TYPE" => "HOME",
                ),
            ),
            'PHONE' => Array(
                "n0" => Array(
                    "VALUE" => $values['FIELDS']['phone_zv_FID2'], // mail_zv_FID2 - id инпута
                    "VALUE_TYPE" => "MOBILE",
                ),
            ),
        ),
        'params' => array("REGISTER_SONET_EVENT" => "Y")
    ));
}
// обращаемся к Битрикс24 при помощи функции curl_exec
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_SSL_VERIFYPEER => 0,
    CURLOPT_POST => 1,
    CURLOPT_HEADER => 0,
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => $queryUrl,
    CURLOPT_POSTFIELDS => $queryData,
));
$result = curl_exec($curl);
curl_close($curl);
$result = json_decode($result, 1);
if (array_key_exists('error', $result)) echo "Ошибка при сохранении лида: ".$result['error_description']."<br/>";

?>