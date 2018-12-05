<?php
namespace Job\Transformer;

use League\Fractal\TransformerAbstract;
use Job\Model\RelUserJob as RelUserJobModel;
use Moment\Moment;

class Progress extends TransformerAbstract
{
    public function transform(RelUserJobModel $reluserjob)
    {
        return [
            'id' => (string) $reluserjob->id,
            'progress' => (string) $reluserjob->progress,
            'status' =>  [
                'label' => (string) $reluserjob->getStatusName(),
                'value' => (string) $reluserjob->status,
                'style' => (string) $reluserjob->getStatusStyle()
            ],
            'datecreated' => [
                'readable' => (string) (new Moment($reluserjob->datecreated))->format('d/m/Y'),
                'timestamp' => (string) $reluserjob->datecreated
            ]
        ];
    }
}
