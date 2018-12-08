<?php
namespace Voice\Transformer;

use League\Fractal\TransformerAbstract;
use Moment\Moment;
use Voice\Model\Script as VoiceScriptModel;

class Script extends TransformerAbstract
{
    protected $availableIncludes = [];

    public function transform(VoiceScriptModel $voicescript)
    {
        return [
            'id' => (string) $voicescript->id,
            'vscid' => (string) $voicescript->vscid,
            'command' => (string) $voicescript->command,
            'text' => (string) mb_strtolower($voicescript->text, 'UTF-8'),
            'status' =>  [
                'label' => (string) $voicescript->getStatusName(),
                'value' => (string) $voicescript->status,
                'style' => (string) $voicescript->getStatusStyle()
            ],
            'datecreated' => [
                'readable' => (string) (new Moment($voicescript->datecreated))->format('d/m/Y'),
                'timestamp' => (string) $voicescript->datecreated
            ]
        ];
    }
}
