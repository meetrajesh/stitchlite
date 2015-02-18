<?php

class store {

	private $id;

	public function __construct($id) {
		$this->id = $id;
	}

	public static function sync_all() {
		// iterate through every store
		foreach (db::fetch_all('select id from stores') as $store) {
			// iterate through every channel for that store (shopify, vend, etc.)
			foreach (db::fetch_all('select id, store_id, channel_id, username, password, store_url from store_channels where store_id=%d order by channel_id', $store['id']) as $store_channel) {
				$channel_name = channel::$channels[$store_channel['channel_id']];
				require_once 'channels/' . $channel_name . '.php';
				$channel = new $channel_name($store, $store_channel);
				return $channel->sync_to_store();
			}
		}
	}

	public function clear_all_products_and_variants() {
		$ids = db::col_query('select v.id, p.id from variants v join products p on v.product_id=p.id join stores s on p.store_id=s.id where s.id=%d', $this->id);
		db::query('delete from variants where id in (%s)', join(',', array_keys($ids)));
		db::query('delete from products where id in (%s)', join(',', array_values($ids)));
	}

	public function create_product($name) {
		db::query('insert into products (store_id, name) values ("%d", "%s")', $this->id, $name);
		return new product(db::insert_id());
	}

}