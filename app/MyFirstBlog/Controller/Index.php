<?php
class MyFirstBlog_Controller_Index extends Pimf_Controller_Abstract
{
  /**
   * @param Pimf_View $view
   * @return string
   */
  protected function loadMainView(Pimf_View $view)
  {
    $viewMain = new Pimf_View();
    $viewMain->setTemplate('theblog');
    $viewMain->assign('blog_title', 'This is my firs Blog with PIMF');
    $viewMain->assign('blog_content', $view->render());
    $viewMain->assign('blog_footer', 'A Blog about cool and thin framework');

    return $viewMain->render();
  }

  public function indexAction()
  {
    $viewAllEntries = new Pimf_View();

    $registry = new Pimf_Registry();
    $entries  = $registry->em->entry->getAll();

    $viewAllEntries->setTemplate('default');
    $viewAllEntries->assign('entries', $entries);

    echo $this->loadMainView($viewAllEntries);
  }

  public function showentryAction()
  {
    $validator = new Pimf_Util_Validator($this->request->fromGet());

    if (!$validator->digit('id') || !$validator->value('id', '>', 0)) {
      throw new Pimf_Controller_Exception('not valid entry');
    }

    $viewSingleEntry = new Pimf_View();
    $viewSingleEntry->setTemplate('entry');

    $registry = new Pimf_Registry();
    $entry    = $registry->em->entry->find(
      $this->request->fromGet()->get('id')
    );

    $viewSingleEntry->assign('title', $entry->getTitle());
    $viewSingleEntry->assign('content', $entry->getContent());
    $viewSingleEntry->assign('back_link_title', 'Back to overview');

    echo $this->loadMainView($viewSingleEntry);
  }

  public function entryasjsonAction()
  {
    $registry = new Pimf_Registry();

    $entry = $registry->em->entry->find(
      $this->request->fromGet()->get('id')
    );

    Pimf_Util_Header::clear();
    Pimf_Util_Header::contentTypeJson();

    echo Pimf_Util_Json::encode($entry->getArrayCopy());
  }

  /**
   * @argument string [title]
   * @argument string [content]
   */
  public function insertCliAction()
  {
    $validator = new Pimf_Util_Validator($this->request->fromCli());

    if (!$validator->length('title', '>', 0) || !$validator->length('content', '>', 0)) {
      throw new Pimf_Controller_Exception('not valid entry');
    }

    $registry = new Pimf_Registry();
    $entry    = new MyFirstBlog_Model_Entry();

    $entry->setTitle($this->request->fromCli()->get('title'));
    $entry->setContent($this->request->fromCli()->get('content'));

    $res = $registry->em->entry->insert($entry);

    var_dump($res);
  }

  /**
   * @argument string [title]
   * @argument string [content]
   */
  public function updateCliAction()
  {
    $validator = new Pimf_Util_Validator($this->request->fromGet());

    if (!$validator->length('title', '>', 0) || !$validator->length('content', '>', 0)) {
      throw new Pimf_Controller_Exception('not valid entry');
    }

    $registry = new Pimf_Registry();
    $entry    = new MyFirstBlog_Model_Entry();

    $entry->setTitle($this->request->fromCli()->get('title'));
    $entry->setContent($this->request->fromCli()->get('content'));

    $entry = $registry->em->entry->reflectId(
      $entry, $this->request->fromCli()->get('id')
    );

    $res = $registry->em->entry->update($entry);

    var_dump($res);
  }

  public function createtableCliAction()
  {
    $registry = new Pimf_Registry();

    try {

      $res = $registry->em->getPDO()->exec(
        file_get_contents(
          dirname(dirname(__FILE__)) .'/_database/create-table.sql'
        )
      );

      var_dump($res);

    } catch (PDOException $e) {
      throw new Pimf_Controller_Exception($e->getMessage());
    }
  }
}