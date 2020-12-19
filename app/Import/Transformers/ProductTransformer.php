<?php

namespace App\Import\Transformers;

use Illuminate\Support\Str;

/**
 * Class ProductTransformer.
 */
class ProductTransformer extends BaseTransformer
{
    /**
     * @param $data
     *
     * @return bool|Item
     */
    public function transform($data)
    {
        return [
                'company_id' => $this->maps['company']->id,
                'product_key' => $this->getString($data, 'product.product_key'),
                'notes' => $this->getString($data, 'product.notes'),
                'cost' => $this->getString($data, 'product.cost'),
                'price' => $this->getString($data, 'product.price'),
                'quantity' => $this->getString($data, 'product.quantity'),
                'tax_name1' => $this->getString($data, 'product.tax_name1'),
                'tax_rate1' => $this->getString($data, 'product.tax_rate1'),
                'tax_name2' => $this->getString($data, 'product.tax_name2'),
                'tax_rate2' => $this->getString($data, 'product.tax_rate2'),
                'tax_name3' => $this->getString($data, 'product.tax_name3'),
                'tax_rate3' => $this->getString($data, 'product.tax_rate3'),
                'custom_value1' => $this->getString($data, 'product.custom_value1'),
                'custom_value2' => $this->getString($data, 'product.custom_value2'),
                'custom_value3' => $this->getString($data, 'product.custom_value3'),
                'custom_value4' => $this->getString($data, 'product.custom_value4'),
            ];
    }
}
