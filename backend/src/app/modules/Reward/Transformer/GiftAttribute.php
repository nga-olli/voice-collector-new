<?php
namespace Reward\Transformer;

use League\Fractal\TransformerAbstract;
use Moment\Moment;
use Reward\Model\GiftAttribute as GiftAttributeModel;

class GiftAttribute extends TransformerAbstract
{
    protected $availableIncludes = [];

    public function transform(GiftAttributeModel $giftattribute)
    {
        $humandatecreated = new Moment($giftattribute->datecreated);

        return [
            'id' => (string) $giftattribute->id,
            'gtid' => (string) $giftattribute->gtid,
            'name' => (string) $giftattribute->name,
            'displaytype' => (string) $giftattribute->displaytype,
            'unit' => (string) $giftattribute->unit,
            'type' => (string) $giftattribute->type,
            'iscritical' => (string) $giftattribute->iscritical,
            'displayorder' => (string) $giftattribute->displayorder,
            'datecreated' => (string) $giftattribute->datecreated,
            'humandatecreated' => (string) $humandatecreated->format('d-m-Y, H:i')
        ];
    }
}
