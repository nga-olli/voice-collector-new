<?php
namespace Reward\Transformer;

use League\Fractal\TransformerAbstract;
use Reward\Model\Category as RewardCategoryModel;
use Moment\Moment;

class Closure extends TransformerAbstract
{
    protected $availableIncludes = [];

    public function transform($category)
    {
        $output = $category;

        if (isset($category['children'])) {
            $output['children'] = [];
            foreach ($category['children'] as $child) {
                $output['children'][] = [
                    'id' => $child['id'],
                    'name' => $child['name']
                ];
            }
        }

        return $output;
    }
}
