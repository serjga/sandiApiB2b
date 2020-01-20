<?php
namespace Sandi\Api;
/**
 * Класс позволяет создавать запросы к серверу для получения данных по API.
 */
class ApiSandiB2b
{
    const FORMAT_RESPONSE = 'json';

    const PARAM_LANG_UA = 'ua';
    const PARAM_ID = 'id';
    const PARAM_MODEL = 'model';
    const PARAM_ART = 'art';
    const PARAM_BARCODE = 'barcode';
    const PARAM_CATEGORY = 'category';
    const PARAM_VENDOR_CODE = 'vendor_code';

    const MODEL_PRODUCT = 'Product';
    const TEST_MODEL_PRODUCT = 'product';

    private $model = FALSE;
    private $method = FALSE;
    private $properties = [];

    /**
     * Присваиваем свойству $properties экземпляра класса первым значение персонального ключа (токена).
     * Также доступна возможность выбора языка (русский, украинский), на котором будут приходить данные.
     * @param string $key персональный ключ (токен)
     * @param null|string $lang язык, на котором приходят данные (по умолчанию данные приходят на русском языке,
     * также доступен украинский при присваивании значения 'ua' в нижнем регистре)
     */
    public function __construct($key, $lang = null)
    {
        $this->properties['apiKey'] = $key;
        if(self::PARAM_LANG_UA === $lang) {
            $this->properties['lang'] = $lang;
        }
    }

