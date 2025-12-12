<?php

namespace App\Http\Controllers\Pay;

use App\Exceptions\RuleValidationException;
use App\Http\Controllers\PayController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache; // 引入缓存

class PaypalPayController extends PayController
{
    const Currency = 'USD'; // 货币单位

    /**
     * 支付网关入口
     * @param string $payway 支付方式
     * @param string $orderSN 订单号
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function gateway(string $payway, string $orderSN)
    {
        try {
            // 加载网关
            $this->loadGateWay($orderSN, $payway);

            // 获取商家邮箱
            $businessEmail = $this->payGateway->merchant_key; // 假设商家邮箱存储在 merchant_key 字段
            if (empty($businessEmail)) {
                throw new RuleValidationException('请配置 PayPal 商家邮箱');
            }

            // 计算金额（转换为美元）
            $total = $this->convertToUSD($this->order->actual_price);

            // 构建 PayPal 表单
            $html = $this->buildPayPalForm($businessEmail, $this->order->title, $orderSN, $total);

            // 返回 HTML 表单
            return response($html);
        } catch (RuleValidationException $exception) {
            return $this->err($exception->getMessage());
        } catch (\Exception $exception) {
            Log::error('PayPal 支付异常: ' . $exception->getMessage());
            return $this->err('支付异常，请稍后重试');
        }
    }

    /**
     * 将人民币金额转换为美元
     * @param float $amountCNY 人民币金额
     * @return float 美元金额
     */
    //private function convertToUSD(float $amountCNY): float
    //{
    //    // 使用固定汇率（例如 0.15）或调用汇率 API
    //   $rate = 0.137; // 默认汇率
    //    return round($amountCNY * $rate, 2);
    //}
    private function convertToUSD(float $amountCNY): float
    {
        // 默认兜底汇率 (1 CNY = 0.137 USD 约等于 1 USD = 7.3 CNY)
        $defaultRate = 0.137;

        try {
            // 使用 Cache 记住汇率 720 分钟 (12小时)，避免频繁请求 API
            $rate = Cache::remember('usd_cny_rate', 720, function () {
                $url = "https://api.exchangerate-api.com/v4/latest/USD";    
                
                // --- 开始：原生 cURL 代码替换部分 ---
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_TIMEOUT, 5); // 设置5秒超时
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Docker环境下有时候证书有问题，建议关闭
                $response = curl_exec($ch);
                $error = curl_error($ch);
                curl_close($ch);
                // --- 结束：原生 cURL 代码替换部分 ---

                if (!$response) {
                    throw new \Exception('API 请求失败: ' . $error);
                }

                // 解析 JSON 数据
                $data = json_decode($response, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception('JSON 解析失败: ' . json_last_error_msg());
                }

                if (!isset($data['rates']['CNY'])) {
                    throw new \Exception('汇率数据不存在');
                }

                // API 返回的是 1 USD = x CNY (例如 7.2)
                // 我们需要的是 1 CNY = x USD，所以是 1 / 7.2
                return 1 / $data['rates']['CNY'];
            });
        } catch (\Exception $e) {
            // API 失败或超时，使用默认汇率，并记录日志
            Log::warning('获取汇率失败，使用默认汇率: ' . $e->getMessage());
            $rate = $defaultRate;
        }
        // 计算最终金额并保留2位小数
        return round($amountCNY * $rate, 2);
    }

    /**
     * 构建 PayPal 表单
     * @param string $businessEmail 商家邮箱
     * @param string $productName 商品名称
     * @param string $orderSN 订单号
     * @param float $amount 金额（美元）
     * @return string HTML 表单
     */
    private function buildPayPalForm(string $businessEmail, string $productName, string $orderSN, float $amount): string
    {
        $returnUrl = route('paypal-return', ['orderSN' => $orderSN]);
        $notifyUrl = route('paypal-notify', ['orderSN' => $orderSN]);
        // 获取原始人民币金额用于 custom 字段
        $originalAmount = $this->order->actual_price;

        return <<<HTML
<!doctype html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>正在转到 PayPal 付款页</title>
</head>
<body onload="document.pay.submit()">
    <form name="pay" action="https://www.paypal.com/cgi-bin/webscr" method="post">
        <input type="hidden" name="cmd" value="_xclick">
        <input type="hidden" name="business" value="{$businessEmail}">
        <input type="hidden" name="item_name" value="{$productName}">
        <input type="hidden" name="item_number" value="{$orderSN}">
		<input type="hidden" name="custom" value="{$originalAmount}">
        <input type="hidden" name="currency_code" value="USD">
        <input type="hidden" name="amount" value="{$amount}">
        <input type="hidden" name="no_note" value="1">
        <input type="hidden" name="no_shipping" value="1">
        <input type="hidden" name="charset" value="utf-8">
        <input type="hidden" name="return" value="{$returnUrl}">
        <input type="hidden" name="cancel_return" value="{$returnUrl}">
        <input type="hidden" name="notify_url" value="{$notifyUrl}">
    </form>
</body>
</html>
HTML;
}

/**
 * PayPal 同步回调
 * @param Request $request
 * @return \Illuminate\Http\RedirectResponse
 */
public function returnUrl(Request $request)
{
    $orderSN = $request->input('orderSN');
    $paymentStatus = $request->input('payment_status');

    // 检查订单是否存在
    $order = $this->orderService->detailOrderSN($orderSN);
    if (!$order) {
        return $this->err('订单不存在');
    }
	
	sleep(5); 
    return redirect(url('detail-order-sn', ['orderSN' => $orderSN]));
}

/**
 * PayPal 异步通知
 * @param Request $request
 */
public function notifyUrl(Request $request)
{
    $rawPostData = file_get_contents('php://input');
    $rawPostArray = explode('&', $rawPostData);
    $postData = [];
    foreach ($rawPostArray as $keyval) {
        $keyval = explode('=', $keyval);
        if (count($keyval) == 2) {
            $postData[$keyval[0]] = urldecode($keyval[1]);
        }
    }

    // 记录日志
    Log::debug("PayPal 异步通知数据", $postData);

    // 验证 IPN 通知
    if ($this->verifyIPN($postData)) {
        $orderSN = $postData['item_number'] ?? '';
        $paymentStatus = $postData['payment_status'] ?? '';
        $txnId = $postData['txn_id'] ?? '';

        if ($paymentStatus === 'Completed') {
            // 支付成功
            $amountCNY = $postData['custom'] ?? 0;
			//修改IPN处理逻辑： 从 custom 字段获取原始人民币金额
			$this->orderProcessService->completedOrder($orderSN, $amountCNY, $txnId);
            Log::info("PayPal 异步通知支付成功", ['订单号' => $orderSN, '交易号' => $txnId]);
        }
    } else {
        Log::error("PayPal 异步通知验证失败", $postData);
    }
}

/**
 * 验证 PayPal IPN 通知
 * @param array $postData
 * @return bool
 */
private function verifyIPN(array $postData): bool
{
    $req = 'cmd=_notify-validate';
    foreach ($postData as $key => $value) {
        $req .= "&$key=" . urlencode($value);
    }

    $ch = curl_init('https://ipnpb.paypal.com/cgi-bin/webscr');
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Connection: Close']);
    $res = curl_exec($ch);
    curl_close($ch);

    return strcmp($res, "VERIFIED") === 0;
}
}
