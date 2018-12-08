<?php
namespace Voice\Controller\V1;

use Shirou\Constants\ErrorCode;
use Shirou\UserException;
use Core\Controller\AbstractController;
use Core\Helper\Utils as Helper;
use Voice\{
    Model\Script as VoiceScriptModel,
    Model\ScriptCategory as VoiceScriptCategoryModel,
    Transformer\Script as VoiceScriptTransformer
};

/**
 * @RoutePrefix("/v1/scripts")
 */
class ScriptController extends AbstractController
{
    protected $recordPerPage = 100;
    private $allowedFormat = ['txt'];

    /**
     * @Route("/random/{vscid:[0-9]+}", methods={"GET"})
     */
    public function randomAction($vscid = 0)
    {
        $sql = 'SELECT * FROM fly_voice_script AS r1 JOIN ';
        $sql .= '(SELECT CEIL(RAND() * (SELECT MAX(vs_id) FROM fly_voice_script)) AS id) AS r2 ';
        $sql .= 'WHERE r1.vs_id >= r2.id AND r1.vs_status = 1 AND r1.vsc_id = ' . (int) $vscid . ' ORDER BY r1.vs_id ASC LIMIT 1';
        $raw = $this->getDI()->get('db')->fetchOne($sql);

        if (!empty($raw)) {
            $myVoiceScript = new VoiceScriptModel();
            $myVoiceScript->id = (int) $raw['vs_id'];
            $myVoiceScript->vscid = (int) $raw['vsc_id'];
            $myVoiceScript->command = (string) $raw['vs_command'];
            $myVoiceScript->text = (string) $raw['vs_text'];
            $myVoiceScript->status = (int) $raw['vs_status'];
            $myVoiceScript->datecreated = (int) $raw['vs_date_created'];

            return $this->createCollection(
                [$myVoiceScript],
                new VoiceScriptTransformer,
                'data'
            );
        } else {
            return $this->respondWithArray([], 'data');
        }
    }

    /**
     * @Route("/", methods={"GET"})
     */
    public function listAction()
    {
        $page = (int) $this->request->getQuery('page', null, 1);
        $formData = [];
        $hasMore = true;

        // Search keyword in specified field model
        $searchKeywordInData = [
            ''
        ];
        $page = (int) $this->request->getQuery('page', null, 1);
        $orderBy = (string) $this->request->getQuery('orderby', null, 'id');
        $orderType = (string) $this->request->getQuery('ordertype', null, 'desc');
        $keyword = (string) $this->request->getQuery('keyword', null, '');

        // optional Filter
        $status = (int) $this->request->getQuery('status', null, 0);

        $formData['columns'] = '*';
        $formData['conditions'] = [
            'keyword' => $keyword,
            'searchKeywordIn' => $searchKeywordInData,
            'filterBy' => [
                'status' => $status
            ]
        ];
        $formData['orderBy'] = $orderBy;
        $formData['orderType'] = $orderType;

        $myVoiceScripts = VoiceScriptModel::paginate($formData, $this->recordPerPage, $page);

        if ($myVoiceScripts->total_pages > 0) {
            if ($page == $myVoiceScripts->total_pages) {
                $hasMore = false;
            }

            return $this->createCollection(
                $myVoiceScripts->items,
                new VoiceScriptTransformer,
                'data',
                [
                    'meta' => [
                        'recordPerPage' => $this->recordPerPage,
                        'hasMore' => $hasMore,
                        'totalItems' => $myVoiceScripts->total_items,
                        'orderBy' => $orderBy,
                        'orderType' => $orderType,
                        'page' => $page
                    ]
                ]
            );
        } else {
            return $this->respondWithArray([], 'data');
        }
    }

    /**
     * @Route("/{id:[0-9]+}", methods={"GET"})
     */
    public function getAction(int $id = 0)
    {
        $myVoiceScript = VoiceScriptModel::findFirst([
            'id = :id:',
            'bind' => ['id' => (int) $id]
        ]);

        if (!$myVoiceScript) {
            throw new UserException(ErrorCode::DATA_NOTFOUND);
        }

        return $this->createItem(
            $myVoiceScript,
            new VoiceScriptTransformer,
            'data'
        );
    }

