<?php

require_once 'channel_interface.php';

class shopify extends channel implements channel_interface {

	private $store;
	private $store_channel;

	public function __construct($store, $store_channel) {
		$this->store = $store;
		$this->store_channel = $store_channel;
	}

	public function sync_to_store() {
		$url = spf('https://%s:%s@%s.myshopify.com/admin/products.json', $this->store_channel['username'], $this->store_channel['password'], $this->store_channel['store_url']);

		// comment out for speed of testing
		$products = json_decode(file_get_contents($url), true)['products'];
		// $products = json_decode(file_get_contents('./tmp.json'), true)['products'];

		$store = new store($this->store['id']);

		// uncomment for testing
		// $store->clear_all_products_and_variants();

		foreach ($products as $product) {
			$product_obj = $store->create_product_if_not_exists($product['title']); // product name

			foreach ($product['variants'] as $variant) {
				list($name, $sku, $quantity, $price) = array_pluck($variant, array('title', 'sku', 'inventory_quantity', 'price'));
				$product_obj->create_or_update_product_variant($name, $sku, $quantity, $price);
			}
		}

		return true;
	}
}