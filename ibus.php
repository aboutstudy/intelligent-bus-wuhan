<?php
/**
 * 智能交通API代理
 * @author Clear
 * @email jiangchunyi001@gmail.com
 * @blog http://me.doucl.com
 */
define('API', 'http://59.173.9.172:7000/wuhan/line!map.action');

$lineNo = $_GET['lineNo'] ? intval($_GET['lineNo']) : 0;
$direction = $_GET['direction'] ? intval($_GET['direction']) : 0;

if($lineNo === 0){
	echo 'showMessage("公交线路参数错误")';
}

$data = getProxy($lineNo, $direction);

echo "fetchData({$data})";

function getProxy($lineNo, $direction = 0){
	$api = API . "?direction={$direction}&lineNo={$lineNo}";

    $data = sendQuery($api);
    
	return json_encode($data);
}

function sendQuery($api) {
	$this_header = array(
		"content-type: application/x-www-form-urlencoded; 
		 charset=UTF-8"
	);

	$ch = curl_init($api);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_NOSIGNAL, 1);    //注意，毫秒超时一定要设置这个
    curl_setopt($ch, 155, 3000);  //超时毫秒，cURL 7.16.2中被加入。从PHP 5.2.3起可使用
	curl_setopt($ch, CURLOPT_POST, 0);

	$data = curl_exec($ch); 
	$curl_errno = curl_errno($ch);
	$curl_error = curl_error($ch);
	curl_close($ch);

	if ($curl_errno > 0) {
		$return = array(
			'status' => 500,
			'msg' => 'api timeout',
			'data' => null
		);
	}
	else{
		$result = json_decode($data, true);

		if($result['jsonr']['status'] == '00' && empty($result['jsonr']['data'])){
			$return = array(
				'status' => 404,	
				'msg' => 'not found',	
				'data' => null	
			);
		}
		elseif($result['jsonr']['status'] == '-1'){
			$return = array(
				'status' => 501,	
				'msg' => 'server error',	
				'data' => null	
			);
		}
		else{
			$return = array(
				'status' => 200,	
				'msg' => $result['jsonr']['errmsg'],	
				'data' => $result['jsonr']['data']	
			);
		}
	}
	return $return;
}