    /**
     * @Route("/", methods={"POST"})
     */
    public function importAction()
    {
        $countSuccess = 0;
        $selectedCategoryId = (int) $this->request->getPost('categoryId', null, 0);

        if ($this->request->hasFiles(true)) {
            foreach ($this->request->getUploadedFiles() as $file) {
                if (!$this->checkFileType($file)) {
                    throw new UserException(ErrorCode::FILE_UPLOAD_ERR_ALLOWED_FORMAT);
                }

                try {
                    $handle = fopen($file->getTempName(), "r");
                    while (($line = fgets($handle)) !== false) {
                        // matching command name
                        preg_match('/^(?P<command>.*):###/', $line, $matches);
                        $scriptName = $matches['command'];

                        // matching text content
                        preg_match('/###(?P<text>.*)###/', $line, $matches);
                        $scriptContent = $matches['text'];

                        // Unique Script
                        $myVoiceScript = VoiceScriptModel::findFirst([
                            'command = :command:',
                            'bind' => [
                                'command' => (string) $scriptName
                            ]
                        ]);

                        if (!$myVoiceScript) {
                            $myVoiceScript = new VoiceScriptModel();
                            $myVoiceScript->assign([
                                'vscid' => (int) $selectedCategoryId,
                                'command' => (string) $scriptName,
                                'text' => (string) $scriptContent,
                                'status' => (int) VoiceScriptModel::STATUS_ENABLE
                            ]);

                            if ($myVoiceScript->create()) {
                                $countSuccess++;
                            } else {
                                throw new \Exception($myVoiceScript->getMessages());
                            }
                        }
                    }

                    fclose($handle);
                } catch (\Exception $e) {
                    throw new \Exception($e->getMessage());
                }
            }
        }

        return $this->respondWithArray([
            'scriptsImported' => $countSuccess
        ], 'data');
    }

    /**
     * Bulk action
     *
     * @Route("/bulk", methods={"POST"})
     */
    public function bulkAction()
    {
        $formData = (array) $this->request->getJsonRawBody();

        if (count($formData['itemSelected']) > 0 && $formData['actionSelected'] != '') {
            switch ($formData['actionSelected']) {
                case 'delete':
                    // Start a transaction
                    $this->db->begin();
                    foreach ($formData['itemSelected'] as $item) {
                        $myVoiceScript = VoiceScriptModel::findFirst([
                            'id = :id:',
                            'bind' => ['id' => (int) $item->id]
                        ])->delete();
                        // If fail stop a transaction
                        if ($myVoiceScript == false) {
                            $this->db->rollback();
                            return;
                        }
                    }
                    // Commit a transaction
                    if ($this->db->commit() == false) {
                        throw new UserException(ErrorCode::DATA_BULK_FAILED);
                    }

                    break;
                case 'disable':
                    $this->db->begin();
                    foreach ($formData['itemSelected'] as $item) {
                        $myVoiceScript = VoiceScriptModel::findFirst([
                            'id = :id:',
                            'bind' => ['id' => (int) $item->id]
                        ]);
                        $myVoiceScript->status = VoiceScriptModel::STATUS_DISABLE;

                        if (!$myVoiceScript->update()) {
                            $this->db->rollback();
                            return;
                        }
                    }

                    if ($this->db->commit() == false) {
                        throw new UserException(ErrorCode::DATA_BULK_FAILED);
                    }

                    break;
            }
        }

        return $this->respondWithOK();
    }

    /**
     * @Route("/", methods={"DELETE"})
     */
    public function deleteAction()
    {
        $formData = (array) $this->request->getJsonRawBody();

        $myVoiceScript = VoiceScriptModel::findFirst([
            'id = :id:',
            'bind' => [
                'id' => (int) $formData['id']
            ]
        ]);

        if (!$myVoiceScript) {
            throw new UserException(ErrorCode::DATA_NOTFOUND);
        }

        if (!$myVoiceScript->delete()) {
            throw new UserException(ErrorCode::DATA_DELETE_FAIL);
        }

        return $this->createItem(
            $myVoiceScript,
            new VoiceScriptTransformer,
            'data'
        );
    }

    /**
     * @Route("/formsource", methods={"GET"})
     */
    public function formsourceAction()
    {
        return $this->respondWithArray([
            'statusList' => VoiceScriptModel::getStatusList(),
            'voicescriptcategoryList' => VoiceScriptCategoryModel::find()
        ], 'data');
    }

    private function checkFileType($file)
    {
        $pass = true;

        if (!in_array($file->getExtension(), $this->allowedFormat)) {
            $pass = false;
        }

        return $pass;
    }
}
