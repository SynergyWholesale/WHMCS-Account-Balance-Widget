<?php

use WHMCS\Database\Capsule as DB;

define('SW_ACCOUNT_BALANCE_API_ENDPOINT', '{{API}}');
define('SW_ACCOUNT_BALANCE_WIDGET_NAME', 'synergywholesale_balance');

class SynergyWholesaleAccountBalanceWidgetAPI
{
    protected $resellerID;
    protected $apiKey;
    protected $soap;

    public function __construct($resellerID, $apiKey)
    {
        $this->soap = new SoapClient(null, [
            'location' => SW_ACCOUNT_BALANCE_API_ENDPOINT,
            'uri' => '',
            'trace' => true
        ]);

        $this->apiKey = $apiKey;
        $this->resellerID = $resellerID;
    }

    public function request($command, $params = [])
    {
        $params['resellerID'] = $this->apiKey;
        $params['apiKey'] = $this->resellerID;

        try {
            $response = $this->soap->$command($params);
        } catch (Exception $ex) {
            
        }

        return $response;
    }

    public function balanceQuery()
    {
        return $this->request('balanceQuery');
    }

}

class SynergyWholesaleAccountBalanceWidget extends \WHMCS\Module\AbstractWidget
{
    protected $title = 'Synergy Wholesale Available Balance';
    protected $description = '';
    protected $weight = 150;
    protected $columns = 1;
    protected $cache = false;
    protected $cacheExpiry = 120;
    protected $requiredPermission = '';

    public function getData() 
    {   
        $query = DB::table('tbladdonmodules')
                ->where('module', SW_ACCOUNT_BALANCE_WIDGET_NAME)
                ->select('setting', 'value')
                ->get();

        $result = [];

        foreach ($query as $data) {
            if ($data->setting == 'api_key') {
                $result['api_key'] = $data->value;
            }

            if ($data->setting == 'reseller_id') {
                $result['reseller_id'] = $data->value;
            }
        }

        return $result;
    }

    public function generateOutput($result)
    {
        $content = '';

        if (empty($result['api_key']) || empty($result['reseller_id'])) {
            return $content;
        }

        $api = new SynergyWholesaleAccountBalanceWidgetAPI($result['reseller_id'], $result['api_key']);

        $apiResult = $api->balanceQuery();
        if ($balance = $apiResult->balance) {
            $content .= '<div style="text-align: center; color: green; font-weight: bold; font-size: 18px; padding: 16px;">Balance: $' . number_format($balance, 2) . '</div>';
        }

        return $content;
    }
}

add_hook('AdminHomeWidgets', 1, function() {
    return new SynergyWholesaleAccountBalanceWidget();
});
