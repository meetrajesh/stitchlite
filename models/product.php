<?php

class product {

	private $id;

	public function __construct($id) {
		$this->id = $id;
	}

	public static function get_products($pid=null) {
		$sql = 'select p.name as product_name, p.store_id, v.* from products p join variants v on v.product_id=p.id';
		if ($pid > 0) {
			$sql .= ' where p.id=%d';
		}
		$sql .= ' order by p.id';
		$variants = db::fetch_all($sql, $pid);
		return self::group_variants_inside_products($variants);
	}

	public static function sort_variants_by_product_id(&$variants) {
		usort($variants, function($v1, $v2) {
  		    return strcmp($v1['product_id'], $v2['product_id']);
		});
	}

	public static function group_variants_inside_products($variants) {
		self::sort_variants_by_product_id($variants);
		$products = [];
		foreach ($variants as $variant) {
			$pid = $variant['product_id'];
			if (!isset($products[$pid])) {
				$products[$pid] = array('id' => $pid,
										'store_id' => $variant['store_id'],
										'name' => $variant['product_name'],
										'variants' => []);
			}
			$variant = array_select_keys($variant, array('id', 'name', 'sku', 'quantity', 'price'));
			$variant['quantity'] = (int)$variant['quantity'];
			$variant['price'] = number_format($variant['price'], 2);
		    $products[$pid]['variants'][] = $variant;
			$old_pid = $pid;
		}

		if (count($products) == 0) {
			return [];
		} elseif (count($products) == 1) {
			return array_shift($products);
		} else {
			return array('products' => array_values($products));
		}
	}

	public function create_or_update_product_variant($name, $sku, $quantity, $price) {
		$variant_id = db::result_query('select id from variants where sku="%s" and product_id=%d', $sku, $this->id);
		if ($variant_id) {
			return db::query('update variants set name="%s", quantity="%d", price="%f" where id=%d', $name, $quantity, $price, $variant_id);
		} else {
			return db::query('insert into variants (product_id, name, sku, quantity, price) values ("%d", "%s", "%s", "%d", "%f")', $this->id, $name, $sku, $quantity, $price);
		}
	}

}
