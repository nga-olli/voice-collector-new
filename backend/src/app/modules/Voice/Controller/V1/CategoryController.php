<?php
namespace Voice\Controller\V1;

use Shirou\Constants\ErrorCode;
use Shirou\UserException;
use Core\Controller\AbstractController;
use Voice\{
    Model\ScriptCategory as VoiceScriptCategoryModel,
    Transformer\ScriptCategory as VoiceScriptCategoryTransformer
};

/**
 * @RoutePrefix("/v1/voice/scriptcategories")
 */
class CategoryController extends AbstractController
{
    protected $recordPerPage = 100;

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
            'name'
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
                'status' => $status,
            ]
        ];
        $formData['orderBy'] = $orderBy;
        $formData['orderType'] = $orderType;

        $myVoiceScriptCategories = VoiceScriptCategoryModel::paginate($formData, $this->recordPerPage, $page);

        if ($myVoiceScriptCategories->total_pages > 0) {
            if ($page == $myVoiceScriptCategories->total_pages) {
                $hasMore = false;
            }

            return $this->createCollection(
                $myVoiceScriptCategories->items,
                new VoiceScriptCategoryTransformer,
                'data',
                [
                    'meta' => [
                        'recordPerPage' => $this->recordPerPage,
                        'hasMore' => $hasMore,
                        'totalItems' => $myVoiceScriptCategories->total_items,
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
        $myVoiceScriptCategory = VoiceScriptCategoryModel::findFirst([
            'id = :id:',
            'bind' => ['id' => (int) $id]
        ]);

        if (!$myVoiceScriptCategory) {
            throw new UserException(ErrorCode::DATA_NOTFOUND);
        }

        return $this->createItem(
            $myVoiceScriptCategory,
            new VoiceScriptCategoryTransformer,
            'data'
        );
    }

    /**
     * @Route("/", methods={"POST"})
     */
    public function addAction()
    {
        $formData = (array) $this->request->getJsonRawBody();

        $myVoiceScriptCategory = new VoiceScriptCategoryModel();
        $myVoiceScriptCategory->assign([
            'name' => (string) $formData['name'],
            'displayorder' => (int) $formData['displayorder'],
            'status' => (int) $formData['status']
        ]);
        
        if (!$myVoiceScriptCategory->create()) {
            throw new UserException(ErrorCode::DATA_CREATE_FAIL);
        }

        return $this->createItem(
            $myVoiceScriptCategory,
            new VoiceScriptCategoryTransformer,
            'response'
        );
    }

    /**
     * @Route("/{id:[0-9]+}", methods={"PUT"})
     */
    public function updateAction(int $id = 0)
    {
        $formData = (array) $this->request->getJsonRawBody();

        $myVoiceScriptCategory = VoiceScriptCategoryModel::findFirst([
            'id = :id:',
            'bind' => ['id' => (int) $id]
        ]);

        if (!$myVoiceScriptCategory) {
            throw new UserException(ErrorCode::DATA_NOTFOUND);
        }

        $myVoiceScriptCategory->name = (string) $formData['name'];
        $myVoiceScriptCategory->displayorder = (int) $formData['displayorder'];
        $myVoiceScriptCategory->status = (int) $formData['status'];

        if (!$myVoiceScriptCategory->update()) {
            throw new UserException(ErrorCode::USER_UPDATE_FAIL);
        }

        return $this->createItem(
            $myVoiceScriptCategory,
            new VoiceScriptCategoryTransformer,
            'data'
        );
    }

    /**
     * @Route("/{id:[0-9]+}", methods={"DELETE"})
     */
    public function deleteAction(int $id = 0)
    {
        $myVoiceScriptCategory = VoiceScriptCategoryModel::findFirst([
            'id = :id:',
            'bind' => [
                'id' => (int) $id
            ]
        ]);

        if (!$myVoiceScriptCategory) {
            throw new UserException(ErrorCode::DATA_NOTFOUND);
        }

        if (!$myVoiceScriptCategory->delete()) {
            throw new UserException(ErrorCode::DATA_DELETE_FAIL);
        }

        return $this->createItem(
            $myVoiceScriptCategory,
            new VoiceScriptCategoryTransformer,
            'data'
        );
    }

    /**
     * @Route("/formsource", methods={"GET"})
     */
    public function formsourceAction()
    {
        return $this->respondWithArray([
            'statusList' => VoiceScriptCategoryModel::getStatusList(),
            'categoryList' => VoiceScriptCategoryModel::find()
        ], 'data');
    }
}
