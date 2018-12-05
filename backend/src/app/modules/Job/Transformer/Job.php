<?php
namespace Job\Transformer;

use League\Fractal\TransformerAbstract;
use Phalcon\Di as PhDi;
use Job\{
    Model\Job as JobModel,
    Model\RelUserJob as RelUserJobModel,
    Transformer\Progress as ProgressTransformer
};
use User\{
    Model\User as UserModel,
    Transformer\User as UserTransformer
};
use Moment\Moment;

class Job extends TransformerAbstract
{
    protected $availableIncludes = [
        'progress'
    ];

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

    public function includeProgress(JobModel $job)
    {
        $di = PhDi::getDefault();
        $authService = $di->get('auth');

        $myUserProgress = RelUserJobModel::findFirst([
            'uid = :uid: AND jid = :jid:',
            'bind' => [
                'uid' => (int) $authService->getUser()['id'],
                'jid' => (int) $job->id
            ]
        ]);
        
        if ($myUserProgress) {
            return $this->item($myUserProgress, new ProgressTransformer);
        } else {
            return null;
        }
    }
}
