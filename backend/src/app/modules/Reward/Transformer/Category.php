<?php
namespace Reward\Transformer;

use League\Fractal\TransformerAbstract;
use Reward\Model\Category as RewardCategoryModel;
use Moment\Moment;

class Category extends TransformerAbstract
{
    protected $availableIncludes = [];

    public function transform(RewardCategoryModel $category)
    {
        return [
            'id' => (string) $category->id,
            'name' => (string) $category->name,
            'description' => (string) $category->description,
            'displayorder' => (string) $category->displayorder,
            'status' =>  [
                'label' => (string) $category->getStatusName(),
                'value' => (string) $category->status,
                'style' => (string) $category->getStatusStyle()
            ],
            'parentid' => (string) $category->parentid,
            'datecreated' => [
                'readable' => (string) (new Moment($category->datecreated))->format('d/m/Y'),
                'timestamp' => (string) $category->datecreated
            ],
            'cover' => (string) $category->getCoverPath()
        ];
    }
}