    /**
     * Отправка GET запроса.
     * @return string
     */
    private function sentGetRequest()
    {
        // $url = "https://b2b-sandi.com.ua/api/". $this->model ."/" . $this->method . "/?" . $this->createStrProperties();
        // https://b2b-sandi.com.ua/api/product/get-by-sku?sku=4482
        $url = "https://b2b-sandi.com.ua/api/" . $this->model . "/" . $this->method . $this->createStrProperties();
        echo $url . PHP_EOL;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => TRUE,
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            return $err;
        } else {
            return $response;
        }
    }

    /**
     * Получение списка всех товаров ТЕСТ.
     * По умолчанию возвращает массив.
     * Будет возвращена JSON-строка, если присвоить переменной $type значение 'json' (в нижнем регистре)
     * @param null|string $type название идентификатора типа данных, которые необходимо вернуть
     * @return string|array
     */
    public function TEST_getProducts($type = null)
    {
        $this->model = self::TEST_MODEL_PRODUCT;
        $this->method = "get-random";
        return $this->getAnswerRequest($type);
    }

    /**
     * Получение списка товаров по категории ТЕСТ.
     * По умолчанию возвращает массив.
     * Будет возвращена JSON-строка, если присвоить переменной $type значение 'json' (в нижнем регистре)
     * @param string $category идентификатор категории
     * @param null|string $type название идентификатора типа данных, которые необходимо вернуть
     * @return string|array
     */
    public function TEST_getProduct($param, $nameIdentifier = null, $type = null)
    {
        $this->model = self::TEST_MODEL_PRODUCT;
        $this->method = "get-by-sku";
        $this->properties['sku'] = '15344';
        $this->createPropertyUniqueIdentifier($param, $nameIdentifier);
        return $this->getAnswerRequest($type);
    }

    /**
     * Получение списка всех товаров.
     * По умолчанию возвращает массив.
     * Будет возвращена JSON-строка, если присвоить переменной $type значение 'json' (в нижнем регистре)
     * @param null|string $type название идентификатора типа данных, которые необходимо вернуть
     * @return string|array
     */
    public function getProducts($type = null)
    {
        $this->model = self::MODEL_PRODUCT;
        $this->method = "getFullListProduct";
        return $this->getAnswerRequest($type);
    }

    /**
     * Получение списка товаров по категории.
     * По умолчанию возвращает массив.
     * Будет возвращена JSON-строка, если присвоить переменной $type значение 'json' (в нижнем регистре)
     * @param string $category идентификатор категории
     * @param null|string $type название идентификатора типа данных, которые необходимо вернуть
     * @return string|array
     */
    public function getProductsByCategory($category, $type = null)
    {
        $this->model = self::MODEL_PRODUCT;
        $this->method = "getGoodsByCategory";
        $this->properties[self::PARAM_CATEGORY] = $this->createArrProperties($category);
        return $this->getAnswerRequest($type);
    }

    /**
     * Получение списка товаров по бренду.
     * По умолчанию возвращает массив.
     * Будет возвращена JSON-строка, если присвоить переменной $type значение 'json' (в нижнем регистре)
     * @param string $vendor идентификатор бренда
     * @param null|string $type название идентификатора типа данных, которые необходимо вернуть
     * @return string|array
     */
    public function getProductsByVendor($vendor, $type = null)
    {
        $this->model = self::MODEL_PRODUCT;
        $this->method = "getGoodsByVendor";
        $this->properties[self::PARAM_VENDOR_CODE] = $this->createArrProperties($vendor);
        return $this->getAnswerRequest($type);
    }

    /**
     * Получение данных 1 товара по уникальному идентификатору.
     * По умолчанию возвращает массив.
     * Будет возвращена JSON-строка, если присвоить переменной $type значение 'json' (в нижнем регистре)
     * @param string $param значения уникального идентификатора товара
     * @param string $nameIdentifier название уникального идентификатора товара
     * @param null|string $type название идентификатора типа данных, которые необходимо вернуть
     * @return string|array
     */
    public function getProduct($param, $nameIdentifier = null, $type = null)
    {
        $this->model = self::MODEL_PRODUCT;
        $this->method = "getProductByUniqueCode";
        $this->createPropertyUniqueIdentifier($param, $nameIdentifier);
        return $this->getAnswerRequest($type);
    }

    /**
     * Получение актуального остатка для товара.
     * По умолчанию возвращает массив.
     * Будет возвращена JSON-строка, если присвоить переменной $type значение 'json' (в нижнем регистре)
     * @param string $param значения уникального идентификатора товара
     * @param string $nameIdentifier название уникального идентификатора товара
     * @param null|string $type название идентификатора типа данных, которые необходимо вернуть
     * @return string|array
     */
    public function getOfferProduct($param, $nameIdentifier = null, $type = null)
    {
        $this->model = self::MODEL_PRODUCT;
        $this->method = "getOfferForProduct";
        $this->createPropertyUniqueIdentifier($param, $nameIdentifier);
        return $this->getAnswerRequest($type);
    }

    /**
     * Получение актуальной цены для товара.
     * По умолчанию возвращает массив.
     * Будет возвращена JSON-строка, если присвоить переменной $type значение 'json' (в нижнем регистре)
     * @param string $param значения уникального идентификатора товара
     * @param string $nameIdentifier название уникального идентификатора товара (по умолчанию id товара,
     * также доступны идентификаторы: 'model', 'art', 'barcode')
     * @param null|string $type название идентификатора типа данных, которые необходимо вернуть
     * @return string|array
     */
    public function getPriceProduct($param, $nameIdentifier = null, $type = null)
    {
        $this->model = self::MODEL_PRODUCT;
        $this->method = "getPriceForProduct";
        $this->createPropertyUniqueIdentifier($param, $nameIdentifier);
        return $this->getAnswerRequest($type);
    }

    /**
     * Возвращение ответа на запрос в различных форматах.
     * Если $type присвоено значение null будет возвращен массив.
     * Если $type присвоено значение 'json' будет возвращена JSON-строка.
     * @param string $type название типа данных, возвращаемых методом
     * @return string|array
     */
    protected function getAnswerRequest($type)
    {
        /*
        if ($type === self::FORMAT_RESPONSE) {
            return $this->sentGetRequest();
        }
        else {
            return (json_decode($this->sentGetRequest(), true));
        }
        */
        $this->TEST_getAnswerRequest($type);
    }


    /**
     * Выводит в консоль ответ на запрос в различных форматах.
     * Если $type присвоено значение null будет возвращен массив.
     * Если $type присвоено значение 'json' будет возвращена JSON-строка.
     * @param string $type название типа данных, возвращаемых методом
     */
    protected function TEST_getAnswerRequest($type)
    {
        if ($type === self::FORMAT_RESPONSE) {
            echo $this->sentGetRequest();
        }
        else {
            print_r(json_decode($this->sentGetRequest(), true));
        }
    }

    /**
     * Приведение к единому виду значения параметра,
     * т.к. в переменной может приходить как массив так и строка (число).
     * @param int|string|array $param значения параметров, указанных при использовании методов
     * @return string
     */
    protected function createArrProperties($param)
    {
        return is_array($param) ? json_encode($param) : json_encode([$param]);
    }

    /**
     * Создание части адрессной строки из массива параметров.
     * @return string
     */
    protected function createStrProperties()
    {
        $str_param = http_build_query($this->properties);
        return ($str_param != '') ? "?" . $str_param : '';
    }

    /**
     * Создание параметра уникального идентификатора.
     * @param string $param значения уникального идентификатора товара
     * @param string $nameIdentifier название уникального идентификатора товара
     */
    protected function createPropertyUniqueIdentifier($param, $nameIdentifier)
    {
        if($nameIdentifier === null) {
            $this->properties[self::PARAM_ID] = $param;
        }
        else {
            if ($nameIdentifier === self::PARAM_MODEL) {
                $this->properties[self::PARAM_MODEL] = $param;
            }
            else if($nameIdentifier === self::PARAM_ART) {
                $this->properties[self::PARAM_ART] = $param;
            }
            else if($nameIdentifier === self::PARAM_BARCODE) {
                $this->properties[self::PARAM_BARCODE] = $param;
            }
        }
    }
}