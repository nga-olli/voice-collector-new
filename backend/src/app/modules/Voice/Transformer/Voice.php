<?php
namespace Voice\Transformer;

use League\Fractal\TransformerAbstract;
use Moment\Moment;
use Voice\Model\Voice as VoiceModel;
use User\Model\User as UserModel;
use User\Transformer\User as UserTransformer;

class Voice extends TransformerAbstract
{
    protected $availableIncludes = [];
    protected $defaultIncludes = [
        'user'
    ];

    public function transform(VoiceModel $voice)
    {
        return [
            'id' => (string) $voice->id,
            'vsid' => (string) $voice->vsid,
            'jid' => (string) $voice->jid,
            'filepath' => (string) $voice->getFilePath(),
            'status' =>  [
                'label' => (string) $voice->getStatusName(),
                'value' => (string) $voice->status,
                'style' => (string) $voice->getStatusStyle()
            ],
            'datecreated' => [
                'readable' => (string) (new Moment($voice->datecreated))->format('d/m/Y'),
                'timestamp' => (string) $voice->datecreated
            ]
        ];
    }

    public function includeUser(VoiceModel $voice)
    {
        $myUser = UserModel::findFirstById($voice->uid);

        if (!$myUser) {
            $myUser = new UserModel();
        }

        return $this->item($myUser, new UserTransformer);
    }
}
