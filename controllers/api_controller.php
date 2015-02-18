<?php

class ApiController extends BaseController {

    public function __construct() {
        // skip csrf check
        $this->_tpl = new template();
    }

    public function help($data) {
        return array('/products' => array('inputs' => array(),
                                          'outputs' => array(
															 'products' => array('id' => 'integer',
																				 'name' => 'string',
																				 'variants' => 
																				 array('id' => 'int',
																					   'name' => 'string',
																					   'sku' => 'string',
																					   'quantity' => 'int',
																					   'price' => 'decimal')))),
					 '/product', array('inputs' => array('id' => 'integer'),
									   'outputs' => array('id' => 'integer',
														  'name' => 'string',
														  'variants' => 
														  array('id' => 'int',
																'name' => 'string',
																'sku' => 'string',
																'quantity' => 'int',
																'price' => 'decimal'))),
					 '/sync/all', array('inputs' => array(),
										'outputs' => 'boolean'),

                     );

    }

	public function sync_all() {
		return store::sync_all();
	}

    public function products($data) {
        return product::get_products();
    }

    public function product($data) {
        return product::get_products($data['id']);
    }

}