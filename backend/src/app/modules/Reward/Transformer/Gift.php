<?php
namespace Reward\Transformer;

use League\Fractal\TransformerAbstract;
use Moment\Moment;
use Reward\Model\Gift as GiftModel;
use Reward\Transformer\GiftStock as GiftStockTransformer;

class Gift extends TransformerAbstract
{
    protected $availableIncludes = [];
    protected $defaultIncludes = [
        'stocks'
    ];

    public function transform(GiftModel $gift)
    {
        $humandatecreated = new Moment($gift->datecreated);
        if ($gift->dateused > 0) {
            $m = new Moment($gift->dateused);
            $humandateused = $m->format('d M Y, H:i');
        } else {
            $humandateused = '';
        }

        return [
            'id' => (string) $gift->id,
            'gtid' => (string) $gift->gtid,
            'name' => (string) $gift->name,
            'description' => (string) $gift->description,
            'displayorder' => (string) $gift->displayorder,
            'datecreated' => (string) $gift->datecreated,
            'humandatecreated' => (string) $humandatecreated->format('d-m-Y, H:i'),
            'dateused' => (string) $gift->dateused,
            'humandateused' => (string) $humandateused
        ];
    }

    public function includeStocks(GiftModel $gift)
    {
        $giftStokcs = $gift->getStocks();

        return $this->collection($giftStokcs, new GiftStockTransformer);
    }
}
