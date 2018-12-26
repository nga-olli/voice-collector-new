<?php
namespace Reward\Controller\V1;

use Shirou\Constants\ErrorCode;
use Shirou\UserException;
use Core\Controller\AbstractController;
use Core\Helper\Utils as Helper;
use Reward\Model\GiftType as GiftTypeModel;
use Reward\Model\GiftAttribute as GiftAttributeModel;
use Reward\Transformer\GiftType as GiftTypeTransformer;
use Reward\Transformer\GiftAttribute as GiftAttributeTransformer;

/**
 * @RoutePrefix("/v1/rewardtypes")
 */
class TypeController extends AbstractController
{
    protected $recordPerPage = 30;

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
        $rcid = (int) $this->request->getQuery('rcid', null, 0);

        $formData['columns'] = '*';
        $formData['conditions'] = [
            'keyword' => $keyword,
            'searchKeywordIn' => $searchKeywordInData,
            'filterBy' => [
                'status' => $status,
                'rcid' => $rcid
            ]
        ];
        $formData['orderBy'] = $orderBy;
        $formData['orderType'] = $orderType;

        $myGifttypes = GiftTypeModel::paginate($formData, $this->recordPerPage, $page);

        if ($myGifttypes->total_pages > 0) {
            if ($page == $myGifttypes->total_pages) {
                $hasMore = false;
            }

            return $this->createCollection(
                $myGifttypes->items,
                new GiftTypeTransformer,
                'data',
                [
                    'meta' => [
                        'recordPerPage' => $this->recordPerPage,
                        'hasMore' => $hasMore,
                        'totalItems' => $myGifttypes->total_items,
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
     * @Route("/", methods={"POST"})
     */
    public function addAction()
    {
        $postForm = (array) $this->request->getPost('form');
        $formData = (array) json_decode($postForm[0]);

        $myGiftType = new GiftTypeModel();
        $myGiftType->name = $formData['name'];
        $myGiftType->rcid = (int) $formData['category'];
        $myGiftType->description = $formData['description'];
        $myGiftType->cost = (int) $formData['cost'];
        $myGiftType->lowstockthreshold = (int) $formData['lowstockthreshold'];
        $myGiftType->status = (int) $formData['cost'];
        $myGiftType->deliverytype = (int) $formData['delivery'];

        if (!$myGiftType->create()) {
            throw new UserException(ErrorCode::DATA_CREATE_FAIL);
        }

        if (count($formData['attrs']) > 0) {
            foreach ($formData['attrs'] as $attr) {
                $myGiftAttribute = new GiftAttributeModel();
                $myGiftAttribute->assign([
                    'gtid' => (int) $myGiftType->id,
                    'name' => (string) $attr->name,
                    'unit' => (string) $attr->unit,
                    'displayorder' => (int) $attr->order,
                    'displaytype' => (int) $attr->displaytype,
                    'type' => (int) $attr->type,
                    'iscritical' => (int) $attr->critical
                ]);

                if (!$myGiftAttribute->create()) {
                    throw new UserException(ErrorCode::DATA_CREATE_FAIL);
                }
            }
        }

        return $this->createItem(
            $myGiftType,
            new GiftTypeTransformer,
            'data'
        );
    }

    /**
     * @Route("/{id:[0-9]+}", methods={"POST"})
     */
    public function updateAction(int $id = 0)
    {
        $myGiftType = GiftTypeModel::findFirst([
            'id = :id:',
            'bind' => [
                'id' => (int) $id
            ]
        ]);

        if (!$myGiftType) {
            throw new UserException(ErrorCode::DATA_NOTFOUND);
        }

        $postForm = (array) $this->request->getPost('form');
        $formData = (array) json_decode($postForm[0]);

        $myGiftType->name = $formData['name'];
        $myGiftType->rcid = $formData['category'];
        $myGiftType->status = $formData['status'];
        $myGiftType->description = $formData['description'];
        $myGiftType->cost = $formData['cost'];
        $myGiftType->lowstockthreshold = $formData['lowstockthreshold'];
        $myGiftType->deliverytype = (int) $formData['delivery'];
        $myGiftType->lowstockthreshold = (int) $formData['lowstockthreshold'];

        if (!$myGiftType->update()) {
            throw new UserException(ErrorCode::DATA_CREATE_FAIL);
        }

        if (count($formData['attrs']) > 0) {
            foreach ($formData['attrs'] as $attr) {
                if (is_string($attr->key) && $attr->isdel === false) {
                    $myGiftAttribute = GiftAttributeModel::findFirst([
                        'id = :id:',
                        'bind' => [
                            'id' => (int) $attr->key
                        ]
                    ]);

                    if (!$myGiftAttribute) {
                        throw new UserException(ErrorCode::DATA_NOTFOUND);
                    }
                } else {
                    $myGiftAttribute = new GiftAttributeModel();
                }
                
                $myGiftAttribute->assign([
                    'gtid' => (int) $myGiftType->id,
                    'name' => (string) $attr->name,
                    'unit' => (string) $attr->unit,
                    'displayorder' => (int) $attr->order,
                    'displaytype' => (int) $attr->displaytype,
                    'type' => (int) $attr->type,
                    'iscritical' => (int) $attr->critical
                ]);

                if (!$myGiftAttribute->save()) {
                    throw new UserException(ErrorCode::DATA_UPDATE_FAIL);
                }
            }
        }

        return $this->createItem(
            $myGiftType,
            new GiftTypeTransformer,
            'data'
        );
    }

    /**
     * Get single reward type
     *
     * @Route("/{id:[0-9]+}", methods={"GET"})
     */
    public function getAction(int $id = 0)
    {
        $myGiftType = GiftTypeModel::findFirst([
            'id = :id:',
            'bind' => [
                'id' => (int) $id
            ]
        ]);

        if (!$myGiftType) {
            throw new UserException(ErrorCode::DATA_NOTFOUND);
        }

        return $this->createItem(
            $myGiftType,
            new GiftTypeTransformer,
            'data'
        );
    }

    /**
     * @Route("/{id:[0-9]+}/attrs", methods={"GET"})
     */
    public function getattrsAction(int $id = 0)
    {
        $myGiftType = GiftTypeModel::findFirst([
            'id = :id:',
            'bind' => [
                'id' => (int) $id
            ]
        ]);

        $myGiftAttributes = $myGiftType->getAttributes([
            'order' => 'displayorder ASC'
        ]);

        if (count($myGiftAttributes) > 0) {
            return $this->createCollection(
                $myGiftAttributes,
                new GiftAttributeTransformer,
                'data'
            );
        } else {
            return $this->respondWithArray([], 'data');
        }
    }

    /**
     * Update single field
     *
     * @Route("/{id:[0-9]+}/field", methods={"PUT"})
     */
    public function updatefieldAction(int $id = 0)
    {
        $formData = (array) $this->request->getJsonRawBody();

        $myGiftType = GiftTypeModel::findFirst([
            'id = :id:',
            'bind' => ['id' => (int) $id]
        ]);

        if (!$myGiftType) {
            throw new UserException(ErrorCode::DATA_NOTFOUND);
        }

        $myGiftType->{$formData['field']} = $formData['value'];

        if (!$myGiftType->update()) {
            throw new UserException(ErrorCode::DATA_UPDATE_FAIL);
        }

        return $this->createItem(
            $myGiftType,
            new GiftTypeTransformer,
            'data'
        );
    }

    /**
     * Delete
     *
     * @Route("/{id:[0-9]+}", methods={"DELETE"})
     */
    public function deleteAction(int $id = 0)
    {
        $formData = (array) $this->request->getJsonRawBody();

        $myGiftType = GiftTypeModel::findFirst([
            'id = :id:',
            'bind' => [
                'id' => (int) $id
            ]
        ]);

        if (!$myGiftType) {
            throw new UserException(ErrorCode::DATA_NOTFOUND);
        }

        if (!$myGiftType->delete()) {
            throw new UserException(ErrorCode::DATA_DELETE_FAIL);
        }

        return $this->createItem(
            $myGiftType,
            new GiftTypeTransformer,
            'data'
        );
    }


    /**
     * @Route("/formsource", methods={"GET"})
     */
    public function formsourceAction()
    {
        return $this->respondWithArray([
            'statusList' => GiftTypeModel::getStatusList()
        ], 'data');
    }

    // /**
    //  * Change status
    //  *
    //  * @Route("/{id:[0-9]+}/status", methods={"PUT"})
    //  */
    // public function changestatusAction(int $id = 0)
    // {
    //     $formData = (array) $this->request->getJsonRawBody();

    //     $myGiftType = GiftTypeModel::findFirst([
    //         'id = :id:',
    //         'bind' => ['id' => (int) $id]
    //     ]);

    //     if (!$myGiftType) {
    //         throw new UserException(ErrorCode::DATA_NOTFOUND);
    //     }

    //     $status = GiftTypeModel::STATUS_DISABLE;
    //     switch ($formData['value']) {
    //         case true:
    //             $status = GiftTypeModel::STATUS_ENABLE;
    //             break;
    //         case true:
    //             $status = GiftTypeModel::STATUS_DISABLE;
    //             break;
    //     }

    //     $myGiftType->status = (int) $status;

    //     if (!$myGiftType->update()) {
    //         throw new UserException(ErrorCode::DATA_UPDATE_FAIL);
    //     }

    //     return $this->createItem(
    //         $myGiftType,
    //         new GiftTypeTransformer,
    //         'data'
    //     );
    // }

    // /**
    //  * Update single attr field
    //  *
    //  * @Route("/{id:[0-9]+}/attr_field", methods={"PUT"})
    //  */
    // public function updateattrfieldAction(int $id = 0)
    // {
    //     $formData = (array) $this->request->getJsonRawBody();

    //     $myGiftAttr = GiftAttributeModel::findFirst([
    //         'id = :id:',
    //         'bind' => ['id' => (int) $id]
    //     ]);

    //     if (!$myGiftAttr) {
    //         throw new UserException(ErrorCode::DATA_NOTFOUND);
    //     }

    //     $myGiftAttr->{$formData['field']} = $formData['value'];

    //     if (!$myGiftAttr->update()) {
    //         throw new UserException(ErrorCode::DATA_UPDATE_FAIL);
    //     }

    //     return $this->createItem(
    //         $myGiftAttr,
    //         new GiftAttributeTransformer,
    //         'data'
    //     );
    // }

    // /**
    //  * Delete
    //  *
    //  * @Route("/{id:[0-9]+}/attr", methods={"DELETE"})
    //  */
    // public function deleteattrAction(int $id = 0)
    // {
    //     $formData = (array) $this->request->getJsonRawBody();

    //     $myGiftAttr = GiftAttributeModel::findFirst([
    //         'id = :id:',
    //         'bind' => [
    //             'id' => (int) $id
    //         ]
    //     ]);

    //     if (!$myGiftAttr) {
    //         throw new UserException(ErrorCode::DATA_NOTFOUND);
    //     }

    //     if (!$myGiftAttr->delete()) {
    //         throw new UserException(ErrorCode::DATA_DELETE_FAIL);
    //     }

    //     return $this->createItem(
    //         $myGiftAttr,
    //         new GiftAttributeTransformer,
    //         'data'
    //     );
    // }
}
