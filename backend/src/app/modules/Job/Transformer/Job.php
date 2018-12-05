<?php
namespace Job\Transformer;

use League\Fractal\TransformerAbstract;
use Job\Model\Job as JobModel;
use Moment\Moment;

class Job extends TransformerAbstract
{
    protected $availableIncludes = [];

    public function transform(JobModel $job)
    {
        return [
            'id' => (string) $job->id,
            'name' => (string) $job->name,
            'description' => (string) $job->description,
            'maxcoinreward' => (string) $job->maxcoinreward,
            'numberofscripts' => (string) $job->numberofscripts,
            'cover' => (string) $job->cover,
            'postedby' => (string) $job->postedby,
            'requiredid' => (string) $job->requiredid,
            'datecreated' => [
                'readable' => (string) (new Moment($job->datecreated))->format('d/m/Y'),
                'timestamp' => (string) $job->datecreated
            ],
            'dateexpired' => [
                'readable' => (string) (new Moment($job->dateexpired))->format('d/m/Y'),
                'timestamp' => (string) $job->dateexpired
            ]
        ];
    }
}
