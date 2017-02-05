<?php

/*
 * php class for connecting to the Whiplash order API
 * https://www.whiplashmerch.com/developers/api
 *
 *
 */

namespace Whiplash;

class WhiplashApi {

    // property declaration
    public $base_url;
    public $connection;

    // Constructor
    public function __construct($api_key, $api_version = '', $test = false) {
        if ($test == true) {
            $this->base_url = 'http://testing.whiplashmerch.com/api/';
        } else {
            $this->base_url = 'https://www.whiplashmerch.com/api/';
        }

        $ch = curl_init();
        // Set headers
        $headers = array('Content-type: application/json', 'Accept: application/json', "X-API-KEY: $api_key");
        if ($api_version != '') {
            array_push($headers, "X-API-VERSION: $api_version");
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $this->connection = $ch;
    }

    // Basic REST functions
    public function get($path, $params = array()) {
        $json_url = $this->base_url . $path;
        $query_params = http_build_query($params);
        // the $path sometimes has the "?" character included.  this is part of the Whiplash API
        // if the path includes "?" character and there are $params then this function will need to be updated
        // for more robust construction of the $json_url
        // for now this prevents the ? from being added twice
        if ($query_params) {
            $json_url .= '?' . $query_params;
        }
        $ch = $this->connection;
        curl_setopt($ch, CURLOPT_URL, $json_url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        $result = curl_exec($ch); // Getting jSON result string
        if($result === false){
			$result = ['code'=>400,'status'=>'error','message'=>curl_error($ch)];
        }
        $out = json_decode($result); // Decode the result
        return $out;
    }

    public function post($path, $params = array()) {
        $json_url = $this->base_url . $path;
        $ch = $this->connection;
        curl_setopt($ch, CURLOPT_URL, $json_url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        $result = curl_exec($ch); // Getting jSON result string
		if($result === false){
			$result = ['code'=>400,'status'=>'error','message'=>curl_error($ch)];
        }
        $out = json_decode($result); // Decode the result
        return $out;
    }

    public function put($path, $params = array()) {
        $json_url = $this->base_url . $path;
        $ch = $this->connection;
        curl_setopt($ch, CURLOPT_URL, $json_url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        $result = curl_exec($ch); // Getting jSON result string
		if($result === false){
			$result = ['code'=>400,'status'=>'error','message'=>curl_error($ch)];
        }
        $out = json_decode($result); // Decode the result
        return $out;
    }

    public function delete($path, $params = array()) {
        $json_url = $this->base_url . $path;
        $ch = $this->connection;
        curl_setopt($ch, CURLOPT_URL, $json_url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        $result = curl_exec($ch); // Getting jSON result string
		if($result === false){
			$result = ['code'=>400,'status'=>'error','message'=>curl_error($ch)];
        }
        $out = json_decode($result); // Decode the result
        return $out;
    }

    /** Item functions * */
    public function get_items($params = array()) {
        return $this->get('items', $params);
    }

    public function get_items_count($params = array()) {
        return $this->get('items/count', $params);
    }

    public function get_item($id) {
        return $this->get('items/' . $id);
    }

    public function get_item_transactions($id) {
        return $this->get('items/' . $id . '/transactions');
    }

    public function get_items_by_sku($sku, $params = array()) {
        return $this->get('items/sku/' . rawurlencode($sku), $params);
    }

    public function get_item_by_originator($id) {
        return $this->get('items/originator/' . $id);
    }

    // This requires a valid ID
    public function create_item($params = array()) {
        $p = array();

        if (!array_key_exists('item', $params)) {
            $p['item'] = $params;
        } else {
            $p = $params;
        }
        return $this->post('items', $p);
    }

    // This requires a valid ID
    public function update_item($id, $params = array()) {
        $p = array();
        if (!array_key_exists('item', $params)) {
            $p['item'] = $params;
        } else {
            $p = $params;
        }
        return $this->put('items/' . $id, $p);
    }

    // This requires a valid ID
    public function delete_item($id) {
        return $this->delete('items/' . $id);
    }

    /** Order functions * */
    public function get_orders($params = array()) {
        return $this->get('orders', $params);
    }

    public function get_orders_count($params = array()) {
        return $this->get('orders/count', $params);
    }

    public function get_order($id) {
        return $this->get('orders/' . $id);
    }

    public function get_order_items($id) {
        return $this->get('order_items?order_id=' . $id);
    }

    public function get_order_by_originator($id) {
        return $this->get('orders/originator/' . $id);
    }

    public function get_order_by_status($status) {
        return $this->get('orders/status/' . $status);
    }

    // This requires a valid ID
    public function create_order($params = array()) {
        $p = array();
        if (!array_key_exists('order', $params)) {
            $p['order'] = $params;
        } else {
            $p = $params;
        }

        if (array_key_exists('order', $p)) {
            if (array_key_exists('order_items', $p['order'])) {
                $p['order']['order_items_attributes'] = $p['order']['order_items'];
                unset($p['order']['order_items']);
            }
        }
        return $this->post('orders', $p);
    }

    // This requires a valid ID
    public function update_order($id, $params = array()) {
        $p = array();
        if (!array_key_exists('order', $params)) {
            $p['order'] = $params;
        } else {
            $p = $params;
        }
        return $this->put('orders/' . $id, $p);
    }

    // possibly rename this to "cancel_order" since "delete" is not supported by the api
    public function delete_order($id) {
        return $this->put('orders/' . $id . '/cancel'); // orders/{order-id}/cancel
    }

    // possibly rename this to "uncancel_order" since "undelete" is not supported by the api
    public function undelete_order($id) {
        return $this->put('orders/' . $id . '/uncancel'); // orders/{order-id}/uncancel
    }

    // This requires a valid ID
    public function pause_order($id) {
        return $this->put('orders/' . $id . '/pause'); // orders/{order-id}/pause
    }

    // This requires a valid ID
    public function unpause_order($id) {
        return $this->put('orders/' . $id . '/release'); // orders/{order-id}/release
    }

    /** OrderItem functions * */
    public function get_order_item($id) {
        return $this->get('order_items/' . $id);
    }

    public function get_order_item_by_originator($id) {
        return $this->get('order_items/originator/' . $id);
    }

    // This requires a valid ID
    public function create_order_item($params = array()) {
        $p = array();
        if (!array_key_exists('order_item', $params)) {
            $p['order_item'] = $params;
        } else {
            $p = $params;
        }
        return $this->post('order_items', $p);
    }

    // This requires a valid ID
    public function update_order_item($id, $params = array()) {
        $p = array();
        if (!array_key_exists('order_item', $params)) {
            $p['order_item'] = $params;
        } else {
            $p = $params;
        }
        return $this->put('order_items/' . $id, $p);
    }

    // This requires a valid ID
    public function delete_order_item($id) {
        return $this->delete('order_items/' . $id);
    }

}
