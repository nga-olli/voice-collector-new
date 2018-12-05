<?php
namespace Job\Controller\V1;

use Shirou\Constants\ErrorCode;
use Shirou\UserException;
use Core\Controller\AbstractController;
use Job\{
    Model\Job as JobModel,
    Transformer\Job as JobTransformer
};

/**
 * @RoutePrefix("/v1/jobs")
 */
class IndexController extends AbstractController
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
        // $status = (int) $this->request->getQuery('status', null, 0);

        $formData['columns'] = '*';
        $formData['conditions'] = [
            'keyword' => $keyword,
            'searchKeywordIn' => $searchKeywordInData,
            'filterBy' => [
                // 'status' => $status,
            ]
        ];
        $formData['orderBy'] = $orderBy;
        $formData['orderType'] = $orderType;

        $myJobs = JobModel::paginate($formData, $this->recordPerPage, $page);

        if ($myJobs->total_pages > 0) {
            if ($page == $myJobs->total_pages) {
                $hasMore = false;
            }

            return $this->createCollection(
                $myJobs->items,
                new JobTransformer,
                'data',
                [
                    'meta' => [
                        'recordPerPage' => $this->recordPerPage,
                        'hasMore' => $hasMore,
                        'totalItems' => $myJobs->total_items,
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

    // /**
    //  * @Route("/{id:[0-9]+}", methods={"GET"})
    //  */
    // public function getAction(int $id = 0)
    // {
    //     $myUser = UserModel::findFirst([
    //         'id = :id:',
    //         'bind' => ['id' => (int) $id]
    //     ]);

    //     if (!$myUser) {
    //         throw new UserException(ErrorCode::DATA_NOTFOUND);
    //     }

    //     return $this->createItem(
    //         $myUser,
    //         new UserTransformer,
    //         'data'
    //     );
    // }

    // /**
    //  * @Route("/", methods={"POST"})
    //  */
    // public function addAction()
    // {
    //     $formData = (array) $this->request->getJsonRawBody();

    //     $myUser = new UserModel();
    //     $myUser->email = (string) $formData['email'];
    //     $myUser->fullname = (string) $formData['fullname'];
    //     $myUser->groupid = (string) $formData['groupid'];
    //     $myUser->password = (string) $this->security->hash($formData['password']);
    //     $myUser->status = (int) $formData['status'];
    //     $myUser->verifytype = (int) $formData['verifytype'];
    //     $myUser->isverified = UserModel::IS_VERIFIED;

    //     if (!$myUser->create()) {
    //         throw new UserException(ErrorCode::USER_CREATE_FAIL);
    //     }

    //     return $this->createItem(
    //         $myUser,
    //         new UserTransformer,
    //         'response'
    //     );
    // }

    // /**
    //  * @Route("/{id:[0-9]+}", methods={"PUT"})
    //  */
    // public function updateAction(int $id = 0)
    // {
    //     $formData = (array) $this->request->getJsonRawBody();

    //     $myUser = UserModel::findFirst([
    //         'id = :id:',
    //         'bind' => ['id' => (int) $id]
    //     ]);

    //     if (!$myUser) {
    //         throw new UserException(ErrorCode::DATA_NOTFOUND);
    //     }

    //     $myUser->fullname = (string) $formData['fullname'];
    //     $myUser->groupid = (string) $formData['groupid'];
    //     $myUser->status = (int) $formData['status'];
    //     $myUser->verifytype = (int) $formData['verifytype'];

    //     if (!$myUser->update()) {
    //         throw new UserException(ErrorCode::USER_UPDATE_FAIL);
    //     }

    //     return $this->createItem(
    //         $myUser,
    //         new UserTransformer,
    //         'data'
    //     );
    // }

    // /**
    //  * @Route("/bulk", methods={"POST"})
    //  */
    // public function bulkAction()
    // {
    //     $formData = (array) $this->request->getJsonRawBody();

    //     if (count($formData['itemSelected']) > 0 && $formData['actionSelected'] != '') {
    //         switch ($formData['actionSelected']) {
    //             case 'delete':
    //                 // Start a transaction
    //                 $this->db->begin();
    //                 foreach ($formData['itemSelected'] as $item) {
    //                     $myUser = UserModel::findFirst([
    //                         'id = :id:',
    //                         'bind' => ['id' => (int) $item->id]
    //                     ])->delete();
    //                     // If fail stop a transaction
    //                     if ($myUser == false) {
    //                         $this->db->rollback();
    //                         return;
    //                     }
    //                 }
    //                 // Commit a transaction
    //                 if ($this->db->commit() == false) {
    //                     throw new UserException(ErrorCode::DATA_BULK_FAILED);
    //                 }

    //                 break;
    //             case 'enable':
    //                 $this->db->begin();
    //                 foreach ($formData['itemSelected'] as $item) {
    //                     $myUser = UserModel::findFirst([
    //                         'id = :id:',
    //                         'bind' => ['id' => (int) $item->id]
    //                     ]);
    //                     $myUser->status = UserModel::STATUS_ENABLE;

    //                     if (!$myUser->update()) {
    //                         $this->db->rollback();
    //                         return;
    //                     }
    //                 }

    //                 if ($this->db->commit() == false) {
    //                     throw new UserException(ErrorCode::DATA_BULK_FAILED);
    //                 }

    //                 break;
    //             case 'disable':
    //                 $this->db->begin();
    //                 foreach ($formData['itemSelected'] as $item) {
    //                     $myUser = UserModel::findFirst([
    //                         'id = :id:',
    //                         'bind' => ['id' => (int) $item->id]
    //                     ]);
    //                     $myUser->status = UserModel::STATUS_DISABLE;

    //                     if (!$myUser->update()) {
    //                         $this->db->rollback();
    //                         return;
    //                     }
    //                 }

    //                 if ($this->db->commit() == false) {
    //                     throw new UserException(ErrorCode::DATA_BULK_FAILED);
    //                 }

    //                 break;
    //         }
    //     }

    //     return $this->respondWithOK();
    // }

    // /**
    //  * @Route("/{id:[0-9]+}", methods={"DELETE"})
    //  */
    // public function deleteAction(int $id = 0)
    // {
    //     $myUser = UserModel::findFirst([
    //         'id = :id:',
    //         'bind' => [
    //             'id' => (int) $id
    //         ]
    //     ]);

    //     if (!$myUser) {
    //         throw new UserException(ErrorCode::DATA_NOTFOUND);
    //     }

    //     if (!$myUser->delete()) {
    //         throw new UserException(ErrorCode::DATA_DELETE_FAIL);
    //     }

    //     return $this->createItem(
    //         $myUser,
    //         new UserTransformer,
    //         'data'
    //     );
    // }

    // /**
    //  * @Route("/formsource", methods={"GET"})
    //  */
    // public function formsourceAction()
    // {
    //     return $this->respondWithArray([
    //         'groupList' => UserModel::getGroupList(),
    //         'statusList' => UserModel::getStatusList(),
    //         'verifyList' => UserModel::getVerifyList(),
    //     ], 'data');
    // }
}