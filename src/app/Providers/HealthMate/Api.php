<?php

namespace App\Providers\HealthMate;

use GuzzleHttp\Client;

class Api implements ApiInterface
{
    /**
     * @var string
     */
    private $clientId;

    /**
     * @var string
     */
    private $secretId;

    /**
     * @var string
     */
    private $refreshToken;

    /**
     * @var string
     */
    private $endPoint;

    /**
     * @var string
     */
    private $uri;

    /**
     * @var Client
     */
    private $clientRequest;


    /**
     * @var array
     */
    private $params = [];


    /**
     * @var int
     */
    private $userId = 0;

    /**
     * @var string
     */
    private $accessToken = '';


    /**
     * @var array
     */
    private $apiUri = [
        'getAuthorizeUrl' => 'oauth2_user/authorize2',
        'getClientToken' => 'oauth2/token',

        'getUser' => 'user',

        'getMeasure' => 'measure',
        'notify' => 'notify',
        'measure' => 'v2/measure',
    ];

    const ENDPOINT_ACCOUNT = 'https://account.health.nokia.com';

    const ENDPOINT_API = 'https://api.health.nokia.com';

    /**
     * @param $uri
     * @return string
     */
    public function getApiUri($uri)
    {
        if (empty($this->apiUri[$uri])) {
            return '';
        }

        return $this->apiUri[$uri];
    }

    /**
     * @return string
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * @param string $clientId
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;
    }

    /**
     * @return string
     */
    public function getSecretId()
    {
        return $this->secretId;
    }

    /**
     * @param string $secretId
     */
    public function setSecretId($secretId)
    {
        $this->secretId = $secretId;
    }

    /**
     * @return string
     */
    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    /**
     * @param string $refreshToken
     */
    public function setRefreshToken($refreshToken)
    {
        $this->refreshToken = $refreshToken;
    }

    /**
     * @return string
     */
    public function getEndPoint()
    {
        return $this->endPoint;
    }

    /**
     * @param string $endPoint
     */
    public function setEndPoint($endPoint)
    {
        $this->endPoint = $endPoint;
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @param string $uri
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
    }

    /**
     * @return Client
     */
    public function getClientRequest()
    {
        return $this->clientRequest;
    }

