<?php
use Phalcon\Http\Request;
use App\Forms\CreateNewsForm;

class NewsController extends ControllerBase
{


    public $createNewsForm;
    public $newsModel;

    public function onConstruct() {}

    public function indexAction(){
      $news = News::find([
          'conditions' => 'status = ?1',
          'bind'       => [
              1 => 0,
          ],
          'limit' => 5,
          'order' => 'id DESC'
      ]);

      $this->view->newsData = $news;
    }
    public function detailAction($newsId = null){
      $url_id = urldecode(strtr($newsId,"'",'%'));
      $newsId = $this->crypt->decryptBase64($url_id);
      $news = News::findFirst([
          'conditions' => 'id = ?1',
          'bind'       => [
              1 => $newsId,
          ],
      ]);
      $this->view->newsData = $news;
    }

    public function archiveAction(){
      $news = News::find([
          'conditions' => 'status = ?1',
          'bind'       => [
              1 => 0,
          ],
          'order' => 'id DESC'
      ]);

      $this->view->newsData = $news;
    }
    public function initialize()
    {
        $this->authorized();
        $this->createNewsForm = new CreateNewsForm();
        $this->newsModel = new News();
    }
    public function createAction()
    {
        $this->view->form = new CreateNewsForm();
    }

    public function createSubmitAction()
    {
        if (!$this->request->isPost()) {
            return $this->response->redirect('admin/login');
        }
        if (!$this->security->checkToken()) {
            $this->flashSession->error("Invalid Token");
            return $this->response->redirect('news/create');
        }
        $this->createNewsForm->bind($_POST, $this->newsModel);
        if (!$this->createNewsForm->isValid()) {
            foreach ($this->createNewsForm->getMessages() as $message) {
                $this->flashSession->error($message);
                $this->dispatcher->forward([
                    'controller' => $this->router->getControllerName(),
                    'action'     => 'create',
                ]);
                return;
            }
        }
        $this->newsModel->setStatus(0);
        $t = time();
        $this->newsModel->setCreatedAt(date("Y-m-d H:i:s",$t));
        $this->newsModel->setUpdatedAt(date("Y-m-d H:i:s",$t));
        if (!$this->newsModel->save()) {
            foreach ($this->newsModel->getMessages() as $m) {
                $this->flashSession->error($m);
                $this->dispatcher->forward([
                    'controller' => $this->router->getControllerName(),
                    'action'     => 'create',
                ]);
                return;
            }
        }
        $this->flashSession->success('News successfully saved.');
        return $this->response->redirect('news/create');
        $this->view->disable();
    }

    public function manageAction()
    {
        $news = News::find([
            'conditions' => 'status = ?1',
            'bind'       => [
                1 => 0,
            ],
        ]);

        $this->view->newsData = $news;
    }

    public function editAction($newsId = null)
    {
        $url_id = urldecode(strtr($newsId,"'",'%'));
        $newsId = $this->crypt->decryptBase64($url_id);

        if (!empty($newsId) AND $newsId != null)
        {
            // Check Post Request
            if($this->request->isPost())
            {
                # bind user type data
                $this->createNewsForm->bind($this->request->getPost(), $this->newsModel);
                $this->view->form = new CreateNewsForm($this->newsModel, [
                    "edit" => true
                ]);

            } else
            {
                // Fetch User News
                $news = News::findFirst([
                    'conditions' => 'id = :1: AND status = :2:',
                    'bind' => [
                        '1' => $newsId,
                        '2' => 0
                    ]
                ]);

                if (!$news) {
                    $this->flashSession->error('News was not found');
                    return $this->response->redirect('news/create');
                }
                // Send News Data in News Form
                $this->view->form = new CreateNewsForm($news, [
                    "edit" => true
                ]);
            }
        } else {
            return $this->response->redirect('news/manage');
        }
    }

    public function editSubmitAction()
    {
        // check post request
        if (!$this->request->isPost()) {
            return $this->response->redirect('news/manage');
        }
        // Validate CSRT Token
        if (!$this->security->checkToken()) {
            $this->flashSession->error("Invalid Token");
            return $this->response->redirect('news/manage');
        }
        // get news id
        $newsEID = $this->request->getPost("eid");
        /**
         * Decode News Eid
         */
        $newsID = $this->crypt->decryptBase64(urldecode(strtr($newsEID,"'",'%')));
        // Check Agin User News is Valid
        $news = News::findFirst([
            'conditions' => 'id = :1: AND status = :2:',
            'bind' => [
                '1' => $newsID,
                '2' => 0
            ]
        ]);
        if (!$news) {
            $this->flashSession->error('News was not found');
            return $this->response->redirect('news/create');
        }
        # Check Form Validation
        if (!$this->createNewsForm->isValid($this->request->getPost(), $news)) {
            foreach ($this->createNewsForm->getMessages() as $message) {
                $this->flashSession->error($message);
                return $this->dispatcher->forward([
                    'controller' => $this->router->getControllerName(),
                    'action'     => 'edit',
                    'params' => [$newsID]
                ]);
            }
        }

        $this->newsModel->setId($newsID);
        $this->newsModel->setUpdatedAt(date("Y-m-d H:i:s",time()));
        $this->newsModel->setStatus($news->status);
        $this->newsModel->setCreatedAt($news->created_at);
        # Doc :: https://docs.phalconphp.com/en/3.3/db-models#create-update-records
        if ($this->newsModel->save($_POST) === false) {
            foreach ($this->newsModel->getMessages() as $m) {
                $this->flashSession->error($m);
            }
            return $this->dispatcher->forward([
                'controller' => $this->router->getControllerName(),
                'action'     => 'edit',
            ]);
        }
        // Clear News Form
        $this->createNewsForm->clear();
        $this->flashSession->success('News was updated successfully.');
        return $this->response->redirect('news/manage');
        $this->view->disable();
    }
    /**
     * Delete News
     */
    public function deleteAction($newsEID)
    {
        /**
         * Decode News EID
         * ----------------------------------------------------
         * http://php.net/manual/en/function.urlencode.php
         */
        $newsID = $this->crypt->decryptBase64(urldecode(strtr($newsEID,"'",'%')));
        $id = (int) $newsID;
        if ($id > 0 AND !empty($id))
        {
            // Check Agin User News is Valid
            $news = News::findFirst([
                'conditions' => 'id = :1: AND status = :2:',
                'bind' => [
                    '1' => $id,
                    '2' => 0
                ]
            ]);
            if (!$news) {
                $this->flashSession->error('News was not found');
                return $this->response->redirect('news/manage');
            }
            if (!$news->delete()) {
                foreach ($news->getMessages() as $msg) {
                    $this->flashSession->error((string) $msg);
                }
                return $this->response->redirect("news/manage");
            } else {
                $this->flashSession->success("News was deleted");
                return $this->response->redirect("news/manage");
            }
        } else {
            $this->flashSession->error("News ID Invalid.");
            return $this->response->redirect("news/manage");
        }
        # View Page Disable
        $this->view->disable();
    }
  }
