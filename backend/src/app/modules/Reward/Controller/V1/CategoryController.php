<?php
namespace Reward\Controller\V1;

use Shirou\Constants\ErrorCode;
use Shirou\UserException;
use Core\Controller\AbstractController;
use Reward\{
    Model\Category as RewardCategoryModel,
    Transformer\Category as RewardCategoryTransformer,
    Transformer\Closure as RewardCategoryClosureTransformer
};

/**
 * @RoutePrefix("/v1/rewards/category")
 */
class CategoryController extends AbstractController
{
    protected $recordPerPage = 50;

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
            'name',
            'description'
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

        $myRewardCategories = RewardCategoryModel::paginate($formData, $this->recordPerPage, $page);

        if ($myRewardCategories->total_pages > 0) {
            if ($page == $myRewardCategories->total_pages) {
                $hasMore = false;
            }

            return $this->createCollection(
                $myRewardCategories->items,
                new RewardCategoryTransformer,
                'data',
                [
                    'meta' => [
                        'recordPerPage' => $this->recordPerPage,
                        'hasMore' => $hasMore,
                        'totalItems' => $myRewardCategories->total_items,
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
        $myRewardCategory = RewardCategoryModel::findFirst([
            'id = :id:',
            'bind' => ['id' => (int) $id]
        ]);

        if (!$myRewardCategory) {
            throw new UserException(ErrorCode::DATA_NOTFOUND);
        }

        return $this->createItem(
            $myRewardCategory,
            new RewardCategoryTransformer,
            'data'
        );
    }

    /**
     * @Route("/", methods={"POST"})
     */
    public function addAction()
    {
        $postForm = (array) $this->request->getPost('form');
        $formData = (array) json_decode($postForm[0]);

        $myRewardCategory = new RewardCategoryModel();
        $myRewardCategory->name = (string) $formData['name'];
        $myRewardCategory->description = (string) $formData['description'];
        $myRewardCategory->displayorder = (int) $formData['displayorder'];
        $myRewardCategory->status = (int) $formData['status'];
        $myRewardCategory->parentid = (int) $formData['parentid'];

        if (!$myRewardCategory->create()) {
            throw new UserException(ErrorCode::DATA_CREATE_FAIL);
        }

        return $this->createItem(
            $myRewardCategory,
            new RewardCategoryTransformer,
            'response'
        );
    }

    /**
     * @Route("/{id:[0-9]+}", methods={"POST"})
     */
    public function updateAction(int $id = 0)
    {
        $postForm = (array) $this->request->getPost('form');
        $formData = (array) json_decode($postForm[0]);

        $myRewardCategory = RewardCategoryModel::findFirst([
            'id = :id:',
            'bind' => ['id' => (int) $id]
        ]);

        if (!$myRewardCategory) {
            throw new UserException(ErrorCode::DATA_NOTFOUND);
        }

        $myRewardCategory->name = (string) $formData['name'];
        $myRewardCategory->description = (string) $formData['description'];
        $myRewardCategory->displayorder = (int) $formData['displayorder'];
        $myRewardCategory->status = (int) $formData['status'];
        $myRewardCategory->parentid = (int) $formData['parentid'];

        if (!$myRewardCategory->update()) {
            throw new UserException(ErrorCode::USER_UPDATE_FAIL);
        }

        return $this->createItem(
            $myRewardCategory,
            new RewardCategoryTransformer,
            'data'
        );
    }

    /**
     * @Route("/{id:[0-9]+}", methods={"DELETE"})
     */
    public function deleteAction(int $id = 0)
    {
        $myRewardCategory = RewardCategoryModel::findFirst([
            'id = :id:',
            'bind' => [
                'id' => (int) $id
            ]
        ]);

        if (!$myRewardCategory) {
            throw new UserException(ErrorCode::DATA_NOTFOUND);
        }

        if (!$myRewardCategory->delete()) {
            throw new UserException(ErrorCode::DATA_DELETE_FAIL);
        }

        return $this->createItem(
            $myRewardCategory,
            new RewardCategoryTransformer,
            'data'
        );
    }

    /**
     * @Route("/formsource", methods={"GET"})
     */
    public function formsourceAction()
    {
        return $this->respondWithArray([
            'statusList' => RewardCategoryModel::getStatusList(),
            'categoryList' => RewardCategoryModel::find()
        ], 'data');
    }

    /**
     * @Route("/closure", methods={"GET"})
     */
    public function closureAction()
    {
        $allCategories = RewardCategoryModel::getFullParentProductCategorys();

        return $this->createCollection(
            $allCategories,
            new RewardCategoryClosureTransformer,
            'data'
        );
    }
}
