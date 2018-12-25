<?php
namespace Reward\Transformer;

use League\Fractal\TransformerAbstract;
use Moment\Moment;
use Reward\Model\GiftStock as GiftStockModel;
use Reward\Transformer\GiftAttribute as GiftAttributeTransformer;

class GiftStock extends TransformerAbstract
{
    protected $availableIncludes = [];
    protected $defaultIncludes = [
        'attribute'
    ];

    public function transform(GiftStockModel $giftstock)
    {
        $humandatecreated = new Moment($giftstock->datecreated);

        return [
            'id' => (string) $giftstock->id,
            'gid' => (string) $giftstock->gid,
            'gaid' => (string) $giftstock->gaid,
            'value' => (string) $giftstock->value,
            'datecreated' => [
                'readable' => (string) (new Moment($giftstock->datecreated))->format('d/m/Y'),
                'timestamp' => (string) $giftstock->datecreated
            ]
        ];
    }

    public function includeAttribute(GiftStockModel $giftstock)
    {
        $giftAttribute = $giftstock->getAttribute();

        return $this->item($giftAttribute, new GiftAttributeTransformer);
    }
}
