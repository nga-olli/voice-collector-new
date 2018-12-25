<?php
namespace Voice\Transformer;

use League\Fractal\TransformerAbstract;
use Moment\Moment;
use Voice\Model\ScriptCategory as ScriptCategoryModel;

class ScriptCategory extends TransformerAbstract
{
    protected $availableIncludes = [];

    public function transform(ScriptCategoryModel $scriptcategory)
    {
        return [
            'id' => (string) $scriptcategory->id,
            'name' => (string) $scriptcategory->name,
            'status' =>  [
                'label' => (string) $scriptcategory->getStatusName(),
                'value' => (string) $scriptcategory->status,
                'style' => (string) $scriptcategory->getStatusStyle()
            ],
            'datecreated' => [
                'readable' => (string) (new Moment($scriptcategory->datecreated))->format('d/m/Y'),
                'timestamp' => (string) $scriptcategory->datecreated
            ]
        ];
    }
}