    /**
     * @param Client $clientRequest
     */
    public function setClientRequest(Client $clientRequest)
    {
        $this->clientRequest = $clientRequest;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param array $params
     */
    public function setParams(array $params)
    {
        $this->params = $params;
    }

    /**
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @param string $accessToken
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    public function __construct($clientId, $secretId, $clientRequest)
    {
        $this->setClientId($clientId);
        $this->setSecretId($secretId);
        $this->setClientRequest($clientRequest);
    }

    public function getAuthorizeUrl($redirectUrl, $scope = '')
    {
        if (empty($scope)) {
            $scope = 'user.info';
        }

        return $this->_getAccountUrl($this->getApiUri('getAuthorizeUrl'), [
            'response_type' => 'code',
            'client_id' => $this->getClientId(),
            'state' => md5(uniqid()),
            'scope' => $scope,
            'redirect_uri' => $redirectUrl
        ]);
    }

    /**
     * @param $redirectUrl
     * @param $code
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getClientToken($redirectUrl, $code)
    {
        return $this->getClientRequest()->request(
            'POST', $this->_getAccountUrl($this->getApiUri('getClientToken')), [
            'form_params' => [
                'grant_type' => 'authorization_code',
                'client_id' => $this->getClientId(),
                'client_secret' => $this->getSecretId(),
                'code' => $code,
                'redirect_uri' => $redirectUrl
            ]
        ]);
    }

    /**
     * After you get your access code, you can request an access token
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function refreshClientToken()
    {
        $params = [
            'grant_type' => 'refresh_token',
            'client_id' => $this->getClientId(),
            'client_secret' => $this->getSecretId(),
            'refresh_token' => $this->getRefreshToken(),
        ];

        return $this->getClientRequest()->request(
            'POST', $this->_getAccountUrl($this->getApiUri('getClientToken')), [
            'form_params' => $params
        ]);
    }

    /**
     * With this service, you can retrieve data from your user ( scope : user.info ). Please find the parameters below
     * @param array $params
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getUser($params = [])
    {
        $params['action'] = 'getinfo';

        if (empty($params['access_token'])) {
            $params['access_token'] = $this->getAccessToken();
        }

        return $this->getClientRequest()->request(
            'GET', $this->_getApiUrl($this->getApiUri('getUser'), $params)
        );
    }

    /**
     * With this service, you can retrieve information about the devices of the user. Please find the parameters below
     *
     * @param array $params
     *
     * @internal $userid    Number    Callback, url encoded.
     * @internal $startdate optional    Date as unix epoch    Start date for the measures
     * @internal $enddate optional    Date as unix epoch    End date for the measures
     * @internal $lastupdate optional    Date as unix epoch    Returns measures updated or created after a certain date.
     * Useful for data synchronization between systems.
     * Note that you don't need to fill the startdate and enddate parameters when using this parameter.
     *
     * @internal $meastype optional    Number Measure type filter. Value is a number, which corresponds to :
     *  1 : Weight (kg)
     *  4 : Height (meter)
     *  5 : Fat Free Mass (kg)
     *  6 : Fat Ratio (%)
     *  8 : Fat Mass Weight (kg)
     *  9 : Diastolic Blood Pressure (mmHg)
     *  10 : Systolic Blood Pressure (mmHg)
     *  11 : Heart Pulse (bpm)
     *  54 : SP02(%)
     *  71 : Body Temperature73 : Skin Temperature
     *  76 : Muscle Mass
     *  77 : Hydration
     *  88 : Bone Mass
     *  91 : Pulse Wave Velocity
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getMeasure($params = [])
    {
        $params['action'] = 'getmeas';

        if (empty($params['access_token'])) {
            $params['access_token'] = $this->getAccessToken();
        }

        return $this->getClientRequest()->request(
            'GET', $this->_getApiUrl($this->getApiUri('getMeasure'), $params)
        );
    }

    /**
     * The Workouts API lets you retrieve the data relevant to workout sessions as measured by the Nokia activity trackers.
     * The data is presented as aggregates for each workout session of a given day.
     * Detailed minute-by-minute data for all workout activities is also available through the GetIntradayActivity service.
     * Note: You can retrieve 200 workouts by calls
     *
     * https://api.health.nokia.com/v2/measure?action=getworkouts
     *
     * @param $params
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getWorkOuts($params = [])
    {
        $params['action'] = 'getworkouts';

        if (empty($params['access_token'])) {
            $params['access_token'] = $this->getAccessToken();
        }

        if (empty($params['userid'])) {
            $params['userid'] = $this->getUserId();
        }

        return $this->getClientRequest()->request(
            'GET', $this->_getApiUrl($this->getApiUri('measure'), $params)
        );
    }

    /**
     * Provides daily aggregated activity data of a user
     *
     * https://api.health.nokia.com/v2/measure?action=getactivity
     *
     * @param array $params
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getActivity($params = [])
    {
        $params['action'] = 'getactivity';

        if (empty($params['access_token'])) {
            $params['access_token'] = $this->getAccessToken();
        }

        return $this->getClientRequest()->request(
            'GET', $this->_getApiUrl($this->getApiUri('measure'), $params)
        );
    }

    /**
     * Retrieves activity data for the user with a fine granularity. The data is given per timestamp.
     * Note: if startdate and enddate are separated by more than 24h, enddate will be overwritten with 24h past startdate.
     * Also, without startdate and enddate, the last activity data will be rendered.
     *
     * https://api.health.nokia.com/v2/measure?action=getintradayactivity
     *
     * @param array $params
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getDetailActivity($params = [])
    {
        $params['action'] = 'getintradayactivity';

        if (empty($params['access_token'])) {
            $params['access_token'] = $this->getAccessToken();
        }

        return $this->getClientRequest()->request(
            'GET', $this->_getApiUrl($this->getApiUri('measure'), $params)
        );
    }

    /**
     * Subscribes to receive notifications when new data is available.
     *
     * @param array $params
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     * https://api.health.nokia.com/notify?action=subscribe
     */
    public function subscribe($params)
    {
        $params['action'] = 'subscribe';

        if (empty($params['access_token'])) {
            $params['access_token'] = $this->getAccessToken();
        }

        return $this->getClientRequest()->request(
            'GET', $this->_getApiUrl($this->getApiUri('notify'), $params)
        );
    }

    public function getNotifyCategory()
    {
        return [
            1 => 'Weight',
            4 => 'Heart Rate, Diastolic pressure, Systolic pressure, Oxymetry',
            16 => 'Activity Measure ( steps, calories, distance, elevation)',
            44 => 'Sleep',
            46 => 'User'
        ];
    }

    private function _getAccountUrl($uri = '', $params = [])
    {
        return $this->_getUrl(self::ENDPOINT_ACCOUNT, $uri, $params);
    }

    private function _getApiUrl($uri = '', $params = [])
    {
        return $this->_getUrl(self::ENDPOINT_API, $uri, $params);
    }

    private function _getUrl($endpoint, $uri = '', $params = [])
    {
        $endpoint = trim($endpoint, '/') . '/';

        if (!empty($uri)) {
            $endpoint .= trim($uri, '/');
        }

        if (!empty($params)) {
            $endpoint .= '?' . http_build_query($params);
        }

        return $endpoint;
    }

}